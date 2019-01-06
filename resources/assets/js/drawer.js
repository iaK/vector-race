module.exports = {

    config: null,
    canvas: null,
    ctx: null,
    color: 'orange',

    constructor(config, canvas, ctx) {
        this.config = config;
        this.canvas = canvas;
        this.ctx = ctx;
    },

    drawLine(start, end, color, lineWidth) {
        this.ctx.beginPath();
        this.ctx.lineWidth = lineWidth;
        this.ctx.strokeStyle = color;
        this.ctx.moveTo(start.x, start.y);
        this.ctx.lineTo(end.x, end.y);
        this.ctx.stroke();
    },
    setColor(color) {
        this.color = color;
    },

    drawShape(coordinates) {
        this.ctx.beginPath();

        let start = coordinates[0];

        this.ctx.moveTo(start.x, start.y);

        coordinates.forEach(function(value) {
            this.ctx.lineTo(value.x, value.y);
        }, this);

        this.ctx.closePath();
        this.ctx.lineWidth = 5;
        this.ctx.strokeStyle = this.color;
        this.ctx.stroke();
    },

    radToDeg(rad) {
        return rad * 180 / Math.PI;
    },

    speedToAngel(speed) {
        let rad = Math.atan(speed.top / speed.left) + 1.5707963268
        return speed.left < 0 ? rad + 3.1415926536 : rad;
    },

    drawCar(carLocation, speed) {
        angel = this.speedToAngel(speed);
        var img=document.getElementById("car");
        this.ctx.translate(carLocation.x, carLocation.y);
        this.ctx.rotate(angel);
        this.ctx.drawImage(img, - this.config.car.width / 2, - this.config.car.height / 2,this.config.car.width,this.config.car.height);
        this.ctx.rotate(- angel);
        this.ctx.translate(-carLocation.x, -carLocation.y);
    },
    
    drawLines(coordinates, color, strokeWidth) {
        this.ctx.beginPath();
        this.ctx.lineWidth = lineWidth;
        this.ctx.strokeStyle = color;

        this.ctx.moveTo(start.x, start.y);

        coordinates.forEach(function(value) {
            this.ctx.lineTo(value.x, value.y);
        }, this);

        this.ctx.stroke();
    },

    drawImage(img, width, height, rotateAngle, translate) {
        this.ctx.translate(translate.x, translate.y);
        this.ctx.rotate(rotateAngle);
        this.ctx.drawImage(img, - width / 2, - height / 2,width,height);
        this.ctx.rotate(- rotateAngle);
        this.ctx.translate(-translate.x, -translate.y);
    },


    drawGridX() {
        let start = this.config.gridWidthX;
        do {
            this.ctx.beginPath();
            this.ctx.lineWidth = this.config.gridLineWidthX;
            this.ctx.strokeStyle = "#DDD";
            this.ctx.moveTo(0, start);
            this.ctx.lineTo(this.canvas.width, start);
            this.ctx.stroke();
            start += this.config.gridWidthX;
        } while (start < this.canvas.width);
    },

    drawGridY() {
        let start = this.config.gridWidthY;
        do {
            this.ctx.beginPath();
            this.ctx.lineWidth = this.config.gridLineWidthY;
            this.ctx.strokeStyle = "#DDD";
            this.ctx.moveTo(start, 0);
            this.ctx.lineTo(start, this.canvas.height);
            this.ctx.stroke();
            start += this.config.gridWidthY;
        } while (start < this.canvas.height);
    },

    drawDirections(pointers) {
        let self = this;
        _(pointers).forEach(function(value, key) {
            self.drawPointer(value);
        });
    },

    drawPointer(pointer) {
        this.ctx.fillStyle = 'blue';
        this.ctx.fillRect(pointer.x - (this.config.pointers.width / 2),  pointer.y - (this.config.pointers.height / 2), this.config.pointers.height, this.config.pointers.width);
    },

}
