<?
session_start();
$_SESSION["mode"]=$_GET["mode"];
$mode=$_SESSION["mode"];
$name=$_SESSION["username"];
$id=$_SESSION["id"];
$pdo = new PDO("sqlite:data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$st = $pdo->prepare("update user set mode=".$mode." where id=".$id.";");
$st = $pdo->prepare("update user set partnerID=0 where id=?;");
$st->execute(array($id));
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
        console.log("mode" + mode);
        var id = <?php echo $id; ?>;
        console.log("id" + id);
        setInterval(function() {
            <?php
            $pdo = new PDO("sqlite:data.sqlite");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

            # mode=0 -> マッチング待ちのmode=1ユーザーを探索
            if ($mode == 0) {
                $st = $pdo->query("select * from user where partnerID=" . $id . ";");
                $st = $pdo->query("select * from user where mode=1 and partnerID=0;");
                $data = $st->fetchAll();
                if (count($data) == 0) $result = false;
                else {
                    $result = true;
                    $rd = rand(0, count($data) - 1);
                    $selectedID = $data[$rd]["id"]; //マッチング相手のID
                    //相手のパートナーIDの欄を自分のIDにする
                    $st = $pdo->query("update user set partnerID=" . $id . "where id=" . $selectedID . ";");
                    //自分のパートナーIDの欄を相手のIDにする
                    $st = $pdo->query("update user set partnerID=" . $selectedID . "where id=" . $id . ";");
                }
            } else {
                # mode=1 -> マッチングされたかチェック
                # マッチングした相手を探す
                $st = $pdo->query("select * from user where partnerID=" . $id . ";");
                $data = $st->fetchAll();
                if (count($data) == 0) $result = false;
                else {
                    $result = true;
                    $selectedID = $data[0]["id"];
                }
            }
            if ($result) { ?>
            console.log(<?php echo '"' . $id . ' , ' . $selectedID . '"'; ?>);
            <?php } else { ?>
            console.log("not found");
            <?php } ?>
        }, 1000);
        <?php if ($result) {
            $pdo = new PDO("sqlite:data.sqlite");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $st = $pdo->prepare("update user set partnerID=-1 where id=" . $id . ";");
            $st = $pdo->prepare("update user set partnerID=-1 where id=" . $selectedID . ";");
        } ?>
        </script>
    </body>

</html>