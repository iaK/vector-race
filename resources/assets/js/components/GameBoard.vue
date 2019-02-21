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
                <div>
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
            <race-course v-if="course"></race-course>
            <race-car v-for="car in cars" :car="car" :key="car.id"></race-car>
            <div id="pointers">
                <game-pointers v-if="cars && yourCar"></game-pointers>
            </div>
        </div>
        <message-board></message-board>
    </div>
</template>

<script>
    import EchoEvents from '../echoEvents.js'
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
            this.setupWatcher();

            this.hasLoaded = true;
            EchoEvents.initialize();
            Event.fire('rerender');
            Event.listen('fakeClick', (name, message) => {this.click(message)});
        },

        beforeDestroy() {
            EchoEvents.destroy();
            clearInterval(this.interval);
        },

        computed: {
            ...mapState(["ctx", "canvas", "path", "cars", "viewport", "course", "yourCar"]),

            loaded() {
                return this.hasLoaded;
            },

            bigScreen() {
                return parseInt(this.viewport.rule) >= 996;
            },

            smallScreen() {
                return ! this.bigScreen;
            },
        },

        methods: {
            ...mapMutations(["setCanvas", "setCtx", "setClick"]),

            setupWatcher() {
                this.interval = setInterval(() => {
                    Event.fire("rerender");
                }, 100);
            },
            click(e) {
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
        },
    }
</script>
