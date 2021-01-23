<?php
session_start();
$db = new PDO("sqlite:backend/data.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$fet = $db->query("select  * from ranking order by score desc limit 10;");
$topTen = $fet->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8" />
        <title>結果発表</title>
        <link rel="stylesheet" href="style.css" />
    </head>

    <body>
        <div class="result_page">
            <h1>ランキング</h1>
            <table>
                <tbody>
                    <?php
                foreach ($topTen as $i => $v) {
                    echo "<tr>";
                    echo "<td>" . ($i + 1) . "</td>";
                    echo "<td>" . $v['userA'] . " & " . $v['userB'] . "</td>";
                    echo "<td>" . $v['score'] . "</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
            <input type="button" value="トップページに戻る" onclick="location.href='top.php'" class="button">
        </div>
    </body>

</html>