<template>
  <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalles de Usuario</h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>
        <div class="modal-body" v-if="user">
          <p><strong>ID:</strong> {{ user.id }}</p>
          <p><strong>Nombre:</strong> {{ user.nombre }}</p>
          <p><strong>Apellidos:</strong> {{ user.apellidos }}</p>
          <p><strong>Email:</strong> {{ user.email }}</p>
          <p><strong>Teléfono:</strong> {{ user.telefono }}</p>
          <p><strong>Ciudad:</strong> {{ user.ciudad }}</p>
          <p><strong>Provincia:</strong> {{ user.provincia }}</p>
          <p><strong>CP:</strong> {{ user.cp }}</p>
          <p><strong>Dirección:</strong> {{ user.direccion }}</p>
          <p><strong>Avatar:</strong></p>
        <div v-if="user.avatar && user.avatar !== ''">
            <img :src="getAvatarUrl(user.avatar)" alt="avatar" class="rounded-circle" style="width:30px;height:30px;object-fit:cover">
        </div>
        <div v-else>
            <i class="bi bi-person-circle" style="font-size: 1rem;"></i>
        </div>
        </div>
        <div class="modal-body text-center" v-else>
          Cargando datos...
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { Modal } from 'bootstrap';
// Peticiones HTTP al backend
import axios from 'axios';

export default {
  data() {
    return {
      // Aquí guardaremos los datos del usuario que se muestran en el modal
      user: {}
    };
  },

  mounted() {
    // Cuando el componente se monta, inicializamos el modal de Bootstrap
    // usando la referencia al elemento del template (ref="modal")
    this.bsModal = new Modal(this.$refs.modal);
  },

  methods: {
    /**
     * Abre el modal y carga los datos del usuario desde el backend.
     * @param {Number|String} userId - ID del usuario a consultar.
     */
    openModal(userId) {
      axios.get(`/admin/usuarios/${userId}`)
        .then(response => {
          // Guardamos los datos devueltos por el backend en "user"
          this.user = response.data;
          // Mostramos el modal de Bootstrap ya inicializado
          this.bsModal.show();
        })
        .catch(error => {
          // En caso de error, lo mostramos en consola (se podría sustituir por una alerta)
          console.error('Error al cargar usuario:', error);
        });
    },

    /**
     * Cierra el modal de Bootstrap.
     */
    closeModal() {
      this.bsModal.hide();
    },

    /**
     * Genera la URL pública del avatar a partir de la ruta almacenada en BD.
     * @param {String} path - Ruta relativa del avatar (ej: "avatars/usuario1.png").
     * @returns {String} URL accesible desde el navegador.
     */
    getAvatarUrl(path) {
      // Ajusta esta función si usas otra forma de servir archivos (Storage::url, etc.)
      return `/storage/${path}`;
    }
  }
}
</script>


