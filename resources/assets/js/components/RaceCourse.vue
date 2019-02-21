<script>
    import { mapMutations, mapState } from 'vuex';

    export default {

        data() {
            return {
                eventToken: null,
            }
        },

        mounted() {
            this.eventToken = Event.listen('rerender', this.rerender);
        },

        beforeDestroy() {
            Event.ignore(this.eventToken);
        },

        render() {
            this.drawCourse();
            this.drawFinishLine()
        },

        computed: {
            ...mapState(["ctx", 'config', 'course', 'canvas']),

            path() {
                let path = new Path2D();

                path = this.drawCourseLine(path, this.course.outer_track.slice());
                path = this.drawCourseLine(path, this.course.inner_track.slice());

                return path;
            },
        },

        methods: {
            ...mapMutations(["setCoursePath"]),

            rerender() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.drawBackground();
                this.drawCourse();
                this.drawFinishLine();
                this.drawGridX();
                this.drawGridY();

                Event.fire("backgroundRendered")
            },

            drawCourseLine(path, line) {
                let start = line.shift();

                path.moveTo(start.x, start.y);

                line.forEach(value => {
                    path.lineTo(value.x, value.y);
                });

                path.closePath();

                return path;
            },

            drawFinishLine() {
                let path = new Path2D()
                path.moveTo(this.course.finish_line.start.x, this.course.finish_line.start.y);
                path.lineTo(this.course.finish_line.end.x, this.course.finish_line.end.y);
                path.closePath();
                this.ctx.strokeStyle = "red"
                this.ctx.lineWidth = 2;
                this.ctx.stroke(path);
            },

            drawCourse() {
                this.setCoursePath(this.path);
                this.ctx.strokeStyle = "#bee3f5";
                this.ctx.fillStyle = "rgba(22, 85, 162, 0.7)";
                this.ctx.lineWidth = 5;
                this.ctx.fill(this.path);
                this.ctx.stroke(this.path);
            },

            drawBackground() {
                this.ctx.fillStyle = "rgba(18, 67, 126, .7)";
                this.ctx.fillRect(0,0,960,960);
            },

            drawGridX() {
                let start = this.config.gridWidthX;
                do {
                    this.ctx.beginPath();
                    this.ctx.lineWidth = this.config.gridLineWidthX;
                    this.ctx.strokeStyle = "rgba(9, 38, 78, .4)";
                    this.ctx.moveTo(0, start);
                    this.ctx.lineTo(this.canvas.width, start);
                    this.ctx.stroke();
                    start += this.config.gridWidthX;
                } while (start < this.canvas.width);
            },

            drawGridY() {
                let start = this.config.gridWidthY;
                do {
                    this.ctx.beginPath();
                    this.ctx.lineWidth = this.config.gridLineWidthY;
                    this.ctx.strokeStyle = "rgba(9, 38, 78, .4)";
                    this.ctx.moveTo(start, 0);
                    this.ctx.lineTo(start, this.canvas.height);
                    this.ctx.stroke();
                    start += this.config.gridWidthY;
                } while (start < this.canvas.height);
            },
        }
    }
</script>
