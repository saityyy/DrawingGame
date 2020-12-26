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
        console.log("mode" + mode);
        var id = <?php echo $id; ?>;
        console.log("id" + id);
        setInterval(function() {
            <?php
            $pdo = new PDO("sqlite:data.sqlite");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

            $result = false;
            # mode=0 -> マッチング待ちのmode=1ユーザーを探索
            if ($mode == 0) {
                $st = $pdo->query("select * from user where mode=1 and partnerID=0;");
                $data = $st->fetchAll();
                if (count($data) > 0) {
                    #echo "console.log(" . count($data) . ");";
                    $rd = rand(0, count($data) - 1);
                    $partnerID = $data[$rd]["id"]; //マッチング相手のID
                    #echo "console.log(" . $partnerID . ");";
                    //自分のパートナーIDの欄を相手のIDにする
                    $st = $pdo->query("update user set partnerID=" . $partnerID . " where id=" . $id . ";");
                }
                $st = $pdo->query("select * from user where mode=1 and partnerID=" . $id . ";");
                $data = $st->fetchAll();
                if (count($data) != 0) {
                    $result = true;
                    $partnerName = $data[0]["name"];
                }
                #mode=1
            } else {
                $st = $pdo->query("select * from user where partnerID=" . $id . ";");
                $data = $st->fetchAll();
                if (count($data) != 0) {
                    echo "console.log(" . $data[0]["partnerID"] . ");";
                    $partnerID = $data[0]["id"]; //マッチング相手のID
                    $partnerName = $data[0]["name"];
                    //自分のパートナーIDの欄を相手のIDにする
                    $st = $pdo->query("update user set partnerID=" . $partnerID . " where id=" . $id . ";");
                    $result = true;
                }
            }
            if ($show) {
                if ($result) { ?>
            console.log(<?php echo '"' . $id . ' , ' . $partnerID . '"'; ?>);
            console.log(<?php echo '"' . $name . ' , ' . $partnerName . '"'; ?>);
            console.log("マッチングに成功しました");
            <?php
                    #header("Location:drawing.php");
                    #exit;
                } else { ?>
            console.log("not found");
            <?php }
                $show = false;
            } ?>
        }, 1000);
        <?php
        $show = true;
        if (false) {
            $pdo = new PDO("sqlite:data.sqlite");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $st = $pdo->prepare("update user set partnerID=-1 where id=" . $id . ";");
            $st = $pdo->prepare("update user set partnerID=-1 where id=" . $partnerID . ";");
        } ?>
        </script>
    </body>

</html>