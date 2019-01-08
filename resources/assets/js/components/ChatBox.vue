<template>
    <div class="chatbox p-4 w-full border border-blue-lightest">
        <div class="inner mb-4 w-full p-4 overflow-y-scroll" ref="inner" style="height: 250px">
            <ul class="list-reset">
                <li v-for="event in events" class="mb-2">
                    <span v-if="event.type == 'system'" class="text-grey italic">
                        {{ event.message }}
                    </span>
                    <span v-else class="text-white">
                        {{ event.message }}
                    </span>
                </li>
            </ul>
        </div>
        <input type="text" :value="message" class="p-4 chat-input text-white w-full" placeholder="type to chat" @keyup.enter="submit">
    </div>
</template>
<script>
    import { mapGetters, mapState, mapActions } from 'vuex'

    export default {
        data() {
            return {
                events: [],
                message: '',
                eventTokens: [],
            }
        },

        mounted() {
            this.eventTokens.push(Event.listen('Joining', (name, user) => {
                this.events.push({
                    message: user.username + ' joined the race',
                    type: "system",
                })
            }));
            this.eventTokens.push(Event.listen('Leaving', (name, user) => {
                this.events.push({
                    message: user.username + ' left the race',
                    type: "system",
                })
            }));
            this.eventTokens.push(Event.listen('Here', (users) => {
            }));
            this.eventTokens.push(Event.listen('MessagePosted', (name, e) => {
                this.events.push({
                    message: e.message,
                    type: e.type
                });
                this.scrollToBottom();
            }));
        },

        beforeDestroy() {
            this.eventTokens.forEach((token) => {
                Event.ignore(token);
            });
        },

        computed: {
            ...mapState(["yourCar", 'race']),
            ...mapGetters(["car"]),
        },

        methods: {
            submit(e) {
                this.events.push({
                    message: this.car(this.yourCar).name + ": " + e.target.value,
                    type: 'message',
                });

                axios.post(`/race/${this.race}/chat`, {
                    message: this.car(this.yourCar).name + ": " + e.target.value,
                    type: 'message',
                });

                this.message = '';

                this.scrollToBottom();
            },
            scrollToBottom() {
                this.$nextTick(() => {
                    this.$refs.inner.scrollTop = this.$refs.inner.scrollHeight;
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .chatbox {
        background: rgba(22, 85, 162, 0.7);
        .inner {
            background-color: rgba(9, 29, 54, 0.7);
        }

        .chat-input {
            background-color: rgba(9, 29, 54, 0.7);
        }
    }


    /* Handle */


        /* Track */
</style>
