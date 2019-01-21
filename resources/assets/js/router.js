import VueRouter from 'vue-router';

export default new VueRouter({
    mode: 'history',
    routes: [
        { path: '/login', component: require('./components/LoginForm.vue'), name: "login"},
        { path: '/signup', component: require('./components/SignupForm.vue'), name: "signup"},
        { path: '/race/create', component: require('./components/CreateGame.vue'), name: "create"},
        { path: '/race/:raceId', component: require('./components/GameLoading.vue'), name: "race"},
        { path: '/lobby', component: require('./components/GameLobby.vue'), name: "lobby"},
        { path: '/404', component: require('./components/GameNotFound.vue'), name: "404"},
    ]
});
