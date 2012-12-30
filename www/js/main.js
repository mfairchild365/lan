var app = {
    connection : false,
    user       : false,

    init: function (serverAddress)
    {
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
        data['data']   = object;

        app.connection.send(JSON.stringify(data));
    },

    onOpen: function(event)
    {
        console.log("Connection established!");
        $("#connection-status").removeClass('badge-important');
        $("#connection-status").addClass('badge-success');
        $("#connection-status").html("Online");
    },

    onMessage: function(event)
    {
        data = JSON.parse(event.data);

        if (data['action'] == undefined) {
            console.log('Error: No action provided');
        }

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
        }
    },

    onClose: function(event)
    {
        console.log(event.data);

        $("#connection-status").removeClass('badge-success');
        $("#connection-status").addClass('badge-important');
        $("#connection-status").html("Offline");
    },

    onError: function(event)
    {
        console.log(event.data);
    },

    onUserConnected: function(data)
    {
        app.addUser(data['LAN\\User\\Record']);
    },

    onUserDisconnected: function(data)
    {
        app.removeUser(data['LAN\\User\\Record']);
    },

    onUserInformation: function(data)
    {
        app.user = data['LAN\\User\\Record'];

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
                            "<li><span class='user-name'>" + user['name'] + "</span></li>" +
                            "<li><span class='user-ip'>" + user['ip'] + "</span></li>" +
                            "<li><span class='user-host'>" + user['host_name'] + "</span></li>" +
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
    }
};