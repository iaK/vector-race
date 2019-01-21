<template>
    <div class="h-full lg:flex lg:items-center lg:justify-center z-10">
        <div class="fixed pin-t pin-r" ref="right">
            <toggle-icon
                open="chatbox"
                icon="far fa-comment"
                class="mt-20 xl:hidden"
            ></toggle-icon>
        </div>
        <div class="fixed pin-r pin-t" ref="rightSide">
            <div class="xl:mr-5 xl:mt-20 flex justify-end flex-wrap" style="width: 400px">
                <div class="">
                    <div class="absolute xl:relative pin-t pin-r">
                        <countdown-board></countdown-board>
                    </div>
                    <div class="absolute xl:relative xl:mt-0 pin-t pin-r">
                        <chat-box v-if="bigScreen" class="w-full"></chat-box>
                        <modal
                            name="chatbox"
                            :adaptive="true"
                            :scrollable="true"
                            height="auto"
                            classes="bg-transparent px-4"
                        >
                            <chat-box
                                v-if="smallScreen"
                                class="w-full"
                                :adaptive="true"
                                :scrollable="true"
                                height="auto"
                            ></chat-box>
                        </modal>
                    </div>
                </div>
            </div>
        </div>

        <canvas @click="click" ref="canvas" height="960" width="960"></canvas>

        <div class="fixed pin-t pin-l" ref="right">
            <toggle-icon
                open="scoreboard"
                icon="far fa-space-shuttle"
                class="mt-4 xl:hidden"
            ></toggle-icon>
            <toggle-icon
                open="player-controls"
                icon="far fa-gamepad"
                class="mt-4 xl:hidden"
            ></toggle-icon>
        </div>
        <div class="fixed pin-l pin-t" ref="leftSide">
            <div class="m-5">
                <div class="absolute xl:relative pin-t pin-l">
                    <score-board v-if="bigScreen" :cars="cars" class="mb-10"></score-board>
                    <modal
                        name="scoreboard"
                        :adaptive="true"
                        :scrollable="true"
                        height="auto"
                        classes="bg-blue-trans-lighter p-5 border border-1 border-blue-lightest px-4"
                    >
                        <score-board v-if="smallScreen" :cars="cars" class="mb-10"></score-board>
                    </modal>
                </div>
                <div class="absolute xl:relative pin-t pin-l">
                    <player-controls v-if="bigScreen"></player-controls>
                    <modal
                        name="player-controls"
                        :adaptive="true"
                        :scrollable="true"
                        height="auto"
                        classes="bg-blue-trans-lighter p-5 border border-1 border-blue-lightest px-4"
                    >
                        <player-controls v-if="smallScreen"></player-controls>
                    </modal>
                </div>

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
            ...mapState(["ctx", "config", "canvas", "path", "cars", 'yourCar', 'state','winner','race', "viewport"]),
            ...mapGetters(["currentCar", "yourTurn", "notInRace"]),

            loaded() {
                return this.hasLoaded;
            },

            bigScreen() {
                if (parseInt(this.viewport.rule) <= 995) {
                    return false
                }
                return true;
            },

            smallScreen() {
                return ! this.bigScreen;
            },
        },

        methods: {
            ...mapMutations(["setCanvas", "setCtx", "setCourse","setClick", "setCurrentCar", "setYourCar", "changeRaceState", "setOnline", "setOffline", "setState",'setCars', 'setNotInRace']),
            ...mapActions(['moveCurrentCar', "changeTurn", "startRace", "calculateNewPointers", "leaveGame", "win", "fail"]),


            setupWatcher() {
                this.interval = setInterval(() => {
                    Event.fire("rerender");
                }, 1000);
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
    canvas {
        overflow: scroll;
        -webkit-overflow-scrolling: touch;
        width: 960px;
        height: 960px;
    }
    .bg {
        background-image: url("/img/track.jpg");
        background-size: cover;
    }
</style>
