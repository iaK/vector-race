import PubSub from 'pubsub-js';

window.Event = new class {

    fire(event, data = null) {
        PubSub.publishSync(event, data);

        return this;
    }

    listen(event, callback) {
        return PubSub.subscribe(event, callback);
    }

    ignore(token) {
        PubSub.unsubscribe(token);

        return this;
    }
};
