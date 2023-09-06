<template>
    <div class="wrapper">
        <Header></Header>
        <Sidebar></Sidebar>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">{{ this.$route.name }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active" v-show="this.$route.name != 'Home'">
                                    <router-link :to="{name : 'Home'}">
                                        <i class="fa fa-arrow-left"></i> Back Home
                                    </router-link>
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
                                <h3 class="card-title">Projects</h3>
                                <button class="btn btn-success float-right" @click="addProject">Add Project</button>
                            </div>
                            <div class="card-body">
                                <div v-show="!countProjects && loadedProjects" class="text-center text-danger pb-2">No projects were found!</div>
                                <div v-show="!loadedProjects" class="text-center pb-2"><i class="nav-icon fas fa-spinner fa-spin"></i> Loading...</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Created Date</th>
                                                <th>Updated Date</th>
                                                <th>Others</th>
                                                <th><i class="fa fa-cog"></i> Settings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(project, index) in projects">
                                                <td>{{ project.id }}</td>
                                                <td>{{ project.name }}</td>
                                                <td>{{ moment(project.created_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>{{ moment(project.updated_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>
                                                    <router-link :to="{ name: 'Languages', params: { id: project.id } }"><button class="btn btn-primary">Languages</button></router-link>
                                                    <span v-show="currentUser.role == 'developer' || currentUser.role == 'admin'">
                                                    <router-link :to="{ name: 'Tokens', params: { id: project.id } }"><button class="btn btn-primary ml-2">Tokens</button></router-link>
                                                    </span>
                                                    <router-link :to="{ name: 'Translates', params: { id: project.id } }"><button class="btn btn-primary ml-2">Translates</button></router-link>
                                                </td>
                                                <td>
                                                    <router-link :to="{ name: 'ViewProject', params: { id: project.id } }"><button class="btn btn-primary"><i class="fa fa-eye"></i> View</button></router-link>
                                                    <span v-show="currentUser.role == 'admin'">
                                                        <button @click="editProject(project.id, index, project.name)" class="btn btn-warning ml-2"><i class="fa fa-edit"></i> Edit</button>
                                                        <button @click="deleteProject(project.id, index)" class="btn btn-danger ml-2"><i class="fa fa-trash"></i> Delete</button>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center" v-show="moreExists">
                                    <button class="btn btn-primary" v-on:click="loadMoreProjects"><i class="fa fa-arrow-down"></i> Load More Projects</button>
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
            projects: {},
            loadedProjects: false,
            countProjects: 0,
            moment: moment,
            moreExists: false,
            nextPage: 0,
            currentUser: this.$store.state.user
        }
    },

    mounted() {
        this.getProjects();
    },

    methods: {
        getProjects(page=1) {
            axios.get(`projects?page=${page}`)
                .then(response => {
                    this.projects = response.data.data;
                    this.countProjects = response.data.total;
                    this.loadedProjects = true;
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        loadMoreProjects() {
            axios.get(`projects?page=${this.nextPage}`)
                .then(response => {
                    response.data.data.forEach(data => {
                        this.projects.push(data);
                    });
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        addProject() {
            self = this;
            Swal.fire({
                title: 'Name project',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: (nameProject) => {
                    axios.post(`projects`, {
                        name: nameProject
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Project added!'
                        });
                        self.projects.unshift(response.data.data);
                        self.countProjects++;
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

        deleteProject(projectId, projectIndex) {
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
                    axios.delete(`projects/${projectId}`)
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Project deleted!'
                        });
                        self.projects.splice(projectIndex, 1);
                        self.countProjects--;
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

        editProject(projectId, projectIndex, projectName) {
            self = this;
            Swal.fire({
                title: `Edit project ID ${projectId}`,
                input: 'text',
                inputValue: projectName,
                showCancelButton: true,
                confirmButtonText: 'Edit',
                showLoaderOnConfirm: true,
                preConfirm: function(newName) {
                    axios.post(`projects/${projectId}`, {
                        name: newName
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Project edited!'
                        });
                    self.projects[projectIndex] = response.data.data;
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
        }
    },

    components: {
        Header, Footer, Sidebar
    }
}
</script>
