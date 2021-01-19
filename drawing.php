<?php
session_start();
if (isset($_GET['currentQNum'])) {
    $_SESSION['currentQNum'] = $_GET['currentQNum'];
}
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>drawing</title>
        <link rel="stylesheet" href="style.css" />
    </head>

    <body>
        <h1 id="turn"></h1>
        <div id="Qinfo">
            <h1 id="QNum"></h1>
            <h2 id="Qtext"></h2>
        </div>
        <p id=judge_result></p>
        <div id="stage" style="width:1200px;height:700px;border:solid 3px #000">
        </div>
        <div id=options>
            <input type="button" value="正誤判定" onclick="judgeRequest()" class="button">
            <input type="button" value="一つ前に戻る" onclick="undoDraw()" class="button">
            <input type="button" value="ターン交代" onclick="changeTurn()" class="button">
        </div>
        <script src="https://cdn.anychart.com/js/latest/graphics.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="drawing.js"></script>
        <script>
        console.log(<?php echo $_SESSION['score']; ?>);
        console.log(<?php echo $_SESSION['startT']; ?>);
        var startT;
        var endT;
        var iniSetFlag = false;
        var wsFlag = false;
        var stage = acgraph.create('stage');
        var Draw = new drawing(stage);
        var xhr = new XMLHttpRequest();
        var xhrb = new XMLHttpRequest();
        var xhrc = new XMLHttpRequest();
        xhr.open('POST', 'backend/iniSet.php', true);
        xhr.setRequestHeader('content-type',
            'application/x-www-form-urlencoded;charset=UTF-8');
        xhr.responseType = 'json';
        xhr.send(null);
        xhr.onreadystatechange = function() {
            if (xhr.status == 200 && xhr.readyState == 4) {
                var res = xhr.response;
                console.log(res);
                Draw.setData(res);
                draw();
                iniSetFlag = true;
                if (Draw.turnFlag == 1) {
                    $('#turn').text("あなたのターンです");
                    $('#stage').css("opacity", 1.0);
                } else {
                    $('#turn').text("相手のターンです。");
                    $('#stage').css("opacity", 0.5);
                }
                console.log("addFigureStack = > " + Draw.addFigureStack);
                $('#QNum').text("第" + Draw.currentQNum + "問目");
                $('#Qtext').text(Draw.Qtext);
                xhrb.open('POST', 'backend/score.php', true);
                xhrb.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                var t = new Date();
                console.log(t.getTime() / 1000);
                xhrb.send("score=" + encodeURIComponent(JSON.stringify({
                    "startT": parseInt(t.getTime() / 1000),
                    "diff": Draw.Qdiff,
                    "judge_result": null
                })));
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
                xhrc.open('POST', 'backend/changeTurn.php', true);
                xhrc.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                var array = {
                    'changeTurn': true,
                    'id': Draw.id,
                    'addLines': Draw.addLines,
                    'addCircles': Draw.addCircles,
                    'addFigureStack': Draw.addFigureStack
                }
                console.log(array);
                var json = JSON.stringify(array);
                xhrc.send("json=" + encodeURIComponent(json));
                conn.send(json);
                //window.location.href = "drawing.php";
            }
        }
        xhrc.onreadystatechange = function() {
            if (xhrc.status == 200 && xhrc.readyState == 4) {
                console.log(xhrc.response);
                window.location.href = "drawing.php";
            }
        }

        function countDown(sec = 5) {
            console.log(startT);
            console.log(endT);
            if (Draw.nextURL == "result.php") sec = 0;
            $("#judge_result").text("正解です。次の問題へ進みます。");
            $("#judge_result").css("color", "blue");
            setTimeout(function() {
                window.location.href = Draw.nextURL;
            }, sec * 1000);
        }
        var conn = new WebSocket('ws://localhost:80');
        conn.onopen = function(e) {
            console.log("connection for comment established!");
            wsFlag = true;
        };
        conn.onmessage = function(e) {
            data = JSON.parse(e.data);
            if (data['id'] == Draw.partnerID) {
                if (data['judge'] != undefined) {
                    var t = new Date();
                    startT = data['startT'];
                    endT = data['endT'];
                    xhrb.open('POST', 'backend/score.php', true);
                    xhrb.setRequestHeader('content-type',
                        'application/x-www-form-urlencoded;charset=UTF-8');
                    xhrb.send("score=" + encodeURIComponent(JSON.stringify({
                        "startT": startT,
                        "endT": endT,
                        "diff": Draw.Qdiff,
                        "judge_result": data['judge']
                    })));
                    if (data['judge'] == 1) countDown();
                    else window.location.href = "drawing.php";
                } else if (data['changeTurn']) {
                    xhrc.open('POST', 'backend/changeTurn.php', true);
                    xhrc.setRequestHeader('content-type',
                        'application/x-www-form-urlencoded;charset=UTF-8');
                    xhrc.send("afs=" + encodeURIComponent(e.data));
                }
            }
        };

        function judge() {
            if (iniSetFlag && wsFlag && Draw.turnFlag == 1) {
                var correct;
                var drawLines = Draw.addLines;
                var drawCircles = Draw.addCircles;
                var linesAns = Draw.ansLines;
                var circlesAns = Draw.ansCircles;
                //console.log(drawLines);
                //console.log(drawCircles);
                //console.log(linesAns);
                //console.log(circlesAns);
                correct = linesAns.every(function lines_check(ans) {
                    //(startX,startY,grad,length)
                    var flag2 = drawLines.some(function line_check(inp) {
                        grad = -(inp[3] - inp[1]) / (inp[2] - inp[0]);
                        leng = Draw.dist(inp[0] - inp[2], inp[1] - inp[3]);
                        console.log(grad + " , " + leng);
                        var fA = Draw.judgeLine([inp[0], inp[1], grad, leng], ans);
                        var fB = Draw.judgeLine([inp[2], inp[3], grad, leng], ans);
                        return fA || fB;
                    });
                    return flag2;
                });
                correct = correct && circlesAns.every(function circles_check(ans) {
                    console.log(1);
                    var flag2 = drawCircles.some(function circle_check(inp) {
                        console.log("inp :" + inp);
                        return Draw.judgeCircle(inp, ans);
                    });
                    return flag2;
                })
                return correct;
            }
        }

        function judgeRequest() {
            if (iniSetFlag && wsFlag && Draw.turnFlag == 1) {
                var result = 0;
                var t = new Date();
                startT = <?php echo $_SESSION['startT']; ?>;
                endT = parseInt(t.getTime() / 1000);
                console.log(startT);
                console.log(endT);
                if (judge()) {
                    result = 1;
                } else {
                    $("#judge_result").text("不正解です。もう一度頑張りましょう");
                    $("#judge_result").css("color", "red");
                }
                var json = JSON.stringify({
                    'judge': result,
                    'id': Draw.id,
                    'nextURL': Draw.nextURL,
                    'startT': startT,
                    'endT': endT
                });
                conn.send(json);
                xhrb.open('POST', 'backend/score.php', true);
                xhrb.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                xhrb.send("score=" + encodeURIComponent(JSON.stringify({
                    "diff": Draw.Qdiff,
                    "judge_result": result,
                    'startT': startT,
                    'endT': endT
                })));
                if (result == 1) countDown();
                else window.location.href = "drawing.php";
            }
        }

        function undoDraw() {
            var Stack = Draw.addFigureStack.pop();
            console.log(Draw.addFigureStack);
            if (Stack == 0) Draw.addCircles.pop();
            else if (Stack == 1) Draw.addLines.pop();
            stage.rect(0, 0, 1200, 700).fill("#01ff62");
            draw();
        }
        $('#stage').on('mousemove', function(e) {
            if (Draw.turnFlag == 1) {
                stage.rect(0, 0, 1200, 700).fill("#01ff62");
                draw();
                Draw.mouseMove(e.offsetX, e.offsetY);
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