<?php
session_start();

$_SESSION = array();
session_destroy();
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>ログアウト</title>
        <link rel="stylesheet" href="../style.css" />
    </head>

    <body>
        <div class="logout">
            <h2>ログアウトしました</h2>
            <p><a href="../top.html">トップページに戻る</a></p>
        </div>
    </body>

</html>