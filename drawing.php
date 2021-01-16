<?php
session_start();
//$_SESSION['addFigureStack'] = [];
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>drawing</title>
    </head>

    <body>
        <h2>二等分線を引け</h2>
        <div id="stage" style="width:900px;height:600px;border:solid 1px #000"></div>
        <div id=correctJudge>
            <input type="button" value="正誤判定" onclick="judge()">
            <p id=result></p>
        </div>
        <input type="button" value="一つ前に戻る" onclick="undoDraw()">
        <input type="button" value="ターン交代" onclick="changeTurn()">
        <script src="https://cdn.anychart.com/js/latest/graphics.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="drawing.js"></script>
        <script>
        var iniSetFlag = false;
        var wsFlag = false;
        var stage = acgraph.create('stage');
        var Draw = new drawing(stage);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'backend/iniSet.php', true);
        xhr.responseType = 'json';
        xhr.setRequestHeader('content-type',
            'application/x-www-form-urlencoded;charset=UTF-8');
        xhr.send(null);
        xhr.onreadystatechange = function() {
            if (xhr.status == 200 && xhr.readyState == 4) {
                var res = xhr.response;
                console.log(res);
                Draw.setData(res);
                draw();
                iniSetFlag = true;
                if (Draw.turnFlag == 1) console.log("your turn");
                else console.log("partner turn");
                console.log("addFigureStack => " + Draw.addFigureStack);
            }
        };


        function draw() {
            Draw.drawLines.forEach(function(xy) {
                Draw.Line(xy);
            });
            Draw.drawCircles.forEach(function(xy) {
                Draw.Circle(xy, undefined, 5);
            })
            Draw.addLines.forEach(function(xy) {
                Draw.Line(xy);
            });
            Draw.addCircles.forEach(function(xy) {
                Draw.Circle(xy);
            })
        }

        function changeTurn() {
            if (iniSetFlag && wsFlag && Draw.turnFlag == 1) {
                var xhr2 = new XMLHttpRequest();
                xhr2.open('POST', 'backend/changeTurn.php', true);
                xhr2.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                var array = {
                    'id': Draw.id,
                    'addLines': Draw.addLines,
                    'addCircles': Draw.addCircles,
                    'addFigureStack': Draw.addFigureStack
                }
                console.log(array);
                var json = JSON.stringify(array);
                xhr2.send("json=" + encodeURIComponent(json));
                conn.send(json);
                window.location.href = "drawing.php";
            }
        }
        var conn = new WebSocket('ws://localhost:80');
        conn.onopen = function(e) {
            console.log("connection for comment established!");
            wsFlag = true;
        };
        conn.onmessage = function(e) {
            data = JSON.parse(e.data);
            if (data['id'] == Draw.partnerID) {
                var xhr2 = new XMLHttpRequest();
                xhr2.open('POST', 'backend/changeTurn.php', true);
                xhr2.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                xhr2.send("afs=" + encodeURIComponent(e.data));
                xhr2.onreadystatechange = function() {
                    if (xhr2.status == 200 && xhr2.readyState == 4) {
                        console.log(xhr2.response);
                        //window.location.href = "drawing.php";
                    }
                }
            }
        };

        function judge(linesAns, circlesAns) {
            var correct;
            var drawLines = Draw.drawLines.concat(Draw.addLines);
            var drawCircles = Draw.drawCircles.concat(Draw.addCircles);
            console.log(drawLines);
            console.log(drawCircles);
            correct = linesAns.every(function lines_check(ans) {
                //(startX,startY,grad,length)
                ans = [
                    ans[0],
                    ans[1],
                    (ans[3] - ans[1]) / (ans[2] - ans[0]),
                    dist(ans[0] - ans[2], ans[1] - ans[3])
                ];
                var flag2 = this.drawLines.some(function line_check(inp) {
                    inp = [
                        inp[0],
                        inp[1],
                        (inp[3] - inp[1]) / (inp[2] - inp[0]),
                        dist(inp[0] - inp[2], inp[1] - inp[3])
                    ];
                    Draw.judgeLine(inp, ans);
                });
                return flag2;
            });
            correct = correct && this.circlesAns.every(function circles_check(ans) {
                var flag2 = drawCircles.some(function circle_check(inp) {
                    return Draw.judgeCircle(inp, ans);
                });
                return flag2;
            })
            return correct;
        }

        function undoDraw() {
            var Stack = Draw.addFigureStack.pop();
            console.log(Draw.addFigureStack);
            if (Stack == 0) Draw.addCircles.pop();
            else if (Stack == 1) Draw.addLines.pop();
            stage.rect(0, 0, 900, 600).fill('white');
            draw();
        }
        $('#stage').on('mousemove', function(e) {
            if (Draw.turnFlag == 1) {
                stage.rect(0, 0, 900, 600).fill('white');
                Draw.mouseMove(e.offsetX, e.offsetY);
                draw();
            }
        });
        $('#stage').on('click', function(e) {
            if (Draw.turnFlag == 1) {
                Draw.mouseClick(e.offsetX, e.offsetY);
                console.log(Draw.addFigureStack);
            }
        });
        </script>
    </body>

</html>