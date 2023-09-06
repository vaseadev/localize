import axios from "axios";

window.axios = axios
axios.defaults.withCredentials = true
axios.defaults.baseURL = 'http://localhost:8000/api/'
if (localStorage.getItem('sanctumToken')) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('sanctumToken')}`
}
