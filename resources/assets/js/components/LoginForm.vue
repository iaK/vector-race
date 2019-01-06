<template>
    <div class="absolute w-full h-full pin flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-white mb-6 text-5xl">Vectorrace</h1>
            <form style="width: 400px">
                <p v-if="error" class="text-red-light mb-2 italic font-bold">Wrong email or password</p>
                <input class="mb-2 p-4 chat-input text-white w-full" placeholder="Email" type="text" v-model="email">
                <input class="mb-4 p-4 chat-input text-white w-full" placeholder="Password" type="password" v-model="password">
                <div class="text-center">
                    <button class="btn" @click.prevent="login">
                        Log in!
                    </button>
                    <button class="btn" @click.prevent="signup">
                        Signup!
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

    export default {

        data() {
            return {
                email: null,
                password: null,
                error: null,
            }
        },

        methods: {
            login() {
                this.error = false;

                axios.post("/login", {
                    email: this.email,
                    password: this.password,
                }).then(({data}) => {
                    window.location.href = "/race";
                }).catch((e) => {
                    this.error = true;
                });
            },
            signup() {
                this.$router.push({name: `signup`});
            },
        }

    }

</script>

<style lang="scss" scoped>
    input {
        background-color: rgba(9, 29, 54, 0.7);
    }
</style>
