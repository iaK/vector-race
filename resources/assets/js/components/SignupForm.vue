<template>
    <div class="absolute w-full h-full pin flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-white mb-6 text-5xl">Create an account</h1>
            <form style="width: 400px">
                <ul class="list-reset" v-if="errors">
                    <li
                        v-for="error in getErrors"
                        v-text="error"
                        class="text-red-light mb-2 italic font-bold"
                    ></li>
                </ul>

                <input class="mb-2 p-4 chat-input text-white w-full" placeholder="Email" type="text" v-model="email">
                <input class="mb-2 p-4 chat-input text-white w-full" placeholder="Username" type="text" v-model="username">
                <input class="mb-4 p-4 chat-input text-white w-full" placeholder="Password" type="password" v-model="password">
                <input class="mb-4 p-4 chat-input text-white w-full" placeholder="Password again" type="password" v-model="password_confirmation">


                <div class="text-center">
                    <button class="btn" @click.prevent="signup">
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
                    window.location.href = "/race";
                }).catch((e) => {
                    this.errors = e.response.data.errors;
                });
            },
        }

    }

</script>

<style lang="scss" scoped>
    input {
        background-color: rgba(9, 29, 54, 0.7);
    }
</style>
