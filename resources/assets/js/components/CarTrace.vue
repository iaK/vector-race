<script>
    import { mapGetters, matMutations, mapActions, mapState } from 'vuex';

    export default {
        props: ["car", "color"],

        data() {
            return {
                eventToken: null,
            }
        },

        render() {
            this.drawTrace()
        },

        mounted() {
            this.eventToken = Event.listen("backgroundRendered", this.drawTrace);
        },

        beforeDestroy() {
            Event.ignore(this.eventToken);
        },

        computed: {
            ...mapState(["config"]),
        },

        methods: {
            ...mapActions(["drawLine"]),
            drawTrace() {
                if (!this.car.trace) {
                    return;
                }

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
