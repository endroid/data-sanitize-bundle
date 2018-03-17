import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue'
import Merger from './components/Merger.vue';

Vue.use(BootstrapVue);

new Vue({
    el: '#merger',
    template: '<Merger />',
    components: { Merger }
});
