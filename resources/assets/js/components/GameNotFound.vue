<template>

</template>

<script>
    import { mapActions } from 'vuex';

    export default {

        mounted() {
            Event.fire('show-result-board', {
                heading: "Not found",
                text: "Game not found or has been ended.",
                buttons: [{
                    text: "Lobby",
                    func: () => {
                        this.$modal.hide('result-board');
                        this.$router.push({name: `lobby`});
                    }
                }]
            });
        },
    }

</script>
