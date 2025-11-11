import "./bootstrap";
import "../scss/app.scss";
import { createApp } from 'vue';
import UsersTable from './components/tablaUsuarios.vue';

const app = createApp({
  data() {
    return {
      vistaActual: '', // vacía al inicio, nada se muestra
    };
  },
  methods: {
    mostrarVista(nombre) {
      this.vistaActual = nombre;
    },
  },
});

app.component('users-table', UsersTable);

app.mount('#app');

