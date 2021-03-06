<template>
    <div class="max-w-md mx-auto pt-8 px-4">
        <h1 class="text-white mb-4">Lobby</h1>
        <div class="p-2 bg-blue mb-4">
            <div
                class="bg-blue-darker w-full p-2 overflow-y-scroll scrollbar"
                style="height: 230px;"
                ref="raceContainer"
            >
                <ul class="list-reset" ref="raceList">
                    <li
                        v-for="(race, index) in races"
                        class="mb-2 text-white px-2 py-1 cursor-pointer"
                        :class="{'bg-blue-lighter': isMarked(index)}"
                        @click="mark(index)"
                        @dblclick="join"
                    >
                        {{ race.host.username }} <i>({{ race.participants.length }} / 4)</i>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sm:flex sm:justify-between sm:flex-wrap">
            <div class="mb-4 flex-grow flex w-full sm:w-auto">
                <button class="btn mr-2 ml-2" @click="join">
                    Join Game
                </button>
                <button dusk="create-game-button" class="btn" @click="create">
                    Create game
                </button>
            </div>
            <div class="w-full sm:w-auto">
                <button class="btn mr-2" @click="howto">
                    How to play
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapActions, mapState, mapMutations } from 'vuex'

    export default {

        data() {
            return {
                marked: null,
                loaded: false,
                watcher: null,
            }
        },

        mounted() {
            this.getRaces();

            window.addEventListener('keyup', this.setEventHandlers);
            window.addEventListener('keydown', this.preventScrollOnKeyDown);

            this.watcher = this.$store.watch(
                (state) => {
                    return this.$store.state.races;
                },
                (val) => {
                    this.loaded = true;
                }
            );

            Echo.channel(`races`)
                .listen('PlayerJoined', (e) => {
                    this.updateRace(e.race);
                })
                .listen('PlayerLeft', (e) => {
                    this.updateRace(e.race);
                })
                .listen('GameClosed', (e) => {
                    this.removeRace(e.race);
                })
                .listen('RaceCreated', (e) => {
                    this.addRace(e.race);
                })
                .listen('RaceStarted', (e) => {
                    this.removeRace(e.race);
                });

            window.addEventListener("resize", (e) => {
                console.log(window.width);
            })
        },

        beforeDestroy() {
            window.removeEventListener('keyup', this.setEventHandlers);
            window.removeEventListener('keydown', this.preventScrollOnKeyDown);
            this.watcher();
        },

        computed: {
            ...mapState(["races"]),
            uniqueRaces() {
                return [...new Set(this.races.map(race => race.id))];
            }
        },

        methods: {
            ...mapMutations(["updateRace", "removeRace", "addRace"]),
            ...mapActions(["getRaceBaseInfo", "getRaces"]),

            preventScrollOnKeyDown(event) {
                 if (event.keyCode == 40 || event.keyCode == 38) {
                    event.preventDefault();
                }
            },
            setEventHandlers(event) {
                if (event.keyCode == 40) {
                    this.down();
                    this.scrollIntoViewIfNeeded(this.$refs.raceList.childNodes[this.marked]);
                }
                if (event.keyCode == 38) {
                    this.up();
                    this.scrollIntoViewIfNeeded(this.$refs.raceList.childNodes[this.marked]);
                }
                if (event.keyCode == 13) {
                    this.join();
                }
            },
            mark(index) {
                this.marked = index;
            },
            isMarked(index) {
                return this.marked == index;
            },
            common(e) {
            },
            down() {
                if (this.races.length -1 == this.marked) {
                    return this.marked = 0;
                }
                this.marked++
            },
            up() {
                if (this.marked == 0) {
                    return this.marked = this.races.length - 1;
                }
                this.marked--
            },
            join() {
                let raceId = this.races[this.marked].id;

                this.$router.push({name: `race`, params: { raceId: raceId }});
            },
            create() {
                this.$router.push({name: `create`});
            },
            scrollIntoViewIfNeeded(target) {
                let rect = target.getBoundingClientRect();
                if (rect.bottom > this.$refs.raceList.scrollHeight - 20) {
                    target.scrollIntoView(false);
                }

                if (rect.top < this.$refs.raceContainer.getBoundingClientRect().top) {
                    target.scrollIntoView();
                }
            },
            howto() {
                this.$modal.show("howto");
            },
        }

    }

</script>
