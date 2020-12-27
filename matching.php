<?php
session_start();
$_SESSION["mode"] = $_GET["mode"];
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

        <h1>マッチング中</h1>
        <script type="text/javascript">
        var mode = <?php echo $mode; ?>;
        var id = <?php echo $id; ?>;
        var conn = new WebSocket('ws://c6c23055028b.ngrok.io');
        var result;
        conn.onmessage = function(e) {
            console.log(e.data);
        };
        conn.onopen = function(e) {
            console.log("connection for comment established!");
            if (result) {
                conn.send("success");
                console.log("send_success");
            }
        };
        <?php
        $result = false;
        # mode=0 -> マッチング待ちのmode=1ユーザーを探索
        if ($mode == 0) {
            echo "console.log(" . count($data) . ");";
            $st = $pdo->query("select * from user where mode=1 and partnerID=0;");
            $data = $st->fetchAll();
            if (count($data) > 0) {
                $rd = rand(0, count($data) - 1);
                $partnerID = $data[$rd]["id"]; //マッチング相手のID
                $partnerName = $data[$rd]["name"];
                #echo "console.log(" . $partnerID . ");";
                $st = $pdo->query("update user set partnerID=" . $partnerID . " where id=" . $id . ";");
                $st = $pdo->query("update user set partnerID=" . $id . " where id=" . $partnerID . ";");
                $result = true;
        ?>
        console.log("success");
        var result = <?php echo $result; ?>;
        <?php
            }
            #mode=1
        } else { ?>
        var result = <?php echo $result; ?>
        conn.onmessage = function(e) {
            console.log("receive_success");
            console.log(e.data);
        };
        conn.onopen = function(e) {
            console.log("connection for comment established!");
        };
        <?php
        }
        if ($result) {
        ?>
        console.log(<?php echo '"' . $id . ' , ' . $partnerID . '"'; ?>);
        console.log(<?php echo '"' . $name . ' , ' . $partnerName . '"'; ?>);
        console.log("マッチングに成功しました");
        <?php
            #header("Location:drawing.php");
            #exit;
        } else { ?>
        console.log("not found");
        <?php } ?>
        </script>
    </body>

</html>