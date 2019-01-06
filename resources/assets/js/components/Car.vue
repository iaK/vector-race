<script>
    import { mapGetters, mapMutations, mapActions, mapState } from 'vuex';

    export default {
        props: ['car'],

        data() {
            return {
                carImage: null,
            }
        },

        created() {
            this.carImage = document.getElementById("car");
            console.log(this.carImage);
            Event.listen('backgroundRendered', this.rerender);
        },

        render(createElement) {
            return createElement(
                'game-trace',   // tag name
                {
                    props: {
                        car: this.car,
                    }
                }
            )
        },

        computed: {
            ...mapState(["config", "ctx", "course"]),
        },

        methods: {
            rerender() {
                this.drawCar()
            },
            drawCar() {
                let car = this.car;
                let angel = this.speedToAngel(car.speed);

                this.ctx.translate(car.location.x, car.location.y);
                this.ctx.rotate(angel);
                this.ctx.drawImage(this.carImage, - this.config.car.width / 2, - this.config.car.height / 2,this.config.car.width,this.config.car.height);
                this.ctx.rotate(- angel);
                this.ctx.translate(-car.location.x, -car.location.y);
            },
            speedToAngel(speed) {
                let rad = Math.atan(speed.top / speed.left) + 1.5707963268
                return speed.left < 0 ? rad + 3.1415926536 : rad;
            },
        }
    }
</script>
