var app = {
    connection            : false,
    user                  : false,
    users                 : [],
    messages              : [],
    timeLoop              : false,
    messageListAutoScroll : true, //auto scroll the message list
    baseURL               : '',
    notifications         : [],
    visible               : true,

    init: function (serverAddress, baseURL)
    {
        app.baseURL = baseURL;

        if ($.browser.chrome == undefined) {
            alert('This app was designed for google chrome.  Some features may not work in other browsers.  Please use google Chrome.  (browser compatibility will be added at a later date)');
        }

        try {
            app.connection = new WebSocket(serverAddress);

            app.connection.onopen = function (e) {
                app.onOpen(e);
            };
            app.connection.onmessage = function (e) {
                app.onMessage(e);
            };
            app.connection.onclose = function (e) {
                app.onClose(e);
            }
            app.connection.onerror = function (e) {
                app.onError(e);
            }

        } catch (ex) {
            console.log(ex);
        }

        $('#edit-profile').click(function(){
            $('#edit-name').val(app.user['name']);
            $('#edit-profile-modal').modal();
        });

        $('#edit-profile-form').submit(function(){
            app.handleProfileEditForm();
        });

        $('#save-profile').click(function() {
            app.handleProfileEditForm();
        });

        $('.alert .close').live("click", function(e) {
            $(this).parent().hide();
        });

        $("#message").keypress(function(event) {
            //check if we need to submit the message.
            if (event.keyCode == 13 && !event.shiftKey) {
                //submit the message.
                app.submitMessage($("#message").val());

                //clear the message container.
                $("#message").val('');

                //Don't allow the enter key to be processed.
                event.preventDefault();
            }
        });

        $('#message-list').scroll(function(event){
            if(($('#message-list').scrollTop() + $('#message-list').height()) == $('#message-list').prop('scrollHeight')) {
                app.messageListAutoScroll = true;
            } else {
                app.messageListAutoScroll = false;
            }
        });

        $('#show-notifications').click(function(e){
            window.webkitNotifications.requestPermission();

            e.preventDefault();
        });

        if (window.webkitNotifications.checkPermission() != 0) { // 0 is PERMISSION_ALLOWED
            $('#show-notifications').css('visibility', 'visible');
        }

        $([window, document]).blur(function () {
            app.visible = false;
        });

        $([window, document]).focus(function () {
            app.visible = true;
        });

        app.timeLoop = setInterval('app.updateMessageTimes()', 1000);
    },

    /**
     * Actions:
     *   -- UPDATE_USER (user object)
     *   -- SEND_CHAT_MESSAGE (text object)
     */
    send: function(action, object)
    {
        data = { };

        data['action'] = action;

        if (object == undefined) {
            object = [];
        }

        data['data']   = object;

        app.connection.send(JSON.stringify(data));
    },

    onOpen: function(event)
    {
        console.log("Connection established!");
        $("#connection-status").removeClass('badge-important');
        $("#connection-status").addClass('badge-success');
        $("#connection-status").html("Online");

        $("#message").removeAttr('disabled');
    },

    onMessage: function(event)
    {
        data = JSON.parse(event.data);

        if (data['action'] == undefined) {
            console.log('Error: No action provided');
        }

        console.log(data['action']);

        switch(data['action']) {
            case 'USER_CONNECTED':
                app.onUserConnected(data['data']);
                break;
            case 'USER_DISCONNECTED':
                app.onUserDisconnected(data['data']);
                break;
            case 'USER_INFORMATION':
                app.onUserInformation(data['data']);
                break;
            case 'USER_UPDATED':
                app.onUserUpdated(data['data']);
                break;
            case 'MESSAGE_NEW':
                app.onNewMessage(data['data']);
                break;
            case 'STEAM_PROFILES':
                app.onSteamProfiles(JSON.parse(data['data']));
                break;
        }
    },

    onClose: function(event)
    {
        console.log(event.data);

        $('#error-modal-alert-text').html("There was an error and you have been disconnected.");
        $('#error-modal').modal('show');

        $("#message").attr('disabled', 'disabled');

        $("#connection-status").removeClass('badge-success');
        $("#connection-status").addClass('badge-important');
        $("#connection-status").removeClass('badge-warning');
        $("#connection-status").html("Offline");
    },

    onError: function(event)
    {
        console.log("Error");

        app.onClose(event);

        alert(event.data);
    },

    onUserConnected: function(data)
    {
        //Add the user to our internal users array.
        app.users[data['LAN\\User\\Record']['id']] = data['LAN\\User\\Record'];

        if (app.users[data['LAN\\User\\Record']['id']]['status'] == 'ONLINE') {
            app.addUser(data['LAN\\User\\Record']);
        }
    },

    onUserDisconnected: function(data)
    {
        app.removeUser(data['LAN\\User\\Record']);
    },

    onUserInformation: function(data)
    {
        app.user = data['LAN\\User\\Record'];

        $.cookie('lan', app.user['id'], { path: '/' });

        if (app.user.name == "UNKNOWN") {
            $('#edit-profile-modal').modal();
        }

        $('#edit-profile-link').html(app.user['name']);

        var elementId = app.getUserElementId(app.user);

        $('#' + elementId).removeClass('them');
        $('#' + elementId).addClass('me');
    },

    onUserUpdated: function(data)
    {
        app.updateUser(data['LAN\\User\\Record']);

        //Update the internal user,
        app.users[data['LAN\\User\\Record']['id']] = data['LAN\\User\\Record'];
    },

    onNewMessage: function(data)
    {
        app.addMessage(data['LAN\\Message\\Record']);

        //Add the message to the internal list of messages.
        app.messages[data['LAN\\Message\\Record']['id']] = data['LAN\\Message\\Record'];
    },

    addMessage: function(message)
    {
        var userClass = 'them';

        if (message['users_id'] == app.user['id']) {
            userClass = 'me';
        }

        var time = moment(message['date_created']).fromNow()

        $('#message-list').append("<li id='message-" + message['id'] + "' class='" + userClass + "'><span class='steam-image-" + message['users_id'] + "'></span> " + message['message'] + " <div class='info'><span class='user user-" + message['users_id'] + "'>" + app.users[message['users_id']]['name'] + "</span> <span class='message-date'>" + time + "</span></div></li>");

        app.scrollMessages();

        if (window.webkitNotifications.checkPermission() == 0 && app.visible == false) {
            // function defined in step 2

            notification = window.webkitNotifications.createNotification(
                app.baseURL + 'img/alert.png', 'LAN: New Message', message['message']);

            notification.onclick = function() {
                //Focus the window.
                window.focus();

                app.clearNotifications();
            };

            notification.onclose = function() {
                //Focus the window.
                window.focus();

                app.clearNotifications();
            };

            notification.show();

            app.notifications.push(notification);
        }
    },

    onSteamProfiles: function(data)
    {
        if (data.response.players == undefined) {
            return;
        }

        for (id in data.response.players) {
            var profile = data.response.players[id];
            var steamId = data.response.players[id].steamid;

            var userId = app.getUserIdBySteamId(steamId);

            if (!userId) {
                continue;
            }

            $('.steam-image-' + userId).html("<img src='" + profile.avatar + "' />");

            $('.steam-name-' + userId).html("<a href='steam://url/SteamIDPage/" + steamId + "'>" + profile.personaname  + "</a>");

            if (profile.gameextrainfo != undefined) {
                $('.steam-game-' + userId).html("Now Playing: <a href='steam://run/" + profile.gameid + "'>" + profile.gameextrainfo + "</a>");
            } else {
                $('.steam-game-' + userId).html('');
            }
        }
    },

    getUserIdBySteamId: function(steamId)
    {
        for (id in app.users) {
            if (app.users[id].steam_id_64 == steamId) {
                return id;
            }
        }

        return false;
    },

    addUser: function(user)
    {
        var elementId = app.getUserElementId(user);

        //Only append if it does not already exist
        if ($('#' + elementId).length != 0) {
            return;
        }

        var html = "<li id='" + elementId + "'>" +
                       "<ul>" +
                            "<li><span class='steam-image-" + user['id'] + "'></span> <span class='user-name'>" + user['name'] + "</span> <span class='steam-name-" + user['id'] + "'></span></li>" +
                            "<li>Host: <span class='user-host'>" + user['host_name'] + "</span> (<span class='user-ip'>" + user['ip'] + "</span>)</li>" +
                            "<li><span class='steam-game-" + user['id'] + "'></span></li>" +
                        "</ul>" +
                   "</li>";

        $('#user-list').append(html);

        $('#' + elementId).addClass('them');
    },

    removeUser: function(user)
    {
        var elementId = app.getUserElementId(user);

        //Only append if it does not already exist
        if ($('#' + elementId).length == 0) {
            return;
        }

        $('#' + elementId).remove();
    },

    updateUser: function(user)
    {
        var elementId = app.getUserElementId(user);

        $('#' + elementId + " .user-name").html(user['name']);

        $('.user-' + user['id']).html(user['name']);

        //Update the client user if we need to.
        if (user['id'] == app.user['id']) {
            app.user = user;
            $('#edit-profile-link').html(app.user['name']);
        }
    },

    getUserElementId: function(user) {
        return 'LAN-User-Record-' + user['id'];
    },

    handleProfileEditForm: function() {
        var name = $('#edit-name').val();

        if (name == '' || name == null) {
            $('#edit-profile-alert-text').html("You must fill in a name");
            $('#edit-profile-alert').addClass('fade in');
            $('#edit-profile-alert').show();
            $('#edit-profile-alert').alert();
            return;
        }

        app.user.name = name;

        app.send('UPDATE_USER', app.user);

        $('#edit-profile-modal').modal('hide');
    },

    submitMessage: function(message)
    {
        if (message == undefined) {
            return false;
        }

        app.send('SEND_CHAT_MESSAGE', message);
    },

    updateMessageTimes: function()
    {
        for (id in app.messages){

            var time = moment(app.messages[id]['date_created']).fromNow()

            $('#message-' + id + " .message-date").html(time);
        }

    },

    scrollMessages:function () {
        if (app.messageListAutoScroll) {
            $("#message-list").scrollTop($("#message-list").prop('scrollHeight'));
        }
    },

    clearNotifications: function() {
        for (id in app.notifications) {
            app.notifications[id].cancel();
        }
    }
};