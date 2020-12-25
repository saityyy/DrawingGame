<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>drawing</title>
    </head>

    <body>
        <a href="login_form.php">ログイン</a>
        <a href="newac_form.php">新規アカウント</a>
        <h1>選択</h1>
        <form action="matching.php" method="GET">
            <input type="radio" name="mode" value=0>コンパス
            <input type="radio" name="mode" value=1 checked>定規
            <input type="submit" value="マッチング開始">
        </form>
    </body>
</html>