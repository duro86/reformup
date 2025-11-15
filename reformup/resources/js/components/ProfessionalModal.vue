<template>
  <div class="modal fade" id="professionalModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalles de Profesional</h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>

        <!-- Contenido cuando ya tenemos datos -->
        <div class="modal-body" v-if="profesional">
          <p><strong>ID perfil:</strong> {{ profesional.id }}</p>
          <p><strong>User ID:</strong> {{ profesional.user_id }}</p>

          <div v-if="profesional.user">
            <p><strong>Usuario asociado:</strong> {{ profesional.user.nombre }} {{ profesional.user.apellidos }}</p>
            <p><strong>Email usuario:</strong> {{ profesional.user.email }}</p>
          </div>
          <div v-else class="text-danger">
            <strong>Advertencia:</strong> este perfil profesional no tiene un usuario asociado.
          </div>

          <hr>

          <p><strong>Empresa:</strong> {{ profesional.empresa }}</p>
          <p><strong>CIF:</strong> {{ profesional.cif }}</p>
          <p><strong>Email empresa:</strong> {{ profesional.email_empresa }}</p>
          <p><strong>Teléfono empresa:</strong> {{ profesional.telefono_empresa }}</p>
          <p><strong>Ciudad:</strong> {{ profesional.ciudad }}</p>
          <p><strong>Provincia:</strong> {{ profesional.provincia }}</p>
          <p><strong>Dirección empresa:</strong> {{ profesional.dir_empresa }}</p>

          <p><strong>Bio:</strong> {{ profesional.bio }}</p>

          <p><strong>Web:</strong>
            <a v-if="profesional.web" :href="profesional.web" target="_blank" rel="noopener">
              {{ profesional.web }}
            </a>
            <span v-else class="text-muted">Sin web</span>
          </p>

          <p><strong>Puntuación media:</strong> {{ profesional.puntuacion_media ? profesional.puntuacion_media : '—' }}</p>
          <p><strong>Trabajos realizados:</strong> {{ profesional.trabajos_realizados ? profesional.trabajos_realizados : 0 }}</p>

          <p><strong>Visible:</strong>
            <span v-if="profesional.visible">
              Sí
            </span>
            <span v-else>
              No
            </span>
          </p>

          <p><strong>Avatar:</strong></p>
          <div v-if="profesional.avatar && profesional.avatar !== ''">
            <img
              :src="getAvatarUrl(profesional.avatar)"
              alt="avatar"
              class="rounded-circle"
              style="width:30px;height:30px;object-fit:cover"
            >
          </div>
          <div v-else>
            <i class="bi bi-person-circle" style="font-size: 1rem;"></i>
          </div>
        </div>

        <!-- Mientras carga -->
        <div class="modal-body text-center" v-else>
          Cargando datos...
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';
import axios from 'axios';

export default {
  name: 'ProfessionalModal',
  data() {
    return {
      profesional: null,
      bsModal: null,
    };
  },
  mounted() {
    this.bsModal = new Modal(this.$refs.modal);
  },
  methods: {
    openModal(professionalId) {
      this.profesional = null; // Mostrar "Cargando..."

      axios.get(`/admin/profesionales/${professionalId}`)
        .then(response => {
          this.profesional = response.data;
          this.bsModal.show();
        })
        .catch(error => {
          console.error('Error al cargar profesional:', error);
        });
    },
    closeModal() {
      this.bsModal.hide();
    },
    getAvatarUrl(path) {
      return `/storage/${path}`;
    },
  },
};
</script>
