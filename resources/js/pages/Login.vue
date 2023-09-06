<template>
    <div class="hold-transition login-page">
        <div class="login-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="#" class="h1"><b>Local</b>ize</a>
                </div>
                <div class="card-body">
                    <div v-show="loading" class="login-box-msg"><i class="nav-icon fas fa-spinner fa-spin"></i></div>
                    <p v-show="error" class="login-box-msg text-danger">
                        {{ error }}
                    </p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" v-model="loginData.email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" v-model="loginData.password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8"></div>
                        <div class="col-4">
                            <button @click="login" type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                    <p class="mb-1">
                        <a href="#">I forgot my password</a>
                    </p>
                    <p class="mb-0">
                        <a href="#" class="text-center">Register a new membership</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import store from '@/store';

    export default {
        data() {
            return {
                loginData: {
                    email: '',
                    password: '',
                },
                error: '',
                loading: false
            }
        },
        methods: {
            login() {
                this.loading = true;
                this.error = '';
                axios.post('auth/login', this.loginData).then((response) => {
                    window.axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`
                    localStorage.setItem('sanctumToken', response.data.access_token);
                    store.commit('setUser', response.data.user);
                    this.$router.push('/admin');
                }).catch((errors) => {
                    this.error = errors.response.data;
                }).finally(() => {
                    this.loading = false;
                });
            }
        }
    }
</script>
