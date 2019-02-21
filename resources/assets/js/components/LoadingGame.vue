<template>
    <div>
        <game-board v-if="loaded"></game-board>
        <div class="flex items-center absolute w-full h-full justify-center pin-l pin-t" v-else>
            <vue-loading type="bars" color="rgba(9, 29, 54, 0.9)" :size="{ width: '100px', height: '100px' }"></vue-loading>
        </div>
    </div>
</template>
<script>

    import { mapActions, mapMutations } from 'vuex';

    export default {

        data() {
            return {
                loaded: false,
            }
        },

        mounted() {
            this.joinGame(this.raceId)
                .then(() => {
                    setTimeout(() => {
                        this.loaded = true;
                    }, 1000)
                }).catch((e) => {
                    this.leaveGame();
                    Event.fire("show-result-board", {
                        heading: "Fail!",
                        text: e.getMessage(),
                        buttons: [
                            {
                                text: "Ok",
                                func: () => {
                                    this.leaveGame();
                                    this.$modal.hide('result-board');
                                }
                            }
                        ]
                    });
                });
        },

        computed: {
            raceId() {
                return this.$route.params.raceId;
            }
        },

        methods: {
            ...mapActions(['joinGame', 'leaveGame']),
        }

    }

</script>

