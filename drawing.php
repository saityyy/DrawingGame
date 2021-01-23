<?php
session_start();
if (isset($_GET['currentQNum'])) {
    $_SESSION['currentQNum'] = $_GET['currentQNum'];
}
if (isset($_GET['judge'])) {
    $NoRightFlag = true;
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
        <p id=judge_result>
            <?php
        if ($NoRightFlag) {
            echo '不正解です。頑張りましょう。';
        }
        ?>
        </p>
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
        var t = new Date();
        console.log(<?php echo "スコア => " . $_SESSION['score']; ?>);
        var s = <?php echo $_SESSION['startT']; ?>;
        console.log(s)
        console.log("経過時間 => " + String(parseInt(t.getTime() / 1000) - s));
        var startT;
        var endT;
        var iniSetFlag = false;
        var wsFlag = false;
        var stage = acgraph.create('stage');
        var Draw = new drawing(stage);
        var XHR_iniset = new XMLHttpRequest();
        var XHR_score = new XMLHttpRequest();
        var XHR_turn = new XMLHttpRequest();
        XHR_iniset.open('POST', 'backend/iniSet.php', true);
        XHR_iniset.setRequestHeader('content-type',
            'application/x-www-form-urlencoded;charset=UTF-8');
        XHR_iniset.responseType = 'json';
        XHR_iniset.send(null);
        XHR_iniset.onreadystatechange = function() {
            if (XHR_iniset.status == 200 && XHR_iniset.readyState == 4) {
                var res = XHR_iniset.response;
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
                $('#QNum').text("第" + Draw.currentQNum + "問目");
                $('#Qtext').text(Draw.Qtext);
                //startTをsessionに記録するためにAjaxでpost送信
                XHR_score.open('POST', 'backend/score.php', true);
                XHR_score.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                var t = new Date();
                XHR_score.send("score=" + encodeURIComponent(JSON.stringify({
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
                XHR_turn.open('POST', 'backend/changeTurn.php', true);
                XHR_turn.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                var array = {
                    'changeTurn': true,
                    'id': Draw.id,
                    'addLines': Draw.addLines,
                    'addCircles': Draw.addCircles,
                    'addFigureStack': Draw.addFigureStack
                }
                var json = JSON.stringify(array);
                XHR_turn.send("json=" + encodeURIComponent(json));
                conn.send(json);
                window.location.href = "drawing.php";
            }
        }
        XHR_turn.onreadystatechange = function() {
            if (XHR_turn.status == 200 && XHR_turn.readyState == 4) {
                window.location.href = "drawing.php";
            }
        }

        function countDown(sec = 5) {
            if (Draw.nextURL == "result.php") sec = 0;
            $("#judge_result").text("正解です。次の問題へ進みます。");
            $("#judge_result").css("color", "blue");
            setTimeout(function() {
                window.location.href = Draw.nextURL;
            }, sec * 1000);
        }
        var conn = new WebSocket('ws:localhost:80');
        conn.onopen = function(e) {
            console.log("connection for comment established!");
            wsFlag = true;
        };
        conn.onmessage = function(e) {
            data = JSON.parse(e.data);
            //パートナーからの通知かどうかをチェック
            if (data['id'] == Draw.partnerID) {
                //judge関数からのsend()
                if (data['judge'] != undefined) {
                    XHR_score.open('POST', 'backend/score.php', true);
                    XHR_score.setRequestHeader('content-type',
                        'application/x-www-form-urlencoded;charset=UTF-8');
                    XHR_score.send("score=" + encodeURIComponent(JSON.stringify({
                        "diff": Draw.Qdiff,
                        "judge_result": data['judge'],
                        "Time": data['Time']
                    })));
                    if (data['judge'] == 1) countDown();
                    else window.location.href = "drawing.php?judge=0";
                }
                //changeTurn関数からのsend()
                else if (data['changeTurn']) {
                    XHR_turn.open('POST', 'backend/changeTurn.php', true);
                    XHR_turn.setRequestHeader('content-type',
                        'application/x-www-form-urlencoded;charset=UTF-8');
                    XHR_turn.send("afs=" + encodeURIComponent(e.data));
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
                correct = linesAns.every(function lines_check(ans) {
                    //(startX,startY,grad,length)
                    var flag2 = drawLines.some(function line_check(inp) {
                        grad = -(inp[3] - inp[1]) / (inp[2] - inp[0]);
                        leng = Draw.dist(inp[0] - inp[2], inp[1] - inp[3]);
                        var fA = Draw.judgeLine([inp[0], inp[1], grad, leng], ans);
                        var fB = Draw.judgeLine([inp[2], inp[3], grad, leng], ans);
                        return fA || fB;
                    });
                    return flag2;
                });
                correct = correct && circlesAns.every(function circles_check(ans) {
                    var flag2 = drawCircles.some(function circle_check(inp) {
                        return Draw.judgeCircle(inp, ans);
                    });
                    return flag2;
                })
                return correct;
            }
        }

        function judgeRequest() {
            if (iniSetFlag && wsFlag && Draw.turnFlag == 1) {
                var result = 0; //合ってたら１、間違ってたら０
                var t = new Date();
                startT = <?php echo $_SESSION['startT']; ?>;
                endT = parseInt(t.getTime() / 1000);
                var Time = endT - startT;
                if (judge()) result = 1;
                var json = JSON.stringify({
                    'judge': result,
                    'id': Draw.id,
                    'nextURL': Draw.nextURL,
                    'Time': Time
                });
                conn.send(json);
                XHR_score.open('POST', 'backend/score.php', true);
                XHR_score.setRequestHeader('content-type',
                    'application/x-www-form-urlencoded;charset=UTF-8');
                XHR_score.send("score=" + encodeURIComponent(JSON.stringify({
                    "diff": Draw.Qdiff,
                    "judge_result": result,
                    "Time": Time
                })));
                if (result == 1) countDown();
                else window.location.href = "drawing.php?judge=0";
            }
        }

        function undoDraw() {
            var Stack = Draw.addFigureStack.pop();
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
            }
        });
        </script>
    </body>

</html>