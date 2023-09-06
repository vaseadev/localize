import '@/bootstrap';
import { createApp } from 'vue';
import '@/axios';
import store from '@/store';
import App from '@/layouts/App.vue';
import router from '@/router';
import '@/auth';
import eventBus from '@/event-bus.js';

if (localStorage.getItem('sanctumToken')) {
    axios.get('user').then((response) => {
        eventBus.emit('userLogged', response.data);
    }).catch((errors) => {
        localStorage.removeItem('sanctumToken');
        window.axios.defaults.headers.common['Authorization'] = ``;
    })
}

eventBus.on('userLogged', (data) => {
    console.log(data);
});


const app = createApp(App)
app.use(router).use(store).mount("#app");
