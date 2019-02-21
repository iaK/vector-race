import Vue from 'vue';
import './event.js';
import VModal from 'vue-js-modal'
import store from "./stores/store.js";
import VueRouter from 'vue-router';
import router from './router.js';
import VueLoading from 'vue-loading-template'
import vClickOutside from 'v-click-outside'
import Viewport from './plugins/viewport.js'
import Echo from 'laravel-echo'


Vue.use(VueLoading)
Vue.use(VueRouter);
Vue.use(VModal);
Vue.use(vClickOutside);
Vue.use(Viewport)
Vue.config.productionTip = false;
// Vue.config.devtools = false;

// Vue.component('car-trace', require('./components/CarTrace.vue'));
Vue.component('game-board', require('./components/GameBoard.vue'));
Vue.component('race-course', require('./components/RaceCourse.vue'));
Vue.component('race-car', require('./components/RaceCar.vue'));
Vue.component('game-pointers', require('./components/GamePointers.vue'));
Vue.component('game-pointer', require('./components/GamePointer.vue'));
Vue.component('car-trace', require('./components/CarTrace.vue'));
Vue.component('countdown-board', require('./components/CountdownBoard.vue'));
Vue.component('score-board', require('./components/ScoreBoard.vue'));
Vue.component('message-board', require('./components/MessageBoard.vue'));
Vue.component('player-controls', require('./components/PlayerControls.vue'));
Vue.component('chat-box', require('./components/ChatBox.vue'));
Vue.component('result-board', require('./components/ResultBoard.vue'));
Vue.component('game-lobby', require('./components/GameLobby.vue'));
Vue.component('create-game', require('./components/CreateGame.vue'));
Vue.component('user-settings', require('./components/UserSettings.vue'));
Vue.component('how-to', require('./components/HowTo.vue'));
Vue.component('toggle-icon', require('./components/ToggleIcon.vue'));


window._ = require('lodash');
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '387b49b3308e38dc6cc0',
    cluster: "eu",
    encrypted: true
});

try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}
