<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <router-link class="brand-link" :to="{name : 'Home'}">
            <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="Localize" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Localize</span>
        </router-link>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://i.imgur.com/r6YT3kX.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ currentUser.name }}</a>
                </div>
            </div>
            <!--
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>
            -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <router-link class="nav-link" active-class="active" :to="{name : 'Home'}">
                        <li class="nav-item">
                            <i class="nav-icon fas fa-home"></i>
                            <p>
                                Home
                            </p>
                        </li>
                    </router-link>
                    <router-link class="nav-link" :to="{name : 'Projects'}" active-class="active">
                        <li class="nav-item">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Projects
                            </p>
                        </li>
                    </router-link>
                    <router-link v-show="currentUser.role == 'admin'" class="nav-link" :to="{name : 'Users'}" active-class="active">
                        <li class="nav-item">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Users
                            </p>
                        </li>
                    </router-link>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script>
    export default {
        data() {
            return {
                currentUser: this.$store.state.user
            }
        }
    }
</script>
