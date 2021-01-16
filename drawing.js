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
        this.Qtext=iniData['Qtext'];
        this.Qdiff=iniData['Qdiff'];
        this.drawLines = iniData['drawLines'];
        this.drawCircles = iniData['drawCircles'];
        this.ansLines = iniData['ansLines'];
        this.ansCircles = iniData['ansCircles'];
        this.nextURL = iniData['nextURL'];
        this.addLines = iniData['addLines'];
        this.addCircles = iniData['addCircles'];
        this.addFigureStack=iniData['addFigureStack'];
        this.currentQNum=iniData['currentQNum'];
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
                var R=this.dist(this.clickX-cx2,this.clickY-cy2);
                for(var i=0;i<this.addCircles.length;i++){
                    var xy=this.addCircles[i];
                    if(parseInt(xy[2])+5>R && xy[2]-5<R){
                        console.log(xy);
                        this.Circle(xy,"#f00",8);
                        this.Circle([this.clickX, this.clickY, R],"f00",8);
                    }
                }
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
        var ansX=parseInt(ans[0]);
        var ansY=parseInt(ans[1]);
        var ansG=parseInt(ans[2]);
        var ansL=parseInt(ans[3]);
        var inpX=parseInt(inp[0]);
        var inpY=parseInt(inp[1]);
        var inpG=parseInt(inp[2]);
        var inpL=parseInt(inp[3]);
        flag = flag && inpL > (ansL - 20);
        //console.log("length_check -> " + flag);
        //angle_check
        var ans_ang = Math.atan(ansG);
        var inp_ang = Math.atan(inpG);
        flag=flag && (ans_ang - 0.1 < inp_ang);
        flag=flag && (inp_ang < ans_ang + 0.1);
        //console.log("ans_ang => "+ans_ang+"  ,  inp_ang => "+inp_ang);
        //console.log(ans_ang - 0.02 + " < " + inp_ang + " < " + ans_ang + 0.02);
        console.log("angle_check -> " + flag);
        //startPoint_check
        if(ansX!=-1&&ansY!=-1){
            flag=flag && ((ansX- 20) < inpX);
            flag=flag && (inpX < (ansX + 20));
            flag=flag && ((ansY - 20 )< inpY);
            flag=flag && (inpY < (ansY + 20));
            console.log("ans => "+ans+"  ,  inp => "+inp);
            console.log("startPoint_check -> " + flag);
        }
        return flag;
    }
    judgeCircle(inp, ans) {
        var ansX=parseInt(ans[0]);
        var ansY=parseInt(ans[1]);
        var ansR=parseInt(ans[2]);
        var inpX=parseInt(inp[0]);
        var inpY=parseInt(inp[1]);
        var inpR=parseInt(inp[2]);
        var flag = true;
        flag = flag && (ansX - 10 < inpX) && (inpX < ansX + 10);
        flag = flag && (ansY - 10 < inpY) && (inpY < ansY + 10);
        flag = flag && (ansR - 10 < inpR) && (inpR < ansR + 10);
        return flag;
    }
}
