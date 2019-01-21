<template>
    <modal
        name="howto"
        classes="bg-transparent p-4 w-full sm:w-auto"
        :adaptive="true"
        :scrollable="true"
        height="auto"
    >
        <div class="menuBox text-white p-10 border border-blue-lightest border-1 relative">
            <div class="absolute pin-r pin-t mr-2 mt-2 text-lg text-white cursor-pointer" @click="close">x</div>
            <h1 class="text-3xl text-center mb-6 text-white">How to play</h1>
            <p class="mb-2">
                Vector race is a space themed turn based race game. The goal is simply to get your car passed the finnish line before your opponents. Beware thought! If you go outside the course, you loose.
            </p>
            <p class="mb-8">
                Once started, you get 9 options where you can go with your car like the picture below. The option you click, your car will go to
            </p>
            <div class="flex flex-wrap sm:flex-no-wrap justify-center sm:items-center mb-8">

                <img src="/img/options.png" class="mr-2 mb-2">
                <img src="/img/options_explanined.png" class="">
            </div>
            <p class="mb-2">
                Your car has speed in both the x and y axis. The leftmost options increse your speed to the left, the top options increases your speed to the top and so on. The middle option dosen't alter the speed.
            </p>
            <p class="mb-8">
                On your turn, your 9 options will be placed relative to your speed.
                if for example you got 3 speed to left and one up, your options will be placed 4 lines to the left and one down from your car, like below
            </p>
            <img src="/img/example.png" class="block mx-auto mb-8">
            <p class="mb-2">
                When the game starts, your car has no speed in any directions.
            </p>
            <p class="mb-2">
                You have 6 seconds to make your move, if you fail to do so, your turn will be skipped.
            </p>
        </div>
    </modal>
</template>

<script>

    export default {
        methods: {
            close() {
                this.$modal.hide("howto")
            }
        }
    }

</script>

<style lang="scss" scoped>
    .menuBox {
        background: rgba(22, 85, 162, 0.9);
    }
</style>
