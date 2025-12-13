//console.log(" bootstrap.js CARGADO");
import 'bootstrap'; //Carga la parte JavaScript de Bootstrap (dropdowns, modals, tooltips, etc.).
import axios from 'axios'; //Importa la librería axios, que usas para hacer peticiones HTTP desde el navegador (AJAX).
window.axios = axios; //Hace que axios esté disponible de forma global como window.axios.

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'; //Configura axios para que todas las peticiones lleven por defecto la cabecera
