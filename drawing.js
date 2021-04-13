//図形をブラウザ画面上で描画、正誤判定するためのクラス
class drawing {
    constructor(stage) {
        this.stage = stage;
    }
    //各変数を定義するメソッド
    setData(iniData) {
        this.mode = iniData['mode'];
        this.id = iniData['id'];
        this.partnerID = iniData['partnerID'];
        this.partnerName = iniData['partnerName'];
        this.turnFlag = iniData['turnFlag'];
        this.Qtext = iniData['Qtext'];
        this.Qdiff = iniData['Qdiff'];
        this.drawLines = iniData['drawLines'];
        this.drawCircles = iniData['drawCircles'];
        this.ansLines = iniData['ansLines'];
        this.ansCircles = iniData['ansCircles'];
        this.nextURL = iniData['nextURL'];
        this.addLines = iniData['addLines'];
        this.addCircles = iniData['addCircles'];
        this.addFigureStack = iniData['addFigureStack'];
        this.currentQNum = iniData['currentQNum'];
        this.clickX = -1;
        this.clickY = -1;
    }
    //線を描画するメソッド
    //xyは[x1,y1,x2,y2]の形式の配列
    Line(xy, col = "#000", weight = 5) {
        var linePath = acgraph.path();
        linePath.parent(this.stage);
        linePath.moveTo(xy[0], xy[1]);
        linePath.lineTo(xy[2], xy[3]);
        linePath.stroke(col, weight);
        linePath.close();
    }
    //円を描画するメソッド
    //xyは[x,y,r]の形式の配列
    Circle(xy, col = "#000", weight = 5) {
        this.stage.circle(xy[0], xy[1], xy[2]).stroke(col, weight)
    }
    //ユークリッド距離の算出をするメソッド
    dist(dx, dy) { return parseInt(Math.sqrt(dx * dx + dy * dy)); }

    //マウスカーソルを動かしたときに呼ばれるメソッド
    mouseMove(cx2, cy2) {
        if (this.clickX != -1 && this.clickY != -1) {
            //線を引く
            if (this.mode == 1) {
                this.Circle([this.clickX, this.clickY, 5]);
                this.Line([this.clickX, this.clickY, cx2, cy2]);
            }
            //円を書く
            else {
                var R = this.dist(this.clickX - cx2, this.clickY - cy2);
                this.Circle([this.clickX, this.clickY, 5]);
                this.Circle([this.clickX, this.clickY, R]);
                //すでに描画されている円と半径を比較して、同じになったら線を赤くする
                for (var i = 0; i < this.addCircles.length; i++) {
                    var xy = this.addCircles[i];
                    if (parseInt(xy[2]) + 5 > R && xy[2] - 5 < R) {
                        this.Circle(xy, "#f00", 8);
                        this.Circle([this.clickX, this.clickY, R], "f00", 8);
                    }
                }
            }
        }
    }
    //マウスをクリックしたときに呼ばれるメソッド
    mouseClick(cx2, cy2) {
        //一回目のクリック
        if (this.clickX == -1 && this.clickY == -1) {
            this.clickX = cx2;
            this.clickY = cy2;
        }
        //二回目のクリック
        else {
            if (this.mode == 1) this.addLines.push([this.clickX, this.clickY, cx2, cy2]);
            else this.addCircles.push([this.clickX, this.clickY, this.dist(this.clickX - cx2, this.clickY - cy2)]);
            this.addFigureStack.push(this.mode);
            this.clickX = -1;
            this.clickY = -1;
        }
    }
    //線の正誤判定を行う
    judgeLine(inp, ans) {
        var flag = true;
        var ansX = parseInt(ans[0]);
        var ansY = parseInt(ans[1]);
        var ansG = parseInt(ans[2]);
        var ansL = parseInt(ans[3]);
        var inpX = parseInt(inp[0]);
        var inpY = parseInt(inp[1]);
        var inpG = parseInt(inp[2]);
        var inpL = parseInt(inp[3]);
        //length_check
        flag = flag && inpL > (ansL - 20);
        //angle_check
        var ans_ang = Math.atan(ansG); //ansGは傾き（tan）なのでatan関数を使って角度に変換する
        var inp_ang = Math.atan(inpG);
        flag = flag && (ans_ang - 0.1 < inp_ang);
        flag = flag && (inp_ang < ans_ang + 0.1);
        //startPoint_check
        if (ansX != -1 && ansY != -1) {
            flag = flag && ((ansX - 20) < inpX);
            flag = flag && (inpX < (ansX + 20));
            flag = flag && ((ansY - 20) < inpY);
            flag = flag && (inpY < (ansY + 20));
        }
        return flag;
    }
    //円の正誤判定を行う
    judgeCircle(inp, ans) {
        var ansX = parseInt(ans[0]);
        var ansY = parseInt(ans[1]);
        var ansR = parseInt(ans[2]);
        var inpX = parseInt(inp[0]);
        var inpY = parseInt(inp[1]);
        var inpR = parseInt(inp[2]);
        var flag = true;
        var err = 10;
        flag = flag && (ansX - err < inpX) && (inpX < ansX + err);
        flag = flag && (ansY - err < inpY) && (inpY < ansY + err);
        flag = flag && (ansR - err < inpR) && (inpR < ansR + err);
        return flag;
    }
}
