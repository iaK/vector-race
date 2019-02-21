<template>
    <div v-if="yourTurn && gameState == 'going'">
        <game-pointer
            pos="middle"
            :speed-top="0"
            :speed-left="0"
            :location="pointers.location.middle"
        />
        <game-pointer
            pos="left"
            :speed-top="0"
            :speed-left="-1"
            :location="pointers.location.left"
        />
        <game-pointer
            pos="right"
            :speed-top="0"
            :speed-left="1"
            :location="pointers.location.right"
        />
        <game-pointer
            pos="top"
            :speed-top="-1"
            :speed-left="0"
            :location="pointers.location.top"
        />
        <game-pointer
            pos="bottom"
            :speed-top="1"
            :speed-left="0"
            :location="pointers.location.bottom"
        />
        <game-pointer
            pos="topLeft"
            :speed-top="-1"
            :speed-left="-1"
            :location="pointers.location.topLeft"
        />
        <game-pointer
            pos="topRight"
            :speed-top="-1"
            :speed-left="1"
            :location="pointers.location.topRight"
        />
        <game-pointer
            pos="bottomLeft"
            :speed-top="1"
            :speed-left="-1"
            :location="pointers.location.bottomLeft"
        />
        <game-pointer
            pos="bottomRight"
            :speed-top="1"
            :speed-left="1"
            :location="pointers.location.bottomRight"
        />
    </div>
</template>
<script>

    import { mapState, mapGetters, mapMutations } from 'vuex';

    export default {

        mounted() {
            let car = this.car(this.yourCar);

            this.initset({
                x: (car.speed.left * this.config.gridWidthX) + car.location.x,
                y: (car.speed.top * this.config.gridWidthY) + car.location.y
            });
        },

        computed: {
            ...mapState(["pointers", "config", "course", "gameState", "yourCar"]),
            ...mapGetters(["yourTurn", "car"]),
        },

        methods: {
            ...mapMutations(["setPointers"]),
            initset(location) {
                this.setPointers({
                    middle : {x: location.x, y:location.y},
                    left : {x: location.x - this.config.gridWidthX, y: location.y},
                    right : {x: location.x + this.config.gridWidthX, y: location.y},
                    top : {x: location.x, y: location.y - this.config.gridWidthY},
                    bottom : {x: location.x, y: location.y + this.config.gridWidthY},
                    topLeft : {x: location.x - this.config.gridWidthX, y:location.y - this.config.gridWidthY},
                    topRight : {x: location.x + this.config.gridWidthX, y:location.y - this.config.gridWidthY},
                    bottomLeft : {x: location.x - this.config.gridWidthX, y:location.y + this.config.gridWidthY},
                    bottomRight : {x: location.x + this.config.gridWidthX, y:location.y + this.config.gridWidthY},
                })
            }
        },

    }

</script>

