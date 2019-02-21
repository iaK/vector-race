<script>
    import { mapGetters, mapMutations, mapActions, mapState } from 'vuex';
    import carImages from '../cars.js';

    export default {
        props: {
            car: {
                type: Object,
                required: true,
            }
        },

        data() {
            return {
                encodedImage: null,
                eventToken: null,
                image: null,
                imageLoaded: false,
            }
        },

        created() {
            this.encodedImage = carImages[this.car.carColor];
            this.image = new Image();
            this.image.src = this.encodedImage;
            this.image.onload = () => {
                this.imageLoaded = true;
            };
            this.eventToken = Event.listen('backgroundRendered', this.rerender);
        },

        beforeDestroy() {
            Event.ignore(this.eventToken);
        },

        render(createElement) {
            return createElement(
                'car-trace',
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
                if (this.car.speed && this.imageLoaded) {
                    this.drawCar()
                }
            },
            drawCar() {
                let angel = this.speedToAngel(this.car.speed);
                this.ctx.translate(this.car.location.x, this.car.location.y);
                this.ctx.rotate(angel);
                this.ctx.drawImage(this.image, - this.config.car.width / 2, - this.config.car.height / 2,this.config.car.width,this.config.car.height);
                this.ctx.rotate(- angel);
                this.ctx.translate(-this.car.location.x, -this.car.location.y);
            },
            speedToAngel(speed) {
                let rad = Math.atan(speed.top / speed.left) + 1.5707963268
                return speed.left < 0 ? rad + 3.1415926536 : rad;
            },
        }
    }
</script>
