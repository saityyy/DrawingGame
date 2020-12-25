<?
session_start();
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

$pdo = new PDO("sqlite:data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$st = $pdo->query("select * from user");
$data = $st->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>drawing</title>
    </head>

    <body>
        <div class=login>
            <?
        session_start();
        if (isset($_SESSION["username"])) {
            echo '<a>ようこそ' . h($_SESSION["username"]) . 'さん！</a>';
            echo '<a href="logout.php">ログアウト</a>';
        } else {
            echo '<a href="login_form.php">ログイン</a>';
        }?>
        </div>
        <a href="newac_form.php">新規アカウント</a>
        <h1>選択</h1>
        <form action="matching.php" method="GET">
            <input type="radio" name="mode" value="0">
            <label for="0">コンパス</label>
            <input type="radio" name="mode" value="1" checked>
            <label for="1">定規</label>
            <input type="submit" value="マッチング開始">
        </form>
    </body>

</html>