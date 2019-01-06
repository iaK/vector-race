<template>
    <div v-if="isHost">
        <button class="btn" @click="startRace">
            Start
        </button>
        <button class="btn" @click="stopRace">
            Stop
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
            ...mapActions(['startRace']),

            stopRace() {
                axios.post(`/race/${this.race}/stop`);
            },
        }
    }
</script>
