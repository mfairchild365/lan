var app = {
    connection : false,

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

    addUser: function(user)
    {
        var elementId = 'LAN-User-Record-' + user['id'];

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
    },

    removeUser: function(user)
    {
        var elementId = 'LAN-User-Record-' + user['id'];

        //Only append if it does not already exist
        if ($('#' + elementId).length == 0) {
            return;
        }

        $('#' + elementId).remove();
    }

};