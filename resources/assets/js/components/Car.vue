<script>
    import { mapGetters, mapMutations, mapActions, mapState } from 'vuex';
    import carImages from '../cars.js';

    export default {
        props: ['car'],

        data() {
            return {
                encodedImage: null,
                eventToken: null,
                image: null
            }
        },

        created() {
            this.encodedImage = carImages.yellow;
            this.image = new Image();
            this.image.src = this.encodedImage;
            this.eventToken = Event.listen('backgroundRendered', this.rerender);
        },

        beforeDestroy() {
            Event.ignore(this.eventToken);
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
                this.ctx.drawImage(this.image, - this.config.car.width / 2, - this.config.car.height / 2,this.config.car.width,this.config.car.height);
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
