<?php
session_start();
$score = round($_SESSION['score']);
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$partnerID = $_SESSION["partnerID"];
$partnerName = $_SESSION["partnerName"];
if ($mode == 0) {
  $lineID = $partnerID;
  $circleID = $id;
  $userA = $name;
  $userB = $partnerName;
} else {
  $lineID = $id;
  $circleID = $partnerID;
  $userA = $partnerName;
  $userB = $name;
}
$db = new PDO("sqlite:backend/data.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$fet = $db->query("select  count(*) from ranking where score>" . $score . ";");
$fet = $fet->fetchAll();
$rank = $fet[0][0] + 1;
$fet = $db->query("select  count(*) from ranking where userA='" . $userA . "' and userB='" . $userB . "' and score=" . $score . ";");
$fet = $fet->fetchAll();
if (!$fet[0][0] >= 1) {
  $db->query("insert into ranking(userA,userB,score) values('" . $userA . "','" . $userB . "'," . $score . ");");
} else $rank - 1;
$fet = $db->query("select  count(*) from ranking;");
$fet = $fet->fetchAll();
$allUsers = $fet[0][0];
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8" />
        <title>結果</title>
        <link rel="stylesheet" href="style.css" />
    </head>

    <body>
        <div class="result_page">
            <h1>結果発表</h1>
            <h2 id="users"><?php echo $userA . "と" . $userB . "のスコアは" ?></h2>
            <h1 id="score"><?php echo $score . "点" ?></h1>
            <h1 id="rank"><?php echo $rank . "位/" . $allUsers ?></h1>
            <input type="button" value="ランキングを見る" onclick="location.href='ranking.php'" class="button">
            <input type="button" value="トップページに戻る" onclick="location.href='top.html'" class="button">
        </div>
    </body>

</html>