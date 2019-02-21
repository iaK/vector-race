import store from './stores/store.js'
import VModal from 'vue-js-modal'


export default {
    initialize() {
        Echo.join(`race.${store.state.race}`)
            .joining((user) => {
                Event.fire("Joining", user);
                if (store.getters.notInRace(user)) {
                    store.commit('setCars', [user]);
                }
                store.commit('setOnline', user);
            })
            .leaving((user) => {
                Event.fire("Leaving", user);
                console.log("left", user)

                store.commit('setOffline', user)
            })
            .here((users) => {
                console.log("here", users)
                Event.fire("Here", users);
                users.forEach((user) => {
                    store.commit('setOnline', user)
                })
            })
            .listen('CarMoved', (e) => {
                console.log("CarMoved", e)
                Event.fire("CarMoved", e);

                store.dispatch('moveCurrentCar', {
                    id: e.id,
                    location: e.location,
                    speed: e.speed,
                });
            })
            .listen('PlayerKicked', (e) => {
                console.log("PlayerKicked");
                Event.fire("PlayerKicked", e);
                if (e.user.id == store.state.yourCar) {
                    store.dispatch('leaveGame');

                    Event.fire('show-result-board', {
                        heading: "Kicked",
                        text: "You got kicked. Sorry.",
                        buttons: [
                            {
                                text: "Ok..",
                                func: () => {
                                    Vue.prototype.$modal.hide('result-board');
                                }
                            }
                        ]
                    })
                }
            })
            .listen('RaceStarted', (e) => {
                console.log("Race started", e.race);
                store.commit('changeRaceState', e.race.state);
                Event.fire("RaceStarted", e);
                Event.fire('messageBoard',{message: 'Game started'});
            })
            .listen('PlayerWon', (e) => {
                Event.fire("PlayerWon", e);

                if (e.user.id == store.state.yourCar) {
                    return store.dispatch('win')
                }
                return store.dispatch('fail');
            })
            .listen('TurnChanged', (e) => {
                console.log("TurnChanged", e)
                store.dispatch('changeTurn', e.car);
                Event.fire('TurnChanged', e)
            })
            .listen("PlayerLeft", (e) => {
                console.log("PlayerLeft");
                store.commit('setNotInRace', e.user);
                Event.fire("PlayerLeft", e);
            })
            .listen("GameClosed", (e) => {
                store.dispatch('leaveGame');
                Event.fire('show-result-board', {
                        heading: "Game closed",
                        text: "The host left the game... Darn it!",
                        buttons: [
                            {
                                text: "Ok..",
                                func: () => {
                                    store.dispatch('leaveGame');
                                    Vue.prototype.$modal.hide('result-board');
                                }
                            }
                        ]
                    })
                    .fire("GameClosed", e);

            })
            .listen('MessagePosted', (e) => {
                Event.fire("MessagePosted", e);
            });
    },

    destroy() {
        Echo.leave(`race.${store.state.race}`)
    }
}

