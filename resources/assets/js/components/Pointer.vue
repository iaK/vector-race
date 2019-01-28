<script>

    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';

    export default {

        props: ["location","pos", "speedLeft", "speedTop"],

        data() {
            return {
                eventToken: null,
            }
        },

        watch: {
            click() {
                this.checkPointer();
            }
        },

        mounted() {
            this.eventToken = Event.listen("backgroundRendered", () => {
                this.$forceUpdate();
            })
        },

        beforeDestroy() {
            Event.ignore(this.eventToken);
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
                    // this.animateMoveCar(car);
                    this.moveCurrentCar(car);
                    this.failIfOutsideCourse(car).then(() => {
                        if(this.gameState == "going") {
                            this.sendClick(JSON.parse(JSON.stringify(car)));
                        }
                    });
                    this.checkIfCross();


                    this.$nextTick(() => {
                        this.calculateNewPointers(this.currentCar);
                        this.clearTurn();
                    });
                } else {
                    this.$forceUpdate();
                }
            },

            animateMoveCar(car) {
                let location = [
                    this.currentCar.location,
                    {x: car.location.x, y: car.location.y}
                ];
                let points = this.calcWaypoints(location);
                console.log(points);
                points.forEach((point, index) => {
                    setTimeout(() => {
                        car.location = point;
                        this.moveCurrentCar(car);
                        this.checkIfCross();
                    }, 10 * (index +1))
                })
            },

            calcWaypoints(vertices){
                let waypoints=[];
                for(let i=1;i<vertices.length;i++){
                    let pt0=vertices[i-1];
                    let pt1=vertices[i];
                    let dx=pt1.x-pt0.x;
                    let dy=pt1.y-pt0.y;
                    for(let j=0;j<10;j++){
                        let x=pt0.x+dx*j/10;
                        let y=pt0.y+dy*j/10;
                        waypoints.push({x:x,y:y});
                    }
                }
                return waypoints;
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
