import { createStore } from 'vuex';
import createPersistedState from "vuex-persistedstate";

const store = createStore({
    state: () => ({
        user: {}
    }),

    mutations: {
        setUser(state, payload) {
            state.user = payload
        },
    },

    plugins: [createPersistedState()]
})

export default store;
