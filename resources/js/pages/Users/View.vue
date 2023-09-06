<template>
    <div class="wrapper">
        <Header></Header>
        <Sidebar></Sidebar>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">View user {{ user.name }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">
                                    <router-link :to="{name : 'Users'}">
                                        <i class="fa fa-arrow-left"></i> Back Users
                                    </router-link>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">User info</h3>
                                </div>
                                <div class="card-body">
                                    <strong><i class="fas fa-key mr-1"></i> ID</strong>
                                    <p class="text-muted">
                                        {{ user.id }}
                                    </p>
                                    <hr>
                                    <strong><i class="fas fa-book mr-1"></i> Name</strong>
                                    <p class="text-muted">
                                        {{ user.name }}
                                    </p>
                                    <hr>
                                    <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                                    <p class="text-muted">
                                        {{ user.email }}
                                    </p>
                                    <hr>
                                    <strong><i class="fas fa-gavel mr-1"></i> Role</strong>
                                    <p class="text-muted">
                                        {{ user.role }}
                                    </p>
                                    <hr>
                                    <strong><i class="fas fa-clock mr-1"></i> Created At</strong>
                                    <p class="text-muted">{{ moment(user.updated_at).format("YYYY-MM-DD HH:mm") }}</p>
                                    <hr>
                                    <strong><i class="fas fa-clock mr-1"></i> Updated At</strong>
                                    <p class="text-muted">{{ moment(user.updated_at).format("YYYY-MM-DD HH:mm") }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer></Footer>
    </div>
</template>

<script>
import Header from '@/layouts/Header.vue'
import Sidebar from '@/layouts/Sidebar.vue'
import Footer from '@/layouts/Footer.vue'
import moment from 'moment'
import Swal from 'sweetalert2'

export default {

    data() {
        return {
            userId: this.$route.params.id,
            user: {},
            moment: moment,
        }
    },

    mounted() {
        this.getUser();
    },

    methods: {
        getUser() {
            axios.get(`users/${this.userId}`)
                .then(response => {
                    this.user = response.data.data;
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message
                    });
                });
        }
    },

    components: {
        Header, Footer, Sidebar
    }
}
</script>
