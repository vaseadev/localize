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
                <div class="row">
                    Home Page
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
export default {

components: {
    Header,
    Footer,
    Sidebar
  }
}
</script>
