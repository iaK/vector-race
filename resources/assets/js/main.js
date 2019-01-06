var $ = jQuery = require("jquery");
window.$ = $;
var _ = require("lodash");
var ctx = require('./canvas').ctx;
var canvas = require('./canvas').canvas;

// ctx.imageSmoothingEnabled = false;

var config = {
    gridWidthX: 20,
    gridWidthY: 20,
    gridLineWidthY: 2,
    gridLineWidthX: 2,

    pointers: {
        width: 10,
        height: 10,
    },

    trace: {
        width: 1,
    },

    car: {
        width: 20,
        height: 20,
    }

}

var course = require("./courses/basic.js");
var drawer = require("./drawer.js");
drawer.constructor(config, canvas, ctx);
course.constructor(drawer, canvas, ctx);
var path = course.draw2DPath();

var game = {
    pointers: {
        directions: {x: 0, y:0},
        location: {
            middle: {x: 0, y: 0},
            left: {x: 0, y: 0},
            right: {x: 0, y: 0},
            bottom: {x: 0, y: 0},
            top: {x: 0, y: 0},
            topLeft: {x: 0, y: 0},
            topRight: {x: 0, y: 0},
            bottomLeft: {x: 0, y: 0},
            bottomRight: {x: 0, y: 0},
        }
    },

    speed: course.speed,

    car: {
        location: course.car.location,
        trace: [
            {x: 0, y: 0},
        ]
    },
}
game.car.location = {x: canvas.width / 2, y: canvas.height - 200};
game.car.trace = [_(game.car.location).clone()];
game.pointers.directions = {x: game.car.location.x + (config.gridWidthX * game.speed.left), y: game.car.location.y + (config.gridWidthY * game.speed.top)};
getPointers(game.pointers.directions);

function setCanvasForRetina(){
    if(devicePixelRatio && devicePixelRatio > 1) {
        canvas.width = canvas.width * devicePixelRatio;
        canvas.height = canvas.height * devicePixelRatio;
        ctx.imageSmoothingEnabled = false;
        ctx.setTransform(2,0,0,2,0,0);
    }
}

function getRelativeMousePosition(event, target) {
  target = target || event.target;
  var rect = target.getBoundingClientRect();

  return {
    x: event.clientX - rect.left,
    y: event.clientY - rect.top,
  }
}

// assumes target or event.target is canvas
function getNoPaddingNoBorderCanvasRelativeMousePosition(event, target) {
  target = target || event.target;
  var pos = getRelativeMousePosition(event, target);

  pos.x = (pos.x * target.width  / target.clientWidth) / (devicePixelRatio && devicePixelRatio > 1 ? 2 : 1);
  pos.y = (pos.y * target.height / target.clientHeight) / (devicePixelRatio && devicePixelRatio > 1 ? 2 : 1);

  return pos;
}



$(canvas).on("click", function(e) {
    let x = e.clientX - $(canvas).position().left;
    let y = e.clientY - $(canvas).position().top;
    let inside = ctx.isPointInPath(path, x, y, "evenodd");
    let r = this.getBoundingClientRect()
    let coor = {
            x: e.clientX - r.left,
            y: e.clientY - r.top
    }

    if(detectClick(coor)) {
        if (!inside) {
            console.log("FAIL!");
        }
        game.car.location = moveLocations(game.car.location);
        saveTrace();

        game.pointers.directions.x = game.car.location.x;
        game.pointers.directions.y = game.car.location.y;
        game.pointers.directions = moveLocations(game.pointers.directions);
        getPointers(game.pointers.directions);
    }
});

function saveTrace() {
    game.car.trace.push({x: game.car.location.x, y: game.car.location.y});
}

function moveLocations(location) {
    location.x += game.speed.left * config.gridWidthX;
    location.y += game.speed.top * config.gridWidthY;
    return location;
}

function getPointers(location) {
    let halfPaddingX = config.gridWidthX / 2;
    let halfPaddingY = config.gridWidthY / 2;

    game.pointers.location.middle.x = location.x;
    game.pointers.location.middle.y = location.y;
    game.pointers.location.left.x = location.x - (halfPaddingX * 2);
    game.pointers.location.left.y = location.y;
    game.pointers.location.right.x = location.x + (halfPaddingX * 2);
    game.pointers.location.right.y = location.y;
    game.pointers.location.top.x = location.x;
    game.pointers.location.top.y = location.y - (halfPaddingY * 2);
    game.pointers.location.bottom.x = location.x;
    game.pointers.location.bottom.y = location.y + (halfPaddingY * 2);
    game.pointers.location.topLeft.x = location.x - (halfPaddingX * 2);
    game.pointers.location.topLeft.y = location.y - (halfPaddingY * 2);
    game.pointers.location.topRight.x = location.x + (halfPaddingX * 2);
    game.pointers.location.topRight.y = location.y - (halfPaddingY * 2);
    game.pointers.location.bottomLeft.x = location.x - (halfPaddingX * 2);
    game.pointers.location.bottomLeft.y = location.y + (halfPaddingY * 2);
    game.pointers.location.bottomRight.x = location.x + (halfPaddingX * 2);
    game.pointers.location.bottomRight.y = location.y + (halfPaddingY * 2);
}

function detectClick(coor) {
    if(isClicked(coor, game.pointers.location.middle)) {
        return true;
    }
    if(isClicked(coor, game.pointers.location.left)) {
        game.speed.left -= 1;
        return true;
    }
    if(isClicked(coor, game.pointers.location.right)) {
        game.speed.left += 1;
        return true;
    }
    if(isClicked(coor, game.pointers.location.top)) {
        game.speed.top -= 1;
        return true;
    }
    if(isClicked(coor, game.pointers.location.bottom)) {
        game.speed.top += 1;
        return true;
    }
    if(isClicked(coor, game.pointers.location.topLeft)) {
        game.speed.left -= 1;
        game.speed.top -= 1;
        return true;
    }
    if(isClicked(coor, game.pointers.location.bottomLeft)) {
        game.speed.left -= 1;
        game.speed.top += 1;
        return true;
    }
    if(isClicked(coor, game.pointers.location.topRight)) {
        game.speed.left += 1;
        game.speed.top -= 1;
        return true;
    }
    if(isClicked(coor, game.pointers.location.bottomRight)) {
        game.speed.left += 1;
        game.speed.top += 1
        return true;
    }
    return false;
}

function isClicked(coor, coordinates) {
    if(
        coor.x < coordinates.x + (config.pointers.width / 2)
        && coor.x > coordinates.x - (config.pointers.width / 2)
        && coor.y < coordinates.y + (config.pointers.height / 2)
        && coor.y > coordinates.y - (config.pointers.height / 2)
    ) {
        return true;
    }

    return  false;
}

let interval = setInterval(function() {
    clearCanvas();
    drawer.drawGridY();
    drawer.drawGridX();
    drawer.drawTrace(game.car.trace);
    drawer.drawDirections(game.pointers.location);
    drawer.drawCar(game.car.location, game.speed);
    course.drawCourse(path);
}, 100)


window.onmousemove = function(e) {
    let x = e.clientX - $(canvas).position().left;
    let y = e.clientY + window.scrollY;
    ctx.strokeStyle = 'blue';//ctx.isPointInPath(path, x, y, "evenodd") ? "red" : "#000";
    ctx.stroke(path);
};


function checkXAxis() {
    let x = _(course.inner).filter(coordinate => {
        return coordinate.x == game.car.location.x;
    }).sortBy(coordinate => coordinate.x).value();
}


function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}
