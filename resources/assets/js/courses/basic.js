module.exports = {
    car: {
        location: {x: 0, y: 0}
    },

    speed: {
        top: 0,
        left: -1,
    },

    drawer: null,
    ctx: null,
    canvas: null,
    inside: null,

    inner: [
        {x: 460, y: 720},
        {x: 300, y: 720},
        {x: 240, y: 680},
        {x: 260, y: 600},
        {x: 360, y: 540},
        {x: 420, y: 440},
        {x: 420, y: 340},
        {x: 380, y: 260},
        {x: 340, y: 160},
        {x: 260, y: 100},
        {x: 300, y: 80},
        {x: 480, y: 80},
        {x: 600, y: 120},
        {x: 580, y: 200},
        {x: 560, y: 280},
        {x: 540, y: 380},
        {x: 560, y: 500},
        {x: 620, y: 640},
        {x: 620, y: 700},
        {x: 580, y: 720},
    ],

    outer: [
        {x: 440, y: 820},
        {x: 260, y: 820},
        {x: 120, y: 780},
        {x: 60, y: 660},
        {x: 120, y: 540},
        {x: 240, y: 420},
        {x: 220, y: 260},
        {x: 100, y: 160},
        {x: 60, y: 80},
        {x: 200, y: 20},
        {x: 620, y: 20},
        {x: 760, y: 40},
        {x: 820, y: 120},
        {x: 780, y: 220},
        {x: 700, y: 280},
        {x: 700, y: 400},
        {x: 740, y: 520},
        {x: 800, y: 680},
        {x: 780, y: 780},
        {x: 680, y: 820},
    ],

    constructor(drawer, canvas, ctx) {
        this.drawer = drawer;
        this.canvas = canvas;
        this.ctx = ctx;
        this.car.location.x = this.canvas.width / 2;
        this.car.location.y = this.canvas.height - 200;
    },

    draw2DPath() {
        let path = new Path2D();
        let start = this.outer.shift();

        path.moveTo(start.x, start.y);

        this.outer.forEach(function(value) {
            path.lineTo(value.x, value.y);
        }, this);

        path.closePath();

        start = this.inner.shift();
        path.moveTo(start.x, start.y);

        this.inner.forEach(function(value) {
            path.lineTo(value.x, value.y);
        }, this);

        path.closePath();

        return path;
    },

    drawCourse(path) {
        this.ctx.lineWidth = 2;
        this.ctx.stroke(path);
    }
}

