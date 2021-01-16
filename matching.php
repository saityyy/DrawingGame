<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>matching</title>
    </head>

    <body>

        <div id="result"></div>
        <a href="top.html">トップページに戻る</a>
        <p id="partner"></p>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script type="text/javascript">
        function countDown(Pname, sec = 3) {
            var i = sec;
            setInterval(function() {
                $("#result").html("<h1>マッチングに成功しました</h1>");
                $("#result").append("<h2>開始まであと" + i + "秒</h2>");
                $("#result").append("<h2>パートナー:" + Pname + "</h2>");
                i--;
            }, 1000)
            setTimeout(function() {
                window.location.href = "drawing.php?currentQNum=1";
            }, (sec + 1) * 1000);
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'backend/matching.php', true);
        xhr.responseType = 'json';
        xhr.send(null);
        xhr.onreadystatechange = function() {
            if (xhr.status == 200 && xhr.readyState == 4) {
                var res = xhr.response;
                console.log(res);
                var mode = res["mode"];
                var id = res["id"];
                var name = res["name"];
                var partnerID = res["partnerID"];
                var partnerName = res["partnerName"];
                var conn = new WebSocket('ws://localhost:80');
                conn.onopen = function(e) {
                    if (mode == 0) {
                        console.log("connection for comment established!");
                        if (partnerID != -100) {
                            conn.send([partnerID, id, name]);
                            console.log("send_success");
                            countDown(partnerName);
                        } else {
                            $("#result").html("<h2>パートナーが見つかりませんでした</h2>");
                            $("#result").append("<a href=''>もう一度探す</a>");
                        }
                    }
                };
                conn.onmessage = function(e) {
                    var temp = e.data.split(",");
                    if (temp[0] == id) {
                        console.log(temp);
                        var url = "backend/matching.php";
                        url += "?partnerID=" + temp[1];
                        url += "&partnerName=" + temp[2];
                        console.log(url);
                        var xhr2 = new XMLHttpRequest();
                        xhr2.open('GET', url);
                        xhr2.send(null);
                        xhr2.onreadystatechange = function() {
                            if (xhr2.status == 200 && xhr2.readyState == 4) {
                                countDown(temp[2]);
                            }
                        }
                    }
                };
            }
        };
        $("#result").html("<h1>マッチング中</h1>");
        </script>
    </body>

</html>