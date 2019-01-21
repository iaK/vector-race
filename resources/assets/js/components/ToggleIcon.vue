<template>
    <div class="w-12 h-12">
        <div class="bg-blue border-1 border-blue-lightest bg-blue p-2 text-white relative w-12 h-12 flex items-center justify-center">
            <i :class="icon" class="text-2xl" @click="click"></i>
        </div>
    </div>
</template>
<script>

    export default {

        props: {
            open: {
                type: String,
            },
            icon: {
                type: String,
            },
            hideOnClick: {
                type: Boolean,
                default: true,
            }
        },

        data() {
            return {
                show: true,
            }
        },

        mounted() {

        },

        methods: {
            click() {
                this.$modal.show(this.open)
            }
        }

    }

</script>
