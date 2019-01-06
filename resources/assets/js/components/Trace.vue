<script>
    import { mapGetters, matMutations, mapActions, mapState } from 'vuex';

    export default {
        props: ["car", "color"],

        render() {
            this.drawTrace()
        },

        mounted() {
            Event.listen("backgroundRendered", this.drawTrace);
        },

        computed: {
            ...mapState(["config"]),
        },

        methods: {
            ...mapActions(["drawLine"]),
            drawTrace() {
                var prevTrace = this.car.trace[0];
                this.car.trace.forEach(trace => {
                    this.drawLine({
                        "start": prevTrace,
                        "end": trace,
                        "color": this.car.traceColor,
                        "lineWidth" : this.config.trace.width,
                    });
                    prevTrace = trace;
                });
            },
        }
    }
</script>
