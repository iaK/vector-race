import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import router from '../router.js';

Vue.use(Vuex);

function intersects(a,b,c,d,p,q,r,s) {
  var det, gamma, lambda;
  det = (c - a) * (s - q) - (r - p) * (d - b);
  if (det === 0) {
    return false;
  } else {
    lambda = ((s - q) * (r - a) + (p - r) * (s - b)) / det;
    gamma = ((b - d) * (r - a) + (c - a) * (s - b)) / det;
    return (0 < lambda && lambda < 1) && (0 < gamma && gamma < 1);
  }
};

export default new Vuex.Store({
    state: {
        ...DATA,
        gameState: "lobby",
        canvas: null,
        ctx: null,
        winner: null,
        host: null,
        course: null,
        click: {},
        path: null,
        race: null,
        races: [],
        createdGame: null,
        pointers: {
            baseLocation: {x: 0, y:0},
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
        currentCarId: null,
        yourCar: null,
        cars: [],
        config: {
            gridWidthX: 20,
            gridWidthY: 20,
            gridLineWidthY: 2,
            gridLineWidthX: 2,

            pointers: {
                width: 10,
                height: 10,
            },

            trace: {
                width: 3,
            },

            car: {
                width: 20,
                height: 20,
            }
        },
    },
    mutations: {
        updateRace(state, race) {
            state.races.forEach((r, index) => {
                if (r.id == race.id) {
                    r.participant_data = race.participant_data;
                    state.races.splice(index,1, r);
                }
            });
        },
        removeRace(state, race) {
            state.races.forEach((r, index) => {
                if (r.id == race.id) {
                    state.races.splice(index,1);
                }
            });
        },
        addRace(state, race) {
            let races = state.races.filter((r) => {
                return r.id == race.id;
            });

            if (races.length == 0) {
                state.races.push(race);
            }
        },
        setKicked(state, user) {
            state.cars.forEach((car, index) => {
                if (car.id == user.id) {
                    car.inRace = false,
                    state.cars.splice(index,1, car);
                }
            });
        },
        setOnline(state, user) {
            state.cars.forEach((car, index) => {
                if (car.id == user.id) {
                    car.online = true,
                    state.cars.splice(index,1, car);
                }
            });
        },
        setOffline(state, user) {
            state.cars.forEach((car, index) => {
                if (car.id == user.id) {
                    car.online = false,
                    state.cars.splice(index,1, car);
                }
            });
        },
        setNotInRace(state, user) {
            state.cars.forEach((car, index) => {
                if (car.id == user.id) {
                    if (state.gameState == "lobby") {
                        state.cars.splice(index,1);
                    } else {
                        car.inRace = false;
                        car.present = false;
                        state.cars.splice(index,1, user);
                    }
                }
            });
        },
        setWinner(state, winner) {
            state.winner = winner;
        },
        setState(state, gameState) {
            state.gameState = gameState;
        },
        setRace(state, race) {
            state.race = race;
        },
        addTrace(state, trace) {
            state.cars.forEach((car, index) => {
                if (car.id == trace.id) {
                    car.trace.push(trace.trace);
                    state.cars.splice(index,1, car);
                }
            });
        },
        moveCar(state, data) {
            state.cars.forEach((car, index) => {
                if (car.id == data.id) {
                    car.location = data.location;
                    car.speed = data.speed;
                    car.trace.push(data.location);
                    state.cars.splice(index,1, car);
                }
            });
        },
        setCar(state, car) {
            state.cars.push(car);
        },
        setCanvas(state, canvas) {
            state.canvas = canvas
        },
        setCtx(state, ctx) {
            state.ctx = ctx;
        },
        setRelativePointerDirections(state, x,y) {
            state.pointers.directions = {
                x: state.car.location.x + (state.config.gridWidthX * state.speed.left),
                y: state.car.location.y + (state.config.gridWidthY * state.speed.top)
            }
        },
        setCourse(state, course) {
            state.course = course;
        },
        setHost(state, host) {
            state.host = host;
        },
        setCoursePath(state, path) {
            state.path = path;
        },
        setPointers(state, pointers) {
            state.pointers.location = pointers;
        },
        setPointer(state, pointer) {
            state.pointers.location[pointer.pos] = pointer
        },
        setClick(state, click) {
            state.click = click;
        },
        setCurrentCar(state, id) {
            state.currentCarId = id;
        },
        setYourCar(state, id) {
            state.yourCar = id;
        },
        changeRaceState(state, gameState) {
            state.gameState = gameState;
        },
        clearTurn(state) {
            state.currentCarId = null;
        },
        setGameEnded(state) {
            state.gameState = 'ended';
        },
        setCars(state, cars) {
            cars.forEach((car) => {
                state.cars.push({
                    id: car.id,
                    location: car.trace && car.trace.length > 1 ? car.trace[car.trace.length -1] : state.course.starting_point,
                    speed: car.speed ? car.speed : state.course.starting_speed,
                    name: car.username,
                    trace: car.trace && car.trace.length > 1 ? car.trace : [state.course.starting_point],
                    traceColor: car.trace_color,
                    carColor: car.car_color,
                    present: true,
                    online: car.online,
                    inRace: true,
                });
            })
        },
        resetGame(state) {
            Echo.leave(`race.${state.race}`);
            state.course = null;
            state.currentCarId = null;
            state.gameState = "lobby";
            state.cars = [];
            state.winner = null;
            state.host = null;
            state.race = null;
        },
        setCreatedGame(state, raceId) {
            state.createdGame = raceId;
        },
        setRaces(state, races) {
            state.races = races;
        },
        setUser(state, user) {
            state.user = user;
        },
    },
    actions: {
        kickCar(context, car) {
            axios.post(`/race/${context.state.race}/kick/${car.id}`).then(({data}) => {
                context.commit('setKicked', data.data);
            });
        },
        getRaces(context) {
            axios.get("/races").then(({data}) => {
                context.commit('setRaces', data.data);
            });
        },
        createGame(context, courseId) {
            return axios.post("/race", {course_id: courseId}).then((e) => {
                context.commit("setCreatedGame", e.data.data.id);
            });
        },
        leaveGame(context) {
            axios.post(`/race/${context.state.race}/leave`).then(({data}) => {
                context.commit('resetGame');
                context.commit('removeRace', data.data)
                router.replace({name: `lobby`});
            })
        },
        changeTurn(context, id) {
            context.commit("setCurrentCar", id);
        },
        skip(context) {
            axios.post(`/race/${context.state.race}/skip`);
        },
        addToTrace(context, trace) {
            context.commit("addTrace", {
                id: context.getters.currentCar.id,
                trace: trace,
            });
        },
        sendClick(context, car) {
            axios.post(`/race/${context.state.race}/move`, car);
        },
        moveCurrentCar(context, car) {
            context.commit('moveCar', {
                id: car.id,
                location: {
                    x: car.location.x,
                    y: car.location.y,
                },
                speed: {
                    left: car.speed.left,
                    top: car.speed.top
                }
            });
        },

        calculateNewPointers(context, location) {
            let loc = JSON.parse(JSON.stringify(location.location));
            loc.x += location.speed.left * context.state.config.gridWidthX;
            loc.y += location.speed.top * context.state.config.gridWidthY;

            context.commit("setPointers", {
                'middle': {x: loc.x,y: loc.y},
                'left': {x: loc.x - context.state.config.gridWidthX, y: loc.y},
                'right': {x: loc.x + context.state.config.gridWidthX, y: loc.y},
                'top': {x: loc.x,y: loc.y - context.state.config.gridWidthY},
                'bottom': {x: loc.x,y: loc.y + context.state.config.gridWidthY},
                'topLeft': {x: loc.x - context.state.config.gridWidthX, y: loc.y - context.state.config.gridWidthY},
                'topRight': {x: loc.x + context.state.config.gridWidthX, y: loc.y - context.state.config.gridWidthY},
                'bottomLeft': {x: loc.x - context.state.config.gridWidthX, y: loc.y + context.state.config.gridWidthY},
                'bottomRight': {x: loc.x + context.state.config.gridWidthX, y: loc.y + context.state.config.gridWidthY},
            });
        },
        drawLine(context, obj) {
            context.state.ctx.beginPath();
            context.state.ctx.lineWidth = obj.lineWidth;
            context.state.ctx.strokeStyle = obj.color;
            context.state.ctx.moveTo(obj.start.x, obj.start.y);
            context.state.ctx.lineTo(obj.end.x, obj.end.y);
            context.state.ctx.stroke();
        },
        failIfOutsideCourse(context, car) {
            if (! context.state.click.inside) {
                context.dispatch("fail", "outside course");
                context.commit("setGameEnded");
                return axios.post(`/race/${context.state.race}/fail`, {"reason": "Outside couse", ...car});
            }
        },
        checkIfCross(context) {
            let car = context.getters.currentCar;
            console.log(car);
            let lineStart = {};
            let lineEnd = {};

            lineStart.x = car.trace[car.trace.length -1].x;
            lineStart.y = car.trace[car.trace.length -1].y;

            lineEnd.x = car.trace[car.trace.length -2].x;
            lineEnd.y = car.trace[car.trace.length -2].y;

            let goalLine = context.state.course.finish_line;

            if (intersects(lineStart.x, lineStart.y, lineEnd.x, lineEnd.y, goalLine.start.x, goalLine.start.y, goalLine.end.x, goalLine.end.y)) {
                if (car.speed.left < 0) {
                    axios.post(`/race/${context.state.race}/win`, car);
                    context.dispatch("win");
                } else {
                    axios.post(`/race/${context.state.race}/fail`, {"reason": "Cross from wrong side", ...car});
                    context.dispatch("fail", "Cross from wrong side");
                }
            }
        },
        startRace(context) {
            axios.post(`/race/${context.state.race}/start`,{cars: context.state.cars});
        },
        joinGame(context, race) {
            axios.post(`/race/${race}/join`).then(({data}) => {
                context.commit("setCourse", data.course);
                context.commit("setYourCar", data.user_id);
                context.commit('setCurrentCar', data.user_turn_id);
                context.commit('setState', data.state);
                context.commit('setCars', data.participants);
                context.commit('setWinner', data.winner_id);
                context.commit('setHost', data.host_id);
                context.commit('setRace', data.id);
            });
        },
        getRaceBaseInfo(context, race) {
            return axios.get(`/race/${race}/info`).then(({data}) => {
                context.commit("setCourse", data.course);
                context.commit("setYourCar", data.user_id);
                context.commit('setCurrentCar', data.user_turn_id);
                context.commit('setState', data.state);
                context.commit('setCars', data.participants);
                context.commit('setWinner', data.winner_id);
                context.commit('setHost', data.host_id);
            });
        },
        fail(context, reason) {
            context.commit("setGameEnded");
            Event.fire("show-result-board", {
                heading: "Fail!",
                text: "That's a loose I'm afraid. Better luck next time",
                buttons: [
                    {
                        text: "Lobby",
                        func: () => {
                            context.dispatch("leaveGame");
                            Event.fire('hide-result-board');
                        }
                    },
                    {
                        text: "Spectate",
                        func: () => {
                            Event.fire('hide-result-board');
                        }
                    }
                ],
            });
        },
        win(context, reason) {
            context.commit("setGameEnded");
            Event.fire("show-result-board", {
                heading: "Win!",
                text: "Good job! Your awesome! :)",
                buttons: [
                    {
                        text: "Lobby",
                        func: () => {
                            context.dispatch("leaveGame");
                            Event.fire('hide-result-board');
                        }
                    }
                ],
            });
        },
    },
    getters: {
        notInRace(state) {
            return (user) => {
                return state.cars.filter((car) => {
                    return car.id == user.id;
                }).length == 0.
            }
        },
        car(state) {
            return (id) => {
                return state.cars.filter((car) => {
                    return car.id == id;
                })[0];
            }
        },
        currentCar(state, getters) {
            return state.cars.filter((car) => {
                return car.id == state.currentCarId;
            })[0];
        },
        // nextCar(state, getters) {
        //     let i = 0;
        //     state.cars.forEach((car, index) => {
        //         if (car.id == state.currentCarId) {
        //             i = index + 1;
        //         }
        //     });
        //     return state.cars[(i >= state.cars.length ? 0 : i)];
        // },
        yourTurn(state, getters) {
            return state.currentCarId == state.yourCar;
        }
    }
});
