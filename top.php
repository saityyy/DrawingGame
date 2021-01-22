<?php
session_start();
$_SESSION['maxQNum'] = 3;
$pdo = new PDO("sqlite:backend/data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
if (isset($_SESSION['id'])) {
    $pdo->query("update users set partnerID=-1 where id=" . $_SESSION['id']);
}
?>

<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8" />
        <title>Top</title>
        <link rel="stylesheet" href="style.css" />
    </head>

    <body>
        <div class="center">
            <img src="img/DrawingGameLogo.png" class="logo" />
            <div class="account">
                <?php
            if (isset($_SESSION['username'])) {
                echo '<h2>ようこそ' . $_SESSION['username'] . 'さん!!</h2>';
            } else {
                echo '<a href="login_form.php">ログイン</a>';
            } ?>
                <p><a href="logout.php">ログアウト</a></p>
                <p><a href="newac_form.php">新規アカウント</a></p>
            </div>
            <h1>選択</h1>
            <br />
            <img src="img/compass2.png" /> 　　
            <img src="img/ruler2.png" />
        </div>
        <form action="frontMatching.php" method="GET">
            <div class="mode0">
                <input type="radio" name="mode" value="0" />
                <label for="0">コンパス</label>
            </div>
            <div class="mode1">
                <input type="radio" name="mode" value="1" checked />
                <label for="1">定規</label>
            </div>
            <br />
            <div class="center">
                <input type="submit" value="マッチング開始" class="button" />
            </div>
        </form>
    </body>

</html>