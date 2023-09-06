<template>
    <div class="wrapper">
        <Header></Header>
        <Sidebar></Sidebar>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Project tokens</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">
                                    <a href="#" @click="$router.go(-1)">
                                        <i class="fa fa-arrow-left"></i> Go Back
                                    </a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tokens</h3>
                                <button class="btn btn-success float-right" @click="addToken">Add Token</button>
                            </div>
                            <div class="card-body">
                                <div v-show="!countTokens && loadedTokens" class="text-center text-danger pb-2">No tokens were found in this project!</div>
                                <div v-show="!loadedTokens" class="text-center pb-2"><i class="nav-icon fas fa-spinner fa-spin"></i> Loading...</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Scope</th>
                                                <th>Created Date</th>
                                                <th>Updated Date</th>
                                                <th><i class="fa fa-cog"></i> Settings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(token, index) in tokens">
                                                <td>{{ token.id }}</td>
                                                <td>{{ token.name }}</td>
                                                <td>{{ token.scope }}</td>
                                                <td>{{ moment(token.created_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>{{ moment(token.updated_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>
                                                    <button @click="showToken(token.token)" class="btn btn-primary ml-2"><i class="fa fa-eye"></i> Show token</button>
                                                    <button @click="editToken(token.id, index, token.name)" class="btn btn-warning ml-2"><i class="fa fa-edit"></i> Edit</button>
                                                    <button @click="deleteToken(token.id, index)" class="btn btn-danger ml-2"><i class="fa fa-trash"></i> Delete</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center" v-show="moreExists">
                                    <button class="btn btn-primary" v-on:click="loadMoreLanguages"><i class="fa fa-arrow-down"></i> Load More Projects</button>
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

<style>
.w-5 {
    width: 15px;
}
</style>

<script>
import Header from '@/layouts/Header.vue'
import Sidebar from '@/layouts/Sidebar.vue'
import Footer from '@/layouts/Footer.vue'
import Swal from 'sweetalert2'
import moment from "moment"

export default {

    data() {
        return {
            tokens: {},
            projectId: this.$route.params.id,
            loadedTokens: false,
            countTokens: 0,
            moment: moment,
            moreExists: false,
            nextPage: 0
        }
    },

    mounted() {
        this.getTokens();
    },

    methods: {
        getTokens(page=1) {
            axios.get(`projects/${this.projectId}/tokens?page=${page}`)
                .then(response => {
                    this.tokens = response.data.data;
                    this.countTokens = response.data.total;
                    this.loadedTokens = true;
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        loadMoreTokens() {
            axios.get(`projects/${this.projectId}/tokens?page=${this.nextPage}`)
                .then(response => {
                    response.data.data.forEach(data => {
                        this.tokens.push(data);
                    });
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        addToken() {
            self = this;
            Swal.fire({
                title: 'Add token',
                html:
                    `Name <input id="swal-name" class="swal2-input">` +
                    `Scope <select id="swal-scope" class="swal2-input">
                        <option value="view">View</option>
                        <option value="full_access">Full access</option>
                    </select>`,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    axios.post(`projects/${this.projectId}/tokens`, {
                        name: document.getElementById('swal-name').value,
                        scope: document.getElementById('swal-scope').value
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Token added!',
                                text: response.data.data.token
                        });
                        self.tokens.unshift(response.data.data);
                        self.countTokens++;
                    })
                    .catch(function (error) {
                        Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: error.response.data.message
                        });
                    });
                },
                })
        },

        deleteToken(tokenId, tokenIndex) {
            self = this;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`projects/${this.projectId}/tokens/${tokenId}`)
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Token deleted!'
                        });
                        self.tokens.splice(tokenIndex, 1);
                        self.countTokens--;
                    })
                    .catch(function (error) {
                        Swal.fire({
                                icon: 'error',
                                title:
                                'Oops...',
                                text: error.response.data.message
                        });
                    });
                }
            })
        },

        editToken(tokenId, tokenIndex, tokenName) {
            self = this;
            Swal.fire({
                title: `Edit token ID ${tokenId}`,
                html:
                    `Name <input id="swal-name" value="${tokenName}" class="swal2-input">` +
                    `Scope <select id="swal-scope" class="swal2-input">
                        <option value="view">View</option>
                        <option value="full_access">Full access</option>
                    </select>`,
                showCancelButton: true,
                confirmButtonText: 'Edit',
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    axios.post(`projects/${self.projectId}/tokens/${tokenId}`, {
                        name: document.getElementById('swal-name').value,
                        scope: document.getElementById('swal-scope').value
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Token edited!'
                        });
                    self.tokens[tokenIndex] = response.data.data;
                    })
                    .catch(function (error) {
                        Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: error.response.data.message
                        });
                    });
                },
            });
        },

        showToken(token) {
            Swal.fire({
                icon: 'info',
                title: 'Private token',
                text: token
            });
        }
    },

    components: {
        Header, Footer, Sidebar
    }
}
</script>
