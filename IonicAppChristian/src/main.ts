import { createApp } from 'vue';
import { IonicVue } from '@ionic/vue';
import App from './App.vue';
import router from './router';

import '@ionic/vue/css/core.css';


const app = createApp(App);
app.use(IonicVue);
app.use(router);
app.mount('#app');



