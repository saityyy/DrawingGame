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
    }
    draw() {
        console.log(this.drawLines);
        for (var i = 0; i < this.drawLines.length; i++) {
            var xy = this.drawLines[i];
            var linePath = acgraph.path();
            linePath.parent(this.stage);
            linePath.moveTo(xy[0], xy[1]);
            linePath.lineTo(xy[2], xy[3]).fill("#000").strokeThickness(5);
            linePath.close();
        }
        for (var i = 0; i < this.drawCircles.length; i++) {
            var xy = this.drawCircles[i];
            this.stage.circle(xy[0], xy[1], xy[2]);
        };
    }
    Line(x1, y1, x2, y2) {
        var linePath = acgraph.path();
        linePath.parent(this.stage);
        linePath.moveTo(x1, y1);
        linePath.lineTo(x2, y2).fill("#000").strokeThickness(5);
        linePath.close();
    }
    Circle(x1, y1, r) { this.stage.circle(x1, y2, r); }
    mouseMove(clickX, clickY) {
        this.stage.rect(0, 0, 900, 600).fill('white');
        if (clickX != -1 && clickY != -1) {
            if (mode) {
                Circle(clickX, clickY, 5).fill("black");
                Line(clickX, clickY, e.offsetX, e.offsetY);
            } else {
                Circle(clickX, clickY, 5).fill("black");
                Circle(clickX, clickY, dist(clickX - e.offsetX, clickY - e.offsetY));
            }
        }
        draw(drawLines, drawCircles);
    }
    mouseClick(clickX, clickY) {
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
    }
}
