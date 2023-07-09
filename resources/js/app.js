import Vue from 'vue';
import Vuetify from '../plugins/vuetify';
import VuetifyMask from '../plugins/vuetify-mask';
import App from './App.vue';
import Vuelidate from 'vuelidate';
import router from './router';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import VueSocketio from 'vue-socket.io';
import store from './store';
import CKEditor from 'ckeditor4-vue';
import Toaster from 'v-toaster'
import excel from 'vue-excel-export'

// You need a specific loader for CSS files like https://github.com/webpack/css-loader
import 'v-toaster/dist/v-toaster.css'


Vue.use(VueSocketio, 'http://192.168.1.49:3472', { });
Vue.use(Vuetify);
Vue.use(VuetifyMask);
Vue.use(Vuelidate);
Vue.use(VueSweetalert2);
Vue.use(Toaster);
Vue.use( CKEditor );
Vue.use(excel)


const app = new Vue({
    vuetify: Vuetify,
    el: '#app',
    router,
    store,
    render: h => h(App),
});