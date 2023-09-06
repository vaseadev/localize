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
                                <h3 class="card-title">Users</h3>
                                <button class="btn btn-success float-right" @click="addUser">Add User</button>
                            </div>
                            <div class="card-body">
                                <div v-show="!countUsers && loadedUsers" class="text-center text-danger pb-2">No users were found in the database!</div>
                                <div v-show="!loadedUsers" class="text-center pb-2"><i class="nav-icon fas fa-spinner fa-spin"></i> Loading...</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Created Date</th>
                                                <th>Updated Date</th>
                                                <th><i class="fa fa-cog"></i> Settings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(user, index) in users">
                                                <td>{{ user.id }}</td>
                                                <td>{{ user.name }}</td>
                                                <td>{{ user.email }}</td>
                                                <td>{{ user.role }}</td>
                                                <td>{{ moment(user.created_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>{{ moment(user.updated_at).format("YYYY-MM-DD HH:mm") }}</td>
                                                <td>
                                                    <router-link :to="{ name: 'ViewUser', params: { id: user.id } }"><button class="btn btn-primary"><i class="fa fa-eye"></i> View</button></router-link>
                                                    <button @click="editUser(user.id, index, user.name, user.email)" class="btn btn-warning ml-2"><i class="fa fa-edit"></i> Edit</button>
                                                    <button @click="editUserPwd(user.id)" class="btn btn-warning ml-2"><i class="fa fa-edit"></i> Edit pwd</button>
                                                    <button @click="deleteUser(user.id, index)" class="btn btn-danger ml-2"><i class="fa fa-trash"></i> Delete</button>
                                                </td>
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
        <Footer></Footer>
    </div>
</template>

<script>
import Header from '@/layouts/Header.vue'
import Sidebar from '@/layouts/Sidebar.vue'
import Footer from '@/layouts/Footer.vue'
import Swal from 'sweetalert2'
import moment from "moment"

export default {

    data() {
        return {
            users: {},
            loadedUsers: false,
            countUsers: 0,
            moment: moment
        }
    },

    mounted() {
        this.getUsers();
    },

    methods: {
        getUsers() {
            axios.get(`users`)
                .then(response => {
                    this.users = response.data.data;
                    this.countUsers = response.data.data.length;
                    this.loadedUsers = true;
                });
        },

        addUser() {
            self = this;
            Swal.fire({
                title: 'Add user',
                html:
                    `Name <input id="swal-name" class="swal2-input">` +
                    `<br>Email <input id="swal-email" type="email" class="swal2-input">` +
                    `<br>Password <input id="swal-password" type="password" class="swal2-input">` +
                    `<br>Role <select id="swal-role" class="swal2-input">
                        <option value="admin">admin</option>
                        <option value="developer">developer</option>
                        <option value="translator">translator</option>
                    </select>`,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    axios.post(`users`, {
                        name: document.getElementById('swal-name').value,
                        email: document.getElementById('swal-email').value,
                        password: document.getElementById('swal-password').value,
                        role: document.getElementById('swal-role').value
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'User added!'
                        });
                        self.users.unshift(response.data.data);
                        self.countUsers++;
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

        deleteUser(userId, userIndex) {
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
                    axios.delete(`users/${userId}`)
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'User deleted!'
                        });
                        self.users.splice(userIndex, 1);
                        self.countUsers--;
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

        editUser(userId, userIndex, userName, userEmail) {
            self = this;
            Swal.fire({
                title: `Edit user ID ${userId}`,
                html:
                    `Name <input id="swal-name" value="${userName}" class="swal2-input">` +
                    `<br>Email <input id="swal-email" value="${userEmail}" type="email" class="swal2-input">` +
                    `<br>Role <select id="swal-role" class="swal2-input">
                        <option value="admin">admin</option>
                        <option value="developer">developer</option>
                        <option value="translator">translator</option>
                    </select>`,
                showCancelButton: true,
                confirmButtonText: 'Edit',
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    axios.post(`users/${userId}`, {
                        name: document.getElementById('swal-name').value,
                        email: document.getElementById('swal-email').value,
                        role: document.getElementById('swal-role').value
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'User edited!'
                        });
                    self.users[userIndex] = response.data.data;
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

        editUserPwd(userId) {
            Swal.fire({
                title: 'New password',
                input: 'password',
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: (newPassword) => {
                    axios.post(`users/${userId}/password`, {
                        password: newPassword
                    })
                    .then(function (response) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Password edited!'
                        });
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
        }
    },

    components: {
        Header, Footer, Sidebar
    }
}
</script>
