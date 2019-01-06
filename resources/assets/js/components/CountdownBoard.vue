<template>
    <div class="flex items-center justify-center countdown-board py-10 px-10 mb-4 border border-blue-lightest border-1 text-white text-4xl w-full">
        {{ secondsLeft }}
    </div>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        data() {
            return {
                interval: null,
                secondsLeft: 0,
            }
        },

        mounted() {
            Event.listen('TurnChanged', (e) => {
                    this.clearClock();
                    this.startClock();
                })
                .listen('PlayerWon', (e) => {
                    this.clearClock();
                }).listen('click', () => {
                    this.clearClock();
                })
        },

        computed: {
            ...mapState(['yourCar', 'race']),
        },

        methods: {
            startClock() {
                this.secondsLeft = 6;
                this.interval = setInterval(() => {
                    if (this.secondsLeft > 0) {
                        this.secondsLeft--;
                    }
                }, 1000)
            },
            clearClock() {
                this.secondsLeft = null;
                clearInterval(this.interval);
            },
        }
    }
</script>

<style>
    .countdown-board {
        background: rgba(22, 85, 162, 0.7);
        /*transform:skewX(-10deg);*/
    }
</style>
