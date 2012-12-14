<html>
    <head>
        <script>
            try {
                var conn = new WebSocket('ws://192.168.1.131:8000/lan');
                console.log('WebSocket - status '+conn.readyState);
                conn.onopen = function(e) {
                    console.log("Connection established!");
                };
                conn.onmessage = function(e) {
                    console.log(e.data);
                };
            } catch(ex){
                console.log(ex);
            }
        </script>
    </head>
</html>