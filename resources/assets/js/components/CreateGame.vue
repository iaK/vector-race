<template>
    <div class="max-w-md mx-auto pt-8 px-4">
        <h1 class="text-white mb-4">Create game</h1>
        <div class="bg-blue p-5 mb-4">
            <select v-model="selectedCourse" class="bg-blue-darker text-white p-2">
                <option v-for="course in courses" :value="course.id" class="text-white">
                    {{ course.name }}
                </option>
            </select>
        </div>
        <div class="flex">
            <button dusk="create-game-button" class="btn" @click="create" :disabled="clicked">
                Create
            </button>
            <button class="btn mr-2 ml-2" @click="cancel" :disabled="clicked">
                Cancel
            </button>
        </div>
    </div>
</template>
<script>
    import { mapState, mapActions, mapMutations } from 'vuex';


    export default {

        data() {
            return {
                courses: [{name: "Standard", id: 1}],
                selectedCourse: 1,
                clicked: false,
            }
        },

        mounted() {
            this.setCreatedGame(null);
        },

        computed: {
            ...mapState(["createdGame"]),
        },

        methods: {
            ...mapMutations(["setCreatedGame"]),
            ...mapActions(["createGame", "joinGame"]),
            create() {
                this.clicked = true;
                this.createGame(this.selectedCourse).then(() => {
                    this.$router.push({name: `race`, params: { raceId: this.createdGame }});
                }).catch((e) => {
                    this.clicked = false;
                    Event.fire("show-result-board", {
                        heading: "Error!",
                        text: e.response.data.data.message,
                        buttons: [{
                            text: "Back",
                            func: () => {
                                this.$modal.hide('result-board');
                            }
                        }]
                    })
                });
            },
            cancel() {
                this.$router.push({name: `lobby`});
            }
        }

    }

</script>
