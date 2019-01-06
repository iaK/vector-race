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
        >
            <div class="menuBox p-10 border border-blue-lightest border-1" style="width: 500px">
                <h1 class="text-3xl text-center mb-6 text-white">Settings</h1>
                <p class="text-white text-lg mb-2">Username</p>
                <input class="mb-6 p-4 chat-input text-white w-full" type="text" v-model="username">
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
            }
        },

        mounted() {
            this.username = this.user && this.user.username;
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
                        username: this.username
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
</style>
