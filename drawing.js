class drawing {
    constructor(stage) {
        this.stage = stage;
    }
    setData(iniData) {
        this.mode = iniData['mode'];
        this.id = iniData['id'];
        this.partnerID = iniData['partnerID'];
        this.partnerName = iniData['partnerName'];
        this.turnFlag = iniData['turnFlag'];
        this.drawLines = iniData['drawLines'];
        this.drawCircles = iniData['drawCircles'];
        this.ansLines = iniData['ansLines'];
        this.ansCircles = iniData['ansCircles'];
        this.nextURL = iniData['nextURL'];
        this.addLines = iniData['addLines'];
        this.addCircles = iniData['addCircles'];
        this.addFigureStack=iniData['addFigureStack'];
        this.clickX = -1;
        this.clickY = -1;
    }
    Line(xy, col = "#000", weight = 5) {
        var linePath = acgraph.path();
        linePath.parent(this.stage);
        linePath.moveTo(xy[0], xy[1]);
        linePath.lineTo(xy[2], xy[3]);
        linePath.stroke(col, weight);
        linePath.close();
    }
    Circle(xy, col = "#000", weight = 5) { 
        this.stage.circle(xy[0], xy[1], xy[2]).stroke(col, weight) 
    }
    dist(dx, dy) { return parseInt(Math.sqrt(dx * dx + dy * dy)); }
    mouseMove(cx2, cy2) {
        if (this.clickX != -1 && this.clickY != -1) {
            if (this.mode==1) {
                this.Circle([this.clickX, this.clickY, 5]);
                this.Line([this.clickX, this.clickY, cx2, cy2]);
            } else {
                this.Circle([this.clickX, this.clickY, 5]);
                this.Circle([this.clickX, this.clickY, this.dist(this.clickX - cx2, this.clickY - cy2)]);
            }
        }
    }
    mouseClick(cx2, cy2) {
        if (this.clickX == -1 && this.clickY == -1) {
            this.clickX = cx2;
            this.clickY = cy2;
        } else {
            if (this.mode==1) this.addLines.push([this.clickX, this.clickY, cx2, cy2]);
            else this.addCircles.push([this.clickX, this.clickY, this.dist(this.clickX - cx2, this.clickY - cy2)]);
            this.addFigureStack.push(this.mode);
            this.clickX = -1;
            this.clickY = -1;
        }
    }

    judgeLine(inp, ans) {
        var flag = true;
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
    }
    judgeCircle(inp, ans) {
        var flag = true;
        flag = flag && (ans[0] - 10 < inp[0]) && (inp[0] < ans[0] + 10);
        flag = flag && (ans[1] - 10 < inp[1]) && (inp[1] < ans[1] + 10);
        flag = flag && (ans[2] - 10 < inp[2]) && (inp[2] < ans[2] + 10);
        return flag;
    }
}
