<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8" />
        <title>新規登録</title>
        <link rel="stylesheet" href="style.css" />
    </head>

    <body>
        <div class="login_form">
            <h2>ユーザ名とパスワードを入力してください</h2>
            <form action="backend/newac_form.php" method="get" target="result">
                <p>ユーザ名</p>
                <input type="text" name="username" />
                <p>パスワード</p>
                <input type="password" name="password" />
                <input type="submit" value="送信" />
            </form>
        </div>
        <iframe name="result"></iframe>
        <p><a href="top.php">トップページに戻る</a></p>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script></script>
    </body>

</html>