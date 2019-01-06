import Vue from 'vue';

window.Event = new class {
    constructor() {
        this.vue = new Vue();
    }

    fire(event, data = null) {
        this.vue.$emit(event, data);

        return this;
    }

    listen(event, callback) {
        this.vue.$on(event, callback);

        return this;
    }
};
