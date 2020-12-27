<?php
session_start();
if (isset($_GET["mode"])) $_SESSION["mode"] = $_GET["mode"];
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$pdo = new PDO("sqlite:data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$st = $pdo->query("update user set mode=" . $mode . " where id=" . $id . ";");
$st = $pdo->query("update user set partnerID=0 where id=" . $id . ";");
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>matching</title>
    </head>

    <body>

        <h1></h1>
        <h2></h2>
        <a href="http://localhost:8080/kadai/DrawingGame/matching.php"></a>
        <p id="partner"></p>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script type="text/javascript">
        var mode = <?php echo $mode; ?>;
        var id = <?php echo $id; ?>;
        var name = <?php echo '"' . $name . '"'; ?>;
        var nextURL = location.href.split('matching.php');
        nextURL = nextURL[0] + "drawing.php";
        console.log(nextURL);
        var conn = new WebSocket('ws://localhost:80');

        function transition(sec) {
            setTimeout(function() {
                window.location.href = nextURL;
            }, sec * 1000);
        }
        var result = false;
        <?php
        if ($mode == 0) {
            $st = $pdo->query("select * from user where mode=1 and partnerID=0;");
            $data = $st->fetchAll();
            if (count($data) > 0) {
                $rd = rand(0, count($data) - 1);
                $partnerID = $data[$rd]["id"]; //マッチング相手のID
                $partnerName = $data[$rd]["name"];
                $st = $pdo->query("update user set partnerID=" . $partnerID . " where id=" . $id . ";");
                $st = $pdo->query("update user set partnerID=" . $id . " where id=" . $partnerID . ";");
                $_SESSION["partnerID"] = $partnerID;
                $_SESSION["partnerName"] = $partnerName;
        ?>
        result = true;
        console.log("success");
        var partnerID = <?php echo $partnerID; ?>;
        var partnerName = <?php echo '"' . $partnerName . '"'; ?>;
        conn.onmessage = function(e) {
            var temp = e.data.split(",");
            if (temp[0] == id) {
                $("h1").text("マッチングに成功しました");
                $("h2").text("開始まであと３秒");
                $("p").text("パートナー:" + temp[2]);
                transition(3);
            }
        };
        conn.onopen = function(e) {
            console.log("connection for comment established!");
            if (result) {
                conn.send([partnerID, id, name]);
                console.log("send_success");
            } else {
                $("h1").text("パートナーが見つかりませんでした");
                $("a").text("もう一度探す");
            }
        };
        <?php
            } ?>
        $("h1").text("パートナーが見つかりませんでした");
        $("a").text("もう一度探す");
        <?php
            #mode=1
        } else { ?>
        $("h1").text("マッチング中");
        conn.onmessage = function(e) {
            var temp = e.data.split(",");
            console.log("temp : " + temp);
            if (temp[0] == id) {
                $("h1").text("マッチングに成功しました");
                $("h2").text("開始まであと３秒");
                $("p").text("パートナー:" + temp[2]);
                console.log("receive_success");
                conn.send([temp[1], id, name]);
                var xhr = new XMLHttpRequest();
                nextURL += "?partnerID=" + encodeURIComponent(temp[1]);
                nextURL += "&partnerName=" + encodeURIComponent(temp[2]);
                xhr.open("GET", nextURL);
                xhr.send(null);
                transition(3);
            }
        };
        conn.onopen = function(e) {
            console.log("connection for comment established!");
        };
        <?php
        } ?>
        </script>
    </body>

</html>