import 'bootstrap/dist/js/bootstrap.bundle.min.js'; // incluye Popper + todos los plugins
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
