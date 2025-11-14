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
// Opción A: importar axios aquí
import axios from 'axios';

export default {
  data() {
    return {
      user: {}
    };
  },
  mounted() {
    this.bsModal = new Modal(this.$refs.modal);
  },
  methods: {
    openModal(userId) {
      axios.get(`/admin/usuarios/${userId}`)
        .then(response => {
          this.user = response.data;
          this.bsModal.show();
        })
        .catch(error => {
          console.error('Error al cargar usuario:', error);
        });
    },
    closeModal() {
      this.bsModal.hide();
    },
    getAvatarUrl(path) {
    // Ajusta esta función a como generes las URLs publicas de los avatares
    return `/storage/${path}`;
  }
  }
}
</script>

