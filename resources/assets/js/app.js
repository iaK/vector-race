require('./bootstrap');
import store from './stores/store.js'
import router from './router.js'

window.Vue = require('vue');

const app = new Vue({
    store,
    router,
    el: '#app'
});
