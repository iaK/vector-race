import Vue from 'vue'
import VueViewports from 'vue-viewports'
import store from '../stores/store'

const options = [
  {
    rule: '1px',
    label: 'xs'
  },
  {
    rule: '576px',
    label: 'sm'
  },
  {
    rule: '768px',
    label: 'md'
  },
  {
    rule: '992px',
    label: 'lg'
  },
  {
    rule: '1200px',
    label: 'xl'
  },
]


export default {
    install(Vue) {
        Vue.use(VueViewports, options);

        window.addEventListener('resize', () => {
            this.commit();
        });

        this.commit();
    },

    commit() {
        store.commit('setViewport', VueViewports._getPublicObject());
    }
}
