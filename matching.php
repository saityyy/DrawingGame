
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>matching</title>
    </head>

    <body>
        <?php
        $mode=$_get["mode"];
        ?>
        <h1>マッチング中</h1>
        <script>
        var mode=<?php echo $mode;?>;
        console.log(mode);
        </script>
    </body>
</html>