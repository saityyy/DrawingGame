class drawing {
    constructor(stage) {
        this.stage = stage;
    }
    setData(iniData) {
        this.mode = iniData['mode'];
        this.id = iniData['id'];
        this.partnerID = iniData['partnerID'];
        this.turn_flag = iniData['turn_flag'];
        this.drawLines = iniData['drawLines'];
        this.drawCircles = iniData['drawCircles'];
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
    Circle(xy, col = "#000", weight = 5) { this.stage.circle(xy[0], xy[1], xy[2]).stroke(col, weight) }
    mouseMove(clickX, clickY, cx2, cy2) {
        if (clickX != -1 && clickY != -1) {
            if (mode) {
                Circle(clickX, clickY, 5);
                Line(clickX, clickY, cx2, cy2);
            } else {
                Circle(clickX, clickY, 5);
                Circle(clickX, clickY, dist(clickX - cx2, clickY - cy2));
            }
        }
    }
    mouseClick(cx2, cy2) {
        if (clickX == -1 && clickY == -1) {
            clickX = cx2;
            clickY = cy2;
        } else {
            if (mode) this.drawLines.push([clickX, clickY, cx2, cy2]);
            else this.drawCircles.push([clickX, clickY, dist(clickX - cx2, clickY - cy2)]);
            clickX = -1;
            clickY = -1;
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
