<?php
session_start();
$_SESSION["mode"] = $_GET["mode"];
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8" />
        <title>matching</title>
        <link rel="stylesheet" href="style.css" />
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
            }, 1000);
            setTimeout(function() {
                window.location.href = "drawing.php?currentQNum=1";
            }, (sec + 1) * 1000);
        }
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "backend/backMatching.php", true);
        xhr.responseType = "json";
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
                var QStack = res["QStack"];
                var conn = new WebSocket("ws://localhost:80");
                conn.onopen = function(e) {
                    if (mode == 0) {
                        console.log("connection for comment established!");
                        if (partnerID != -100) {
                            //マッチング成功
                            var json = JSON.stringify({
                                partnerID: partnerID,
                                id: id,
                                name: name,
                                QStack: QStack,
                            });
                            //パートナーに通知を送る
                            //認証用にパートナーのIDを送る=>受け取ったパートナーが自分のIDであることを確かめる
                            conn.send(json);
                            console.log("send_success");
                            //カウントダウン開始
                            countDown(partnerName);
                        } else {
                            $("#result").html("<h2>パートナーが見つかりませんでした</h2>");
                            $("#result").append("<a href=''>もう一度探す</a>");
                        }
                    }
                };
                //mode0ユーザーからマッチングが成功したことをWebSocketで伝えられる
                conn.onmessage = function(e) {
                    var temp = JSON.parse(e.data);
                    //送ってきた相手が本当にあなたのパートナーかを検証
                    //＝＞mode0ユーザが送ってきたマッチング相手のIDが自分のIDであればtrue
                    if (temp["partnerID"] == id) {
                        console.log("receive_success");
                        var xhr2 = new XMLHttpRequest();
                        xhr2.open("POST", "backend/backMatching.php");
                        xhr2.setRequestHeader(
                            "content-type",
                            "application/x-www-form-urlencoded;charset=UTF-8"
                        );
                        xhr2.send("json=" + encodeURIComponent(e.data));
                        xhr2.onreadystatechange = function() {
                            if (xhr2.status == 200 && xhr2.readyState == 4) {
                                countDown(temp["name"]);
                            }
                        };
                    }
                };
            }
        };
        $("#result").html("<h1>マッチング中‥‥‥</h1>");
        </script>
    </body>

</html>