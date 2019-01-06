<script>

    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';

    export default {

        props: ["location","pos", "speedLeft", "speedTop"],

        watch: {
            click() {
                this.checkPointer();
            }
        },

        mounted() {
            Event.listen("backgroundRendered", () => {
                this.$forceUpdate();
            })
        },

        render() {
            this.ctx.fillStyle = '#dbf1f7';
            this.ctx.beginPath();
            this.ctx.arc(
                this.location.x,
                this.location.y,
                this.config.pointers.height / 1.5,
                0,
                2 * Math.PI
            );
            this.ctx.fill();
            // this.ctx.fillRect(
            //     this.location.x - (this.config.pointers.width / 2),
            //     this.location.y - (this.config.pointers.height / 2),
            //     this.config.pointers.height,
            //     this.config.pointers.width
            // );
        },

        computed: {
            ...mapState(["ctx", "config", "click", "yourCar", 'gameState']),
            ...mapGetters(["currentCar"]),
        },

        methods: {
            ...mapMutations(["clearTurn"]),
            ...mapActions(["calculateBasePointer", "moveCurrentCar", "calculateNewPointers", "addToTrace", 'changeTurn', "sendClick", "failIfOutsideCourse", "checkIfCross"]),

            checkPointer() {
                if (this.isClicked(this.click)) {
                    Event.fire('click');

                    let car = {
                        location: this.location,
                        id: this.yourCar,
                        speed: {
                            left: this.currentCar.speed.left + this.speedLeft,
                            top: this.currentCar.speed.top + this.speedTop
                        }
                    };

                    this.moveCurrentCar(car);
                    this.failIfOutsideCourse(car);
                    this.checkIfCross();

                    if(this.gameState != "done") {
                        this.sendClick(JSON.parse(JSON.stringify(car)));
                    }

                    this.$nextTick(() => {
                        this.calculateNewPointers(this.currentCar);
                        this.clearTurn();
                    });
                } else {
                    this.$forceUpdate();
                }
            },

            isClicked(clickLocation) {
                let halfPointerWidth = this.config.pointers.width / 2;
                let halfPointerHeight = this.config.pointers.height / 2;

                if (
                    clickLocation.x < this.location.x + halfPointerWidth
                    && clickLocation.x > this.location.x - halfPointerWidth
                    && clickLocation.y < this.location.y + halfPointerHeight
                    && clickLocation.y > this.location.y - halfPointerHeight
                ) {
                    return true;
                }

                return  false;
            }
        }
    }

</script>
