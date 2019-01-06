<template>
            <ul class="list-reset">

                <li
                    v-for="car in cars"
                    :key="car.id"
                    class="flex mb-4"
                >
                    <div
                        class="flex flex-grow items-center justify-between scoreboard-item py-2 px-10 border border-blue-lightest border-1 text-white"
                        :class="{'opacity-50' : !car.inRace}"
                        style="width: 250px"
                    >
                        <span class="inline-block mr-4" :class="{'font-bold': yourTurn(car)}">
                            {{ car.name }} {{ isHost(car.id) ? "(host)" : ''}}
                        </span>

                        <span>
                            <span
                                class="inline-block w-2 h-2 rounded-full"
                                :class="{'bg-green' : car.online, 'bg-red' : ! car.online}"
                            ></span>
                        </span>
                    </div>
                    <button
                        v-if="isHost(yourCar) && car.id != host"
                        class="ml-2 scoreboard-item py-2 px-2 border border-blue-lightest border-1 text-white text-center"
                        @click="kickCar(car)"
                        style="width: 50px"
                    >
                        kick
                    </button>

                </li>

            </ul>
</template>
<script>
    import { mapState, mapGetters, mapActions } from 'vuex'

    export default {
        props: ['cars'],

        computed: {
            ...mapState(["yourCar", "host"]),
            ...mapGetters(["currentCar"]),
        },

        methods: {
            ...mapActions(["kickCar"]),
            isHost(id) {
                return id == this.host;
            },
            yourTurn(car) {
                if (this.currentCar) {
                    return this.currentCar.id == car.id;
                }
                return false;
            }
        }
    }
</script>

<style scoped>
    .scoreboard-item {
        background: rgba(22, 85, 162, 0.7);
        width: 300px;
        transform:skewX(-10deg);
    }
</style>
