<template>
    <div v-if="user" class="absolute pin-t pin-r p-5 z-50">
        <img
            :src="gravatarUrl"
            alt="Avatar"
            class="w-10 h-10 rounded-full cursor-pointer z-50"
            @click="openMenu"
        />
        <modal
            name="user-settings"
            classes="bg-transparent pin flex items-center justify-center"
            :adaptive="true"
            :scrollable="true"
            height="auto"
        >
            <div class="menuBox p-10 border border-blue-lightest border-1" style="width: 500px">
                <h1 class="text-3xl text-center mb-6 text-white">Settings</h1>
                <p class="text-white text-lg mb-2">Username</p>
                <input class="mb-6 p-4 chat-input text-white w-full" type="text" v-model="username">
                <p class="text-white text-lg mb-2">Car color</p>
                <select v-model="selectedCarColor" class="inner text-white p-2 mb-6 w-full">
                    <option v-for="color in carColors" :value="color.slug" class="text-white">
                        {{ color.value }}
                    </option>
                </select>
                <p class="text-white text-lg mb-2 w-full">Trace color</p>
                <select v-model="selectedTraceColor" class="inner text-white p-2 w-full mb-6">
                    <option v-for="color in traceColors" :value="color.slug" class="text-white">
                        {{ color.value }}
                    </option>
                </select>
                <div class="flex justify-between">
                    <button class="btn" @click.prevent="save">
                        {{ saved ? "Saved" : saving ? "Saving.." : "Save" }}
                    </button>
                    <button class="btn" @click.prevent="logout">
                        Logout
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    let md5 = require('md5');
    import { mapGetters, mapState, mapMutations } from 'vuex';

    export default {

        data() {
            return {
                open: false,
                username: '',
                saving: false,
                saved: false,
                selectedTraceColor: '',
                selectedCarColor: '',
                carColors: [
                    {
                        slug: "red",
                        value: "Red",
                    },
                    {
                        slug: "green",
                        value: "Green",
                    },
                    {
                        slug: "yellow",
                        value: "Yellow",
                    },
                    {
                        slug: "blue",
                        value: "Blue",
                    },
                    {
                        slug: "orange",
                        value: "Orange",
                    },
                    {
                        slug: "purple",
                        value: "Purple",
                    },
                ],
                traceColors: [
                    {
                        slug: "red",
                        value: "Red",
                    },
                    {
                        slug: "green",
                        value: "Green",
                    },
                    {
                        slug: "yellow",
                        value: "Yellow",
                    },
                    {
                        slug: "blue",
                        value: "Blue",
                    },
                    {
                        slug: "orange",
                        value: "Orange",
                    },
                    {
                        slug: "purple",
                        value: "Purple",
                    },
                ],
            }
        },

        mounted() {
            this.username = this.user && this.user.username;
            this.selectedTraceColor = this.user && this.user.trace_color;
            this.selectedCarColor = this.user && this.user.car_color;
        },

        computed: {
            ...mapState(["user"]),

            gravatarUrl() {
                return "https://www.gravatar.com/avatar/" + md5(this.user.email)
            }
        },

        methods: {
            ...mapMutations(["setUser"]),
            save() {
                this.saving = true;

                axios.put(`/user/${this.user.id}`, {
                        username: this.username,
                        car_color: this.selectedCarColor,
                        trace_color: this.selectedTraceColor,
                    })
                    .then(({data}) => {
                        this.setUser(data.data.user);
                    })
                    .catch((error) => {
                        console.log(error)
                    })
                    .finally(() => {
                        this.saving = false;
                        this.saved = true;
                    })
            },
            logout() {
                axios.post("/logout").then(() => {
                    window.location.href = "/"
                })
            },
            openMenu() {
                this.$modal.show('user-settings');
            }
        },
    }

</script>

<style lang="scss" scoped>
    .menuBox {
        background: rgba(22, 85, 162, 0.9);
    }
    input {
        background-color: rgba(9, 29, 54, 0.7);
    }
    .inner {
        background-color: rgba(9, 29, 54, 0.7);
    }
</style>
