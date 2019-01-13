<template>
    <div class="h-full flex items-center justify-center z-10">

        <div class="fixed pin-r pin-t">
            <div class="p-10 flex justify-end flex-wrap" style="max-width: 400px">
                <div class="pt-10">
                    <countdown-board></countdown-board>
                    <chat-box></chat-box>
                </div>
            </div>
        </div>

        <canvas @click="click" ref="canvas" height="960" width="960"></canvas>

        <div class="fixed pin-l pin-t">
            <div class="px-5 py-5 m-5">
                <score-board :cars="cars" class="mb-10"></score-board>
                <player-controls></player-controls>
            </div>
        </div>

        <div v-if="hasLoaded">
            <game-course></game-course>
            <game-car v-for="car in cars" :car="car" :key="car.id"></game-car>
            <game-pointers></game-pointers>
        </div>
        <message-board></message-board>
    </div>
</template>

<script>
    import { mapMutations, mapState, mapActions, mapGetters } from 'vuex';
    import axios from 'axios';

    export default {
        data() {
            return {
                hasLoaded: false,
                interval: null,
            }
        },

        mounted() {
            this.setCanvas(this.$refs.canvas);
            this.setCtx(this.$refs.canvas.getContext("2d"));
            this.setCanvasForRetina();
            this.setupWatcher();

            this.hasLoaded = true;

            Echo.join(`race.${this.race}`)
                .joining((user) => {
                    Event.fire("Joining", user);
                    if (this.notInRace(user)) {
                        this.setCars([user]);
                    }
                    this.setOnline(user);
                })
                .leaving((user) => {
                    Event.fire("Leaving", user);
                    console.log("left", user)

                    this.setOffline(user)
                })
                .here((users) => {
                    console.log("here", users)
                    Event.fire("Here", users);
                    users.forEach((user) => {
                        this.setOnline(user)
                    })
                })
                .listen('CarMoved', (e) => {
                    console.log("CarMoved", e)
                    Event.fire("CarMoved", e);

                    this.moveCurrentCar({
                        id: e.id,
                        location: e.location,
                        speed: e.speed,
                    });
                })
                .listen('PlayerKicked', (e) => {
                    console.log("PlayerKicked");
                    Event.fire("PlayerKicked", e);
                    if (e.user.id == this.yourCar) {
                        this.leaveGame();

                        Event.fire('show-result-board', {
                            heading: "Kicked",
                            text: "You got kicked. Sorry.",
                            buttons: [
                                {
                                    text: "Ok..",
                                    func: () => {
                                        this.$modal.hide('result-board');
                                    }
                                }
                            ]
                        })
                    }
                })
                .listen('RaceStarted', (e) => {
                    console.log("Race started", e.race);
                    this.changeRaceState(e.race.status);
                    Event.fire("RaceStarted", e);
                    Event.fire('messageBoard',{message: 'Game started'});
                })
                .listen('PlayerWon', (e) => {
                    Event.fire("PlayerWon", e);

                    if (e.user.id == this.yourCar) {
                        return this.win()
                    }
                    return this.fail();
                })
                .listen('TurnChanged', (e) => {
                    console.log("TurnChanged", e)
                    this.changeTurn(e.car);
                    Event.fire('TurnChanged', e)
                })
                .listen("PlayerLeft", (e) => {
                    console.log("PlayerLeft");
                    this.setNotInRace(e.user);
                    Event.fire("PlayerLeft", e);
                })
                .listen("GameClosed", (e) => {
                    this.leaveGame();
                    Event.fire('show-result-board', {
                            heading: "Game closed",
                            text: "The host left the game... Darn it!",
                            buttons: [
                                {
                                    text: "Ok..",
                                    func: () => {
                                        this.leaveGame();
                                        this.$modal.hide('result-board');
                                    }
                                }
                            ]
                        })
                        .fire("GameClosed", e);

                })
                .listen('MessagePosted', (e) => {
                    Event.fire("MessagePosted", e);
                });

            Event.fire('rerender');
        },

        beforeDestroy() {
            clearInterval(this.interval);
        },

        computed: {
            ...mapState(["ctx", "config", "canvas", "path", "cars", 'yourCar', 'state','winner','race']),
            ...mapGetters(["currentCar", "yourTurn", "notInRace"]),

            loaded() {
                return this.hasLoaded;
            }
        },

        methods: {
            ...mapMutations(["setCanvas", "setCtx", "setCourse","setClick", "setCurrentCar", "setYourCar", "changeRaceState", "setOnline", "setOffline", "setState",'setCars', 'setNotInRace']),
            ...mapActions(['moveCurrentCar', "changeTurn", "startRace", "calculateNewPointers", "leaveGame", "win", "fail"]),

            setupWatcher() {
                this.interval = setInterval(() => {
                    Event.fire("rerender");
                }, 10);
            },
            click(e) {
                let x = e.clientX - $(this.$refs.canvas).position().left;
                let y = e.clientY - $(this.$refs.canvas).position().top;

                let r = this.canvas.getBoundingClientRect()
                let inside = this.ctx.isPointInPath(this.path, e.clientX - r.left, e.clientY - r.top, "evenodd");

                let clickLocation = {
                    x: e.clientX - r.left,
                    y: e.clientY - r.top,
                    inside,
                    event: e,
                }

                this.setClick(clickLocation);
            },
            setCanvasForRetina(){
                // if(devicePixelRatio && devicePixelRatio > 1) {
                //     this.$refs.canvas.width = this.$refs.canvas.width * devicePixelRatio;
                //     this.$refs.canvas.height = this.$refs.canvas.height * devicePixelRatio;
                //     this.ctx.imageSmoothingEnabled = false;
                //     this.ctx.setTransform(2,0,0,2,0,0);
                // }
            },
        },
    }
</script>

<style>
.con {
        width: 960px;
        height: 960px;

}
    .bg {
        background-image: url("/img/track.jpg");
        background-size: cover;
    }

    canvas {
        margin-top: 20px;
    }
</style>
