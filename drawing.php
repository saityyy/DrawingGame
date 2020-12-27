<?php
session_start();
if (isset($_GET["partnerID"]) && isset($_GET["partnerName"])) {
    $_SESSION["partnerID"] = $_GET["partnerID"];
    $_SESSION["partnerName"] = $_GET["partnerName"];
}
$partnerID = $_SESSION["partnerID"];
$partnerName = $_SESSION["partnerName"];
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$_SESSION["QNum"] = 1;
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>drawing</title>
    </head>

    <body>
        <?php
    echo '<p>id : ' . $id . '</p>';
    echo '<p>name : ' . $name . '</p>';
    echo '<p>partnerID : ' . $partnerID . '</p>';
    echo '<p>partnerName : ' . $partnerName . '</p>';
    ?>
        <h2>二等分線を引け</h2>
        <div id="stage" style="width:900px;height:600px;border:solid 1px #000"></div>
        <div id=correctJudge>
            <input type="button" value="正誤判定" onclick="judge()">
            <p id=result></p>
        </div>
        <input type="button" value="一つ前に戻る" onclick="r()">
        <input type="button" value="ターン交代" onclick="c()">
        <script src="https://cdn.anychart.com/js/latest/graphics.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script type="text/javascript">
        var mode = <?php echo $mode; ?>;
        var id = <?php echo $id; ?>;
        var partnerID = <?php echo $partnerID; ?>;
        var clickX = -1;
        var clickY = -1;
        var turn_flag = true;
        var draw_stack = [];
        var drawLines = [];
        var drawCircles = [];
        var stage = acgraph.create('stage');
        var conn = new WebSocket('ws://localhost:80');
        var ws_flag = false;
        conn.onopen = function(e) {
            console.log("connection for comment established!");
            ws_flag = true;
        };
        conn.onmessage = function(e) {
            if (e.data == partnerID) {
                console.log(e.data);
                turn_flag = true;
            }
        };
        //var conn = new WebSocket('ws://localhost:80');

        function c() {
            if (ws_flag) {
                turn_flag = false;
                conn.send(id);
            }
        }

        function dist(dx, dy) {
            return parseInt(Math.sqrt(dx * dx + dy * dy));
        }

        function Line(x1, y1, x2, y2) {
            var linePath = acgraph.path();
            linePath.parent(stage);
            linePath.moveTo(x1, y1);
            linePath.lineTo(x2, y2).fill("#000").strokeThickness(5);
            linePath.close();
        }

        function draw(lines, circles) {
            lines.forEach(function(xy) {
                Line(xy[0], xy[1], xy[2], xy[3]);
            });
            circles.forEach(function(xy) {
                stage.circle(xy[0], xy[1], xy[2]);
            });
        }

        function r() {
            var s = draw_stack.pop();
            if (s == 1) drawLines.pop();
            else if (s == 0) drawCircles.pop();
            stage.rect(0, 0, 900, 600).fill('white');
            draw(drawLines, drawCircles);
        }

        function fetch() {
            //drawLines,drawCircles
            drawLines.push([100, 500, 700, 500]);
            drawLines.push([100, 500, 600, 100]);
        }

        function judge() {
            //DBから取ってきた正解データと見比べる。
            //各問題に対する満たすべき条件で正誤判定する。
            //例えば、二等分線を引く問題だと始点と傾きがあっている線が一本引けていればいい。
            //他の問題でも始点、傾き、線の長さが条件を満たしていればok（だと思う）
            var correct;
            var linesAns = [];
            var circlesAns = [];
            //挙動チェック
            linesAns.push([100, 500, 700, 290]);
            console.log(drawLines);
            console.log(drawCircles);
            correct = linesAns.every(function lines_check(ans) {
                ans = [ans[0], ans[1], (ans[3] - ans[1]) / (ans[2] - ans[0]),
                    1
                ]; //dist(ans[0] - ans[2], ans[1] - ans[3])];
                var flag2 = drawLines.some(function line_check(inp) {
                    console.log("#######################");
                    correct = false;
                    var flag = true;
                    //[始点x座標、始点ｙ座標、傾き、角度]
                    inp = [inp[0], inp[1], (inp[3] - inp[1]) / (inp[2] - inp[0]), dist(inp[0] - inp[2],
                        inp[1] -
                        inp[3])];
                    //length_check
                    flag = flag && inp[3] > (ans[3] - 20);
                    console.log("length_check -> " + flag);
                    //angle_check
                    var ans_ang = Math.atan(ans[2]);
                    var inp_ang = Math.atan(inp[2]);
                    flag = flag && (ans_ang - 0.02 < inp_ang) && (inp_ang < ans_ang + 0.02);
                    console.log(ans_ang - 0.02 + " < " + inp_ang + " < " + ans_ang + 0.02);
                    console.log("angle_check -> " + flag);
                    //startPoint_check
                    flag = flag && (ans[0] - 10 < inp[0]) && (inp[0] < ans[0] + 10);
                    flag = flag && (ans[1] - 10 < inp[1]) && (inp[1] < ans[1] + 10);
                    console.log("startPoint_check -> " + flag);
                    return flag;
                });
                return flag2;
            });
            correct = correct && circlesAns.every(function circles_check(ans) {
                //円は中心と半径が多少合っていればいい
                var flag2 = drawCircles.some(function circle_check(inp) {
                    var flag = true;
                    flag = flag && (ans[0] - 10 < inp[0]) && (inp[0] < ans[0] + 10);
                    flag = flag && (ans[1] - 10 < inp[1]) && (inp[1] < ans[1] + 10);
                    flag = flag && (ans[2] - 10 < inp[2]) && (inp[2] < ans[2] + 10);
                    return flag;
                });
                return flag2;
            })
            if (correct) $('#result').text("正解です");
            else $('#result').text("不正解です");
        };
        draw(drawLines, drawCircles);
        $('#stage').on('mousemove', function(e) {
            stage.rect(0, 0, 900, 600).fill('white');
            if (clickX != -1 && clickY != -1) {
                if (mode) {
                    stage.circle(clickX, clickY, 5).fill("black");
                    Line(clickX, clickY, e.offsetX, e.offsetY);
                } else {
                    stage.circle(clickX, clickY, 5).fill("black");
                    stage.circle(clickX, clickY, dist(clickX - e.offsetX, clickY - e.offsetY));
                }
            }
            draw(drawLines, drawCircles);
        });
        $('#stage').on('click', function(e) {
            if (clickX == -1 && clickY == -1) {
                clickX = e.offsetX;
                clickY = e.offsetY;
            } else {
                if (mode) drawLines.push([clickX, clickY, e.offsetX, e.offsetY]);
                else drawCircles.push([clickX, clickY, dist(clickX - e.offsetX, clickY - e.offsetY)]);
                draw_stack.push(mode);
                clickX = -1;
                clickY = -1;
            }
        });
        </script>
    </body>

</html>