<template>
    <div class="absolute w-full h-full pin flex items-center justify-center">
        <div class="text-center mx-4">
            <h1 class="text-white mb-6 text-3xl lg:text-5xl">
                Vectorrace
            </h1>

            <form class="w-full" style="max-width: 400px">

                <p v-if="error" class="text-red-light mb-2 italic font-bold">
                    Wrong email or password
                </p>

                <input
                    class="bg-blue-darker mb-2 p-4 chat-input text-white w-full"
                    dusk="email"
                    name="email"
                    placeholder="Email"
                    type="text"
                    v-model="email"
                >
                <input
                    class="bg-blue-darker mb-4 p-4 chat-input text-white w-full"
                    dusk="password"
                    name="password"
                    placeholder="Password"
                    type="password"
                    v-model="password"
                >

                <div class="text-center">

                    <button dusk="login-button" class="btn" @click.prevent="login">
                        Log in!
                    </button>
                    <button dusk="signup-button" class="btn" @click.prevent="signup">
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
                    window.location.href = "/lobby";
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
