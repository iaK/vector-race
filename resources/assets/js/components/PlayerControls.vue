<template>
    <div>
        <button v-if="isHost" class="btn" @click="startRace">
            Start
        </button>
        <button class="btn" @click="confirm">
            Leave game
        </button>
    </div>
</template>
<script>
    import { mapActions, mapState, mapGetters } from 'vuex'

    export default {
        computed: {
            ...mapState(["race", "host", 'yourCar']),
            isHost() {
                return this.host == this.yourCar;
            }
        },

        methods: {
            ...mapActions(["leaveGame", "startRace"]),

            stopRace() {
                axios.post(`/race/${this.race}/stop`);
            },
            confirm() {
                Event.fire("show-result-board", {
                    heading: "Confirm",
                    text: "Are you sure?",
                    buttons: [
                        {
                            text: "Yes!",
                            func: () => {
                                this.leaveGame();
                                Event.fire('hide-result-board');
                            }
                        },
                        {
                            text: "Cancel!",
                            func: () => {
                                Event.fire('hide-result-board');
                            }
                        }
                    ]
                });
            }
        }
    }
</script>
