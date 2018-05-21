import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue'
import Merger from './components/Merger.vue';

Vue.component('merger', Merger);

Vue.use(BootstrapVue);

new Vue({ el: '#app' });
