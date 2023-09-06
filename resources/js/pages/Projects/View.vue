<template>
    <div class="wrapper">
        <Header></Header>
        <Sidebar></Sidebar>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">View project {{ project.name }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">
                                    <router-link :to="{name : 'Projects'}">
                                        <i class="fa fa-arrow-left"></i> Back Projects
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
                                    <h3 class="card-title">Project info</h3>
                                </div>
                                <div class="card-body">
                                    <strong><i class="fas fa-book mr-1"></i> Name</strong>
                                    <p class="text-muted">
                                        {{ project.name }}
                                    </p>
                                    <hr>
                                    <strong><i class="fas fa-clock mr-1"></i> Created At</strong>
                                    <p class="text-muted">{{ moment(project.updated_at).format("YYYY-MM-DD HH:mm") }}</p>
                                    <hr>
                                    <strong><i class="fas fa-clock mr-1"></i> Updated At</strong>
                                    <p class="text-muted">{{ moment(project.updated_at).format("YYYY-MM-DD HH:mm") }}</p>
                                    <div class="text-center">
                                        <router-link :to="{ name: 'Languages', params: { id: this.projectId } }"><button class="btn btn-primary">Languages</button></router-link>
                                        <span v-show="currentUser.role == 'developer' || currentUser.role == 'admin'">
                                        <router-link :to="{ name: 'Tokens', params: { id: this.projectId } }"><button class="btn btn-primary ml-2">Tokens</button></router-link>
                                        </span>
                                        <router-link :to="{ name: 'Translates', params: { id: this.projectId } }"><button class="btn btn-primary ml-2">Translates</button></router-link>
                                        <br>
                                        <div class="mt-2">
                                            <button class="btn btn-success" @click="importJsonFile">Import JSON File</button>
                                            <button class="btn btn-success ml-2" @click="importSheets">Import Google Sheets</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="lastImport" class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Save last import</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                        <b>Type</b> <a class="float-right">{{ lastImport.type }}</a>
                                        </li>
                                        <li class="list-group-item">
                                        <b>Date</b> <a class="float-right">{{ moment(lastImport.created_at).format("YYYY-MM-DD HH:mm") }}</a>
                                        </li>
                                    </ul>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button @click="saveLastImport" class="btn btn-success btn-block"><i class="fas fa-check"></i> Yes</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button @click="deleteLastImport" class="btn btn-danger btn-block"><i class="fas fa-trash"></i> No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" v-show="currentUser.role == 'admin'">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Project add access user</h3>
                                </div>
                                <div class="card-body">
                                    <div class="input-group">
                                        <input v-model="this.username" type="text" placeholder="Username" class="form-control">
                                        <span class="input-group-append">
                                        <button @click="searchUsers" class="btn btn-info btn-flat">Search</button>
                                        </span>
                                    </div>
                                    <div class="table-responsive mt-4">
                                        <div v-show="!searchCount && searched && !searchLoading" class="text-center text-danger pb-2">No users were found in the database!</div>
                                        <div v-show="searchLoading" class="text-center pb-2"><i class="nav-icon fas fa-spinner fa-spin"></i> Loading...</div>

                                        <div v-show="searchCount">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>User</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th><i class="fa fa-cog"></i> Settings</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(user, index) in searchedUsers">
                                                        <td>{{ user.id }}</td>
                                                        <td>{{ user.name }}</td>
                                                        <td>{{ user.email }}</td>
                                                        <td>{{ user.role }}</td>
                                                        <td><button @click="addAccessUser(user.id, index)" class="btn btn-success ml-2"><i class="fa fa-plus"></i> Add</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Project access users</h3>
                                </div>
                                <div class="card-body">
                                    <div v-show="!countAccessUsers && loadedAccessUsers" class="text-center text-danger pb-2">No users were found in this project!</div>
                                    <div v-show="!loadedAccessUsers" class="text-center pb-2"><i class="nav-icon fas fa-spinner fa-spin"></i> Loading...</div>
                                    <div v-show="countAccessUsers" class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>User</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th><i class="fa fa-cog"></i> Settings</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(user, index) in accessUsers">
                                                    <td>{{ user.id }}</td>
                                                    <td>{{ user.name }}</td>
                                                    <td>{{ user.email }}</td>
                                                    <td>{{ user.role }}</td>
                                                    <td><button @click="removeAccessUser(user.id, index)" class="btn btn-danger ml-2"><i class="fa fa-trash"></i> Remove</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
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
import { ref } from 'vue';

