<template>
    <div v-if="show" class="bg-blue flex items-center justify-center xl:py-10 xl:px-10 mb-4 border border-blue-lightest border-1 text-white text-4xl xl:w-full w-12 h-12 xl:h-auto mt-4 xl:mt-0">
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
                eventTokens: [],
                show: true,
            }
        },

        mounted() {
            this.eventTokens.push(Event.listen('TurnChanged', (e) => {
                this.clearClock();
                this.startClock();
            }))
            this.eventTokens.push(Event.listen('PlayerWon', (e) => {
                this.clearClock();
            }))
            this.eventTokens.push(Event.listen('click', () => {
                this.clearClock();
            }))
        },

        beforeDestroy() {
            this.eventTokens.forEach((token) => {
                Event.ignore(token);
            });
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
