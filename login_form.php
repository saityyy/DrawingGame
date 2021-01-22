<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8" />
        <title>ログイン</title>
        <link rel="stylesheet" href="style.css" />
    </head>

    <body>
        <div class="login">
            <h2>ユーザ名とパスワードを入力してください</h2>
            <form action="backend/login_form.php" method="get" target="result">
                <p>ユーザ名</p>
                <input type="text" name="username" />
                <p>パスワード</p>
                <input type="password" name="password" />
                <input type="submit" value="送信" />
            </form>
            <iframe name="result"></iframe>
            <p><a href="top.php">トップページに戻る</a></p>
        </div>
    </body>

</html>