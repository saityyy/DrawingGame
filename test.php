<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>test</title>
    </head>

    <body>
        <h1></h1>
        <h2></h2>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script>
        var cnt = [1, 2, 3];
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'test2.php', true);
        //xhr.responseType = 'json';
        xhr.setRequestHeader('content-type',
            'application/x-www-form-urlencoded;charset=UTF-8');
        //xhr.setRequestHeader('content-type',
        //'application/json;charset=UTF-8');
        var json = {
            "a": [1, 2, 3, 4, 5],
            "b": 4
        };
        json = JSON.stringify(json);
        xhr.send("json=" + encodeURIComponent(json));
        xhr.onreadystatechange = function() {
            if (xhr.status == 200 && xhr.readyState == 4) {
                var res = JSON.parse(xhr.response);
                console.log(res);
            }
        };
        </script>
    </body>

</html>