require('./bootstrap');
require('./event.js');

window.Vue = require('vue');


import VModal from 'vue-js-modal'
import store from "./stores/store.js";
import VueRouter from 'vue-router';
import router from './router.js';
import VueLoading from 'vue-loading-template'
import vClickOutside from 'v-click-outside'
import Viewport from './viewport.js'

Vue.use(VueLoading)
Vue.use(VueRouter);
Vue.use(VModal);
Vue.use(vClickOutside);
Vue.use(Viewport)
Vue.config.productionTip = false;
// Vue.config.devtools = false;

// Vue.component('car-trace', require('./components/CarTrace.vue'));
Vue.component('vector-game', require('./components/Game.vue'));
Vue.component('game-course', require('./components/Course.vue'));
Vue.component('game-car', require('./components/Car.vue'));
Vue.component('game-pointers', require('./components/Pointers.vue'));
Vue.component('game-pointer', require('./components/Pointer.vue'));
Vue.component('game-trace', require('./components/Trace.vue'));
Vue.component('countdown-board', require('./components/CountdownBoard.vue'));
Vue.component('score-board', require('./components/ScoreBoard.vue'));
Vue.component('message-board', require('./components/MessageBoard.vue'));
Vue.component('master-controls', require('./components/MasterControls.vue'));
Vue.component('player-controls', require('./components/PlayerControls.vue'));
Vue.component('chat-box', require('./components/ChatBox.vue'));
Vue.component('result-board', require('./components/ResultBoard.vue'));
Vue.component('game-lobby', require('./components/GameLobby.vue'));
Vue.component('create-game', require('./components/CreateGame.vue'));
Vue.component('user-settings', require('./components/UserSettings.vue'));
Vue.component('how-to', require('./components/HowTo.vue'));
Vue.component('toggle-icon', require('./components/ToggleIcon.vue'));

const app = new Vue({
    store,
    router,
    el: '#app'
});
