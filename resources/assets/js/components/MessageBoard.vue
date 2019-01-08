<template>
    <div class="fixed pin-t pin-l w-full">
      <transition
        name="custom-classes-transition"
        enter-active-class="animated slideInDown"
        leave-active-class="animated slideOutUp"
      >
        <div v-if="text" class="flex justify-center">
            <div class="left-triangel"></div>
            <div class="middle flex items-center" style="max-width: 400px;">
                <h1 class="text-white text-xl font-normal text-center px-8 py-2" v-text="text">
                </h1>
            </div>
            <div class="right-triangel"></div>
        </div>
      </transition>
    </div>
</template>
<script>
    import { mapState } from 'vuex'

    export default {

        data() {
            return {
                text: '',
                eventTokens: [],
            }
        },

        mounted() {
            this.eventTokens.push(Event.listen('TurnChanged', (name, e) => {
                if (e.car == this.yourCar && this.gameState != "done") {
                    this.setText("Your turn!")
                }
             }));

            this.eventTokens.push(Event.listen('messageBoard', (name, message) => {
                this.setText(message.message);
            }));
        },

        beforeDestroy() {
            this.eventTokens.forEach((token) => {
                Event.ignore(token);
            });
        },

        computed: {
            ...mapState(["yourCar", 'gameState', 'race']),
        },

        methods: {
            setText(message) {
                this.text = message;

                setTimeout(() => {
                    this.text = '';
                }, 2000);
            }
        }
    }
</script>

<style scoped>
    .left-triangel {
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 52px 52px 0;
        border-color: transparent white transparent transparent;
    }

    .left-triangel:after {
        position: absolute;
        margin-left: 2px;
        top: 0;
        content: '';
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 50px 50px 0;
        border-color: transparent rgba(9, 29, 54, 1) transparent transparent;
    }

    .middle {
        height: 51px;
        background: rgba(9, 29, 54, 1);
        border-bottom: 1px solid white;
    }

    .right-triangel {
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 52px 52px 0 0;
        border-color: white transparent transparent transparent;
    }

    .right-triangel:after {
        position: absolute;
        top: 0;
        content: '';
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 50px 50px 0 0;
        border-color: rgba(9, 29, 54, 1) transparent transparent transparent;
    }

    .animated {
        animation-duration: .3s
    }
</style>
