<template>
    <div class="wrapper">
        <Header></Header>
        <Sidebar></Sidebar>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Project translates</h1>
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
                                <h3 class="card-title">Filters</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">Key</span>
                                        </div>
                                        <input v-model="filterKey" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">Language</span>
                                        </div>
                                        <select v-model="filterLanguage" class="form-control">
                                            <option value="">Without language</option>
                                            <option v-for="language in languages" :value="language.id">{{ language.code }}</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">Paginate</span>
                                        </div>
                                        <select v-model="filterPaginate" class="form-control">
                                            <option value="15">15</option>
                                            <option value="25">25</option>
                                            <option value="250">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <button @click="changeFilter" class="btn btn-success btn-block mb-4">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Translates</h3>
                                <button class="btn btn-success float-right" @click="addTranslate">Add Translate</button>
                            </div>
                            <div class="card-body">
                                <div v-show="!countTranslates && loadedTranslates" class="text-center text-danger pb-2">No translates were found in this project!</div>
                                <div v-show="!loadedTranslates" class="text-center pb-2"><i class="nav-icon fas fa-spinner fa-spin"></i> Loading...</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Key</th>
                                                <th>Value</th>
                                                <th>Language</th>
                                                <th>Created Date</th>
                                                <th>Updated Date</th>
                                                <th><i class="fa fa-cog"></i> Settings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(translate, index) in translates">
                                                <td>{{ translate.id }}</td>
                                                <td>{{ translate.key }}</td>
                                                <td>{{ translate.value }}</td>
                                                <td>{{ this.parsedLanguages[translate.language_id] }}</td>
                                                <td>{{ moment(translate.created_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>{{ moment(translate.updated_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>
                                                    <button @click="editTranslate(translate.id, index, translate.key, translate.value)" class="btn btn-warning ml-2"><i class="fa fa-edit"></i> Edit</button>
                                                    <button @click="deleteTranslate(translate.id, index)" class="btn btn-danger ml-2"><i class="fa fa-trash"></i> Delete</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center" v-show="moreExists">
                                    <button class="btn btn-primary" v-on:click="loadMoreTranslates"><i class="fa fa-arrow-down"></i> Load More Translates</button>
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
            translates: {},
            languages: [],
            parsedLanguages: [],
            projectId: this.$route.params.id,
            loadedTranslates: false,
            countTranslates: 0,
            moment: moment,
            moreExists: false,
            nextPage: 0,
            filterKey: '',
            filterLanguage: '',
            filterPaginate: 15,
            htmlLanguagesOptions: ''
        }
    },

    mounted() {
        this.getTranslates();
        this.getLanguages();
    },

    methods: {
        getTranslates() {
            let filters = '';
            if (this.filterKey) filters = filters + `&key=${this.filterKey}`;
            if (this.filterLanguage) filters = filters + `&language_id=${this.filterLanguage}`;

            axios.get(`projects/${this.projectId}/translates?page=1&per_page=${this.filterPaginate}${filters}`)
                .then(response => {
                    this.translates = response.data.data;
                    this.countTranslates = response.data.total;
                    this.loadedTranslates = true;
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        loadMoreTranslates() {
            let filters = '';
            if (this.filterKey) filters = filters + `&key=${this.filterKey}`;
            if (this.filterLanguage) filters = filters + `&language_id=${this.filterLanguage}`;

            axios.get(`projects/${this.projectId}/translates?page=${this.nextPage}&per_page=${this.filterPaginate}${filters}`)
                .then(response => {
                    response.data.data.forEach(data => {
                        this.translates.push(data);
                    });
                    if (response.data.current_page < response.data.last_page) {
                        this.moreExists = true;
                        this.nextPage = response.data.current_page + 1;
                    } else {
                        this.moreExists = false;
                    }
                });
        },

        changeFilter() {
            this.loadedTranslates = false;
            this.translates = {};
            this.getTranslates();
        },

        getLanguages() {
            axios.get(`projects/${this.projectId}/languages`)
                .then(response => {
                    this.languages = response.data.data;
                    this.parseLanguages();
                });
        },

        parseLanguages() {
            this.languages.forEach((item, index) => {
                this.parsedLanguages[item.id] = item.code;
                this.htmlLanguagesOptions = this.htmlLanguagesOptions + `<option value="${item.id}">${item.code}</option>`;
            });
        },

        addTranslate() {
            self = this;
            Swal.fire({
                title: 'Add translate',
                html:
                    `Key <input id="swal-key" class="swal2-input">` +
                    `<br>Value <textarea id="swal-value" class="swal2-input"></textarea>` +
                    `<br>Language <select id="swal-language" class="swal2-input">
                        ${this.htmlLanguagesOptions}
                    </select>`,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    let languageId = document.getElementById('swal-language').value;
                    axios.post(`projects/${this.projectId}/translates/languages/${languageId}`, {
                        key: document.getElementById('swal-key').value,
                        value: document.getElementById('swal-value').value,
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Translate added!',
                                text: response.data.data.token
                        });
                        self.translates.unshift(response.data.data);
                        self.countTranslates++;
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

        deleteTranslate(translateId, translateIndex) {
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
                    axios.delete(`projects/${this.projectId}/translates/${translateId}`)
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Translate deleted!'
                        });
                        self.translates.splice(translateIndex, 1);
                        self.countTranslates--;
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

        editTranslate(translateId, translateIndex, translateKey, translateValue) {
            self = this;
            Swal.fire({
                title: `Edit translate ID ${translateId}`,
                html:
                    `Key <input id="swal-key" class="swal2-input" value="${translateKey}">` +
                    `<br>Value <textarea id="swal-value" class="swal2-input">${translateValue}</textarea>`,
                showCancelButton: true,
                confirmButtonText: 'Edit',
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    axios.post(`projects/${self.projectId}/translates/${translateId}`, {
                        key: document.getElementById('swal-key').value,
                        value: document.getElementById('swal-value').value
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Translate edited!'
                        });
                    self.translates[translateIndex] = response.data.data;
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