export default {
    data() {
        return {
            projectId: this.$route.params.id,
            currentUser: this.$store.state.user,
            project: {},
            accessUsers: {},
            loadedAccessUsers: false,
            countAccessUsers: 0,
            moment: moment,
            username: '',
            searchedUsers: {},
            searched: false,
            searchLoading: false,
            searchCount: 0,
            lastImport: false
        }
    },

    mounted() {
        this.getProject();

        if (this.currentUser.role == 'admin') {
            this.getListProjectAccess();
        }

        this.getLastImport();
    },

    methods: {
        deleteLastImport() {
            axios.delete(`projects/${this.projectId}/import/${this.lastImport.id}`)
                .then(response => {
                    this.lastImport = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'You have successfully deleted your last import!'
                    });
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message
                    });
                });
        },

        saveLastImport() {
            axios.post(`projects/${this.projectId}/import/${this.lastImport.id}`)
                .then(response => {
                    this.lastImport = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'You have successfully saved your last import!'
                    });
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message
                    });
                });
        },

        getLastImport() {
            axios.get(`projects/${this.projectId}/import/last`)
                .then(response => {
                    if (response.data.import) {
                        this.lastImport = response.data.import;
                    }
                }).catch(error => {

                });
        },

        importJsonFile() {
            self = this;
            Swal.fire({
                title: 'Import JSON File',
                html: '<input id="swal-language" class="swal2-input" placeholder="Language Code">',
                input: 'file',
                inputAttributes: {
                    'accept': '.json',
                    'aria-label': 'Upload your file'
                },
                showCancelButton: true,
                confirmButtonText: 'Upload',
                preConfirm: (file) => {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('language', document.getElementById('swal-language').value);

                    axios.post(`projects/${this.projectId}/import/json`, formData)
                        .then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Imported successfully!',
                                text: `Imported ${response.data.raport.importedTranslates} translates! Exist ${response.data.raport.existKeys.length} keys!`
                            });
                            self.lastImport = response.data.import;
                        }).catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: error.response.data.message
                            });
                        });
                },
            });
        },

        importSheets() {
            self = this;
            Swal.fire({
                title: 'Import Google Sheets',
                input: 'text',
                html: 'Sheets ID<br>https://docs.google.com/spreadsheets/d/<b>ID</b>',
                showCancelButton: true,
                confirmButtonText: 'Upload',
                preConfirm: (sheetsId) => {
                    const formData = new FormData();
                    formData.append('sheetId', sheetsId);

                    axios.post(`projects/${this.projectId}/import/sheets`, formData)
                        .then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Imported successfully!',
                                text: `Imported ${response.data.raport.importedTranslates} translates! Exist ${response.data.raport.existKeys.length} keys!`
                            });
                            self.lastImport = response.data.import;
                        }).catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: error.response.data.message
                            });
                        });
                },
            });
        },

        getProject() {
            axios.get(`projects/${this.projectId}`)
                .then(response => {
                    this.project = response.data.data;
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message
                    });
                });
        },

        getListProjectAccess() {
            axios.get(`projects/${this.projectId}/access`)
                .then(response => {
                    this.accessUsers = response.data.data;
                    this.countAccessUsers = response.data.data.length;
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message
                    });
                }).finally(() => {
                    this.loadedAccessUsers = true;
                });
        },

        removeAccessUser(userId, userIndex) {
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
                    axios.delete(`users/${userId}/projects/${this.projectId}`)
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'User access removed!'
                        });
                        self.accessUsers.splice(userIndex, 1);
                        self.countAccessUsers--;
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

        searchUsers() {
            this.searched = true;
            this.searchLoading = true;

            axios.get(`users/${this.username}/search/projects/${this.projectId}`)
                .then(response => {
                    this.searchedUsers = response.data.data;
                    this.searchCount = response.data.data.length;
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.statusText
                    });
                }).finally(() => {
                    this.searchLoading = false;
                });
        },

        addAccessUser(userId, userIndex) {
            self = this;
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`users/${userId}/projects/${this.projectId}`)
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'User access added!'
                        });
                        self.accessUsers.unshift(response.data.user);
                        self.countAccessUsers++;
                        self.searchedUsers.splice(userIndex, 1);
                        self.searchCount--;
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
        }
    },

    components: {
        Header, Footer, Sidebar
    }
}
</script>
