<template>
<div class="max-w-md mx-auto pt-8">
        <h1 class="text-white mb-4">Create game</h1>
        <div class="p-5 lobbybox mb-4">
            <select v-model="selectedCourse" class="inner text-white p-2">
                <option v-for="course in courses" :value="course.id" class="text-white">
                    {{ course.name }}
                </option>
            </select>
        </div>
        <div class="flex">
            <button class="btn" @click="create">
                Create
            </button>
            <button class="btn mr-2 ml-2" @click="cancel">
                Cancel
            </button>
        </div>
    </div>
</template>
<script>
    import { mapState, mapActions } from 'vuex';


    export default {

        data() {
            return {
                courses: [{name: "Standard", id: 1}],
                selectedCourse: null,
            }
        },

        mounted() {
            this.$store.watch(
                (state) => {
                    return this.$store.state.createdGame;
                },
                (val) => {
                    this.$router.push({name: `race`, params: { raceId: this.createdGame }});
                }
            );
        },

        computed: {
            ...mapState(["createdGame"]),
        },

        methods: {
            ...mapActions(["createGame"]),
            create() {
                this.createGame(this.selectedCourse);
            },
            cancel() {
                this.$router.push({name: `lobby`});
            }
        }

    }

</script>


<style lang="scss" scoped>
    .lobbybox {
        background: rgba(22, 85, 162, 0.7);
        .inner {
            background-color: rgba(9, 29, 54, 0.7);
        }
        .marked {
            background: rgba(22, 85, 162, 0.8);
        }
    }
</style>
