import store from '@/store';

if (localStorage.getItem('sanctumToken')) {
    axios.get('user').then((response) => {
        store.commit('setUser', response.data);
    }).catch((errors) => {
        store.commit('setUser', {});
        localStorage.removeItem('sanctumToken');
        window.axios.defaults.headers.common['Authorization'] = ``;
    })
}
