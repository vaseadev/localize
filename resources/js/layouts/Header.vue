<template>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a @click="logout()" class="nav-link text-danger" href="#" role="button">
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    import store from '@/store';

    export default {
        methods: {
            logout(){
                axios.post(`auth/logout`).then((response) => {
                    store.commit('setUser', {});
                    localStorage.removeItem('sanctumToken');
                    window.axios.defaults.headers.common['Authorization'] = ``;
                    this.$router.push('/admin/login');
                }).catch((errors) => {
                    console.log("Logout error!")
                })
            }
        }
    }
</script>
