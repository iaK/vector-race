<template>
    <div class="absolute w-full h-full pin flex items-center justify-center">
        <div class="text-center mx-4">
            <h1 class="text-white mb-6 text-3xl lg:text-5xl">Create an account</h1>
            <form class="w-full" style="max-width: 400px">
                <ul class="list-reset" v-if="errors">
                    <li
                        v-for="error in getErrors"
                        v-text="error"
                        class="text-red-light mb-2 italic font-bold"
                    ></li>
                </ul>

                <input dusk="email" class="bg-blue-darker mb-2 p-4 text-white w-full" placeholder="Email" type="text" v-model="email">
                <input dusk="username" class="bg-blue-darker mb-2 p-4 text-white w-full" placeholder="Username" type="text" v-model="username">
                <input dusk="password" class="bg-blue-darker mb-4 p-4 text-white w-full" placeholder="Password" type="password" v-model="password">
                <input dusk="password-confirmation" class="bg-blue-darker mb-4 p-4 text-white w-full" placeholder="Password again" type="password" v-model="password_confirmation">


                <div class="text-center">
                    <button dusk="signup-button" class="btn" @click.prevent="signup">
                        Signup!
                    </button>
                    <button class="btn" @click.prevent="login">
                        Log in instead!
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
                username: null,
                password: null,
                errors: null,
                password_confirmation: null,
            }
        },

        computed: {
            getErrors() {
                if (!this.errors) {
                    return;
                }

                let errorArray = [];
                for(let error in this.errors) {
                    if(this.errors.hasOwnProperty(error)) {
                        errorArray.push(this.errors[error]);
                    }
                }
                return errorArray.flat();
            }
        },

        methods: {
            login() {
                this.$router.push({name: `login`});
            },
            signup() {
                this.error = false;

                axios.post("/register", {
                    email: this.email,
                    username: this.username,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                }).then(({data}) => {
                    window.location.href = "/lobby";
                }).catch((e) => {
                    this.errors = e.response.data.errors;
                });
            },
        }

    }

</script>
