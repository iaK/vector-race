<template>
    <div>
        <modal name="result-board" classes="bg-transparent" :click-to-close="false">
            <div>
                <div class="relative" style="top: 2px;">
                    <div class="flex justify-center">
                        <div class="inline-flex justify-center relative">
                            <div class="left-triangel"></div>
                            <div class="middle flex items-center" style="max-width: 400px;">
                                <h1
                                    v-text="heading"
                                    class="text-white text-xl font-normal text-center px-8 py-2"
                                ></h1>
                            </div>
                            <div class="right-triangel"></div>
                        </div>
                    </div>
                </div>
                <div class="result-board px-8 py-6 text-white text-md">
                    <div class="mb-4" v-text="text"></div>

                    <div class="flex justify-center w-full">
                        <button
                            v-for="(button, index) in buttons"
                            class="btn mr-2"
                            :class="'button-' + index"
                            @click="button.func"
                            v-text="button.text"
                        >
                        </button>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>

    export default {
        props: ["result"],

        data() {
            return {
                heading: '',
                text: '',
                buttons: [],
            }
        },

        mounted() {
            Event.listen('show-result-board', this.setValues);
            Event.listen('hide-result-board', this.close);
        },

        methods: {
            setValues(name, e) {
                this.heading = e.heading;
                this.text = e.text;
                this.buttons = e.buttons;
                this.show();
            },
            show() {
                this.$modal.show('result-board');
            },
            close() {
                this.$modal.hide('result-board');
            },
            lobby() {
                this.$modal.hide('result-board');
            },
        }
    }

</script>

<style lang="scss" scoped>
    .result-board {
        background-color: rgba(9, 29, 54, 1);
        border: 1px solid white;
    }
    .left-triangel {
        width: 52px;
        height: 0;
        border-style: solid;
        border-width: 0 0 52px 52px;
        border-color: transparent transparent white transparent;
    }

    .left-triangel:after {
        position: absolute;
        content: "";
        top: 2px;
        left: 2px;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 0 50px 50px;
        border-color: transparent transparent rgba(9, 29, 54, 1) transparent;
    }

    .middle {
        height: 52px;
        background: rgba(9, 29, 54, 1);
        border-top: 1px solid white;
    }

    .right-triangel {
        width: 52px;
        height: 0;
        border-style: solid;
        border-width: 52px 0 0 52px;
        border-color: transparent transparent transparent white;
    }

    .right-triangel:after {
        position: absolute;
        top: 2px;
        right: 2px;
        content: '';
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 50px 0 0 50px;
        border-color: transparent transparent transparent rgba(9, 29, 54, 1);
    }
</style>
