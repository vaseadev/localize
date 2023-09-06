<template>
    <div class="wrapper">
        <Header></Header>
        <Sidebar></Sidebar>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Project languages</h1>
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
                                <h3 class="card-title">Languages</h3>
                                <button class="btn btn-success float-right" @click="addLanguage">Add Language</button>
                            </div>
                            <div class="card-body">
                                <div v-show="!countLanguages && loadedLanguages" class="text-center text-danger pb-2">No languages were found in this project!</div>
                                <div v-show="!loadedLanguages" class="text-center pb-2"><i class="nav-icon fas fa-spinner fa-spin"></i> Loading...</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Code</th>
                                                <th>Created Date</th>
                                                <th>Updated Date</th>
                                                <th><i class="fa fa-cog"></i> Settings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(language, index) in languages">
                                                <td>{{ language.id }}</td>
                                                <td>{{ language.code }}</td>
                                                <td>{{ moment(language.created_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>{{ moment(language.updated_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>
                                                    <button @click="editLanguage(language.id, index, language.code)" class="btn btn-warning ml-2"><i class="fa fa-edit"></i> Edit</button>
                                                    <button @click="deleteLanguage(language.id, index)" class="btn btn-danger ml-2"><i class="fa fa-trash"></i> Delete</button>
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
            languages: {},
            projectId: this.$route.params.id,
            loadedLanguages: false,
            countLanguages: 0,
            moment: moment,
            moreExists: false,
            nextPage: 0
        }
    },

    mounted() {
        this.getLanguages();
    },

    methods: {
        getLanguages(page=1) {
            axios.get(`projects/${this.projectId}/languages?page=${page}&per_page=15`)
                .then(response => {
                    this.languages = response.data.data;
                    this.countLanguages = response.data.total;
                    this.loadedLanguages = true;
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        loadMoreLanguages() {
            axios.get(`projects/${this.projectId}/languages?page=${this.nextPage}&per_page=15`)
                .then(response => {
                    response.data.data.forEach(data => {
                        this.languages.push(data);
                    });
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        addLanguage() {
            self = this;
            Swal.fire({
                title: 'Code language',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: (codeLanguage) => {
                    axios.post(`projects/${this.projectId}/languages`, {
                        code: codeLanguage
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Language added!'
                        });
                        self.languages.unshift(response.data.data);
                        self.countLanguages++;
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

        deleteLanguage(languageId, languageIndex) {
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
                    axios.delete(`projects/${this.projectId}/languages/${languageId}`)
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Language deleted!'
                        });
                        self.languages.splice(languageIndex, 1);
                        self.countLanguages--;
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

        editLanguage(languageId, languageIndex, languageCode) {
            self = this;
            Swal.fire({
                title: `Edit language ID ${languageId}`,
                input: 'text',
                inputValue: languageCode,
                showCancelButton: true,
                confirmButtonText: 'Edit',
                showLoaderOnConfirm: true,
                preConfirm: function(newCode) {
                    axios.post(`projects/${self.projectId}/languages/${languageId}`, {
                        code: newCode
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Language edited!'
                        });
                    self.languages[languageIndex] = response.data.data;
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
