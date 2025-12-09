<template>
  <div class="modal fade" id="solicitudModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content bg-pro-primary">
        <div class="modal-header bg-pro-secondary text-white">
          <h5 class="modal-title">
            Solicitud #{{ solicitud.id }} - {{ solicitud.titulo || '' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body" v-if="loaded">
          
          <!-- Descripción -->
          <div class="mb-3" v-if="solicitud.descripcion">
            <h6 class="fw-semibold mb-1">Descripción de la solicitud</h6>
            <div class="p-2 small border rounded bg-pro-primary border-pro-secondary"
                 v-html="solicitud.descripcion">
            </div>
          </div>
          <span v-else class="text-muted">Sin descripción</span>

          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="fw-semibold">Ubicación</h6>
              <p class="mb-0">
                <span v-if="solicitud.ciudad">{{ solicitud.ciudad }}</span>
                <span v-if="solicitud.ciudad && solicitud.provincia"> - </span>
                <span v-if="solicitud.provincia">{{ solicitud.provincia }}</span>
                <br>
                <small v-if="solicitud.dir_empresa" class="text-muted">
                  {{ solicitud.dir_empresa }}
                </small>
              </p>
            </div>
            <div class="col-md-6">
              <h6 class="fw-semibold">Presupuesto máximo</h6>
              <p class="mb-0">
                <span v-if="solicitud.presupuesto_max">
                  {{ formatMoney(solicitud.presupuesto_max) }} €
                </span>
                <span v-else class="text-muted">No indicado</span>
              </p>
            </div>
          </div>

          <hr>

          <div>
            <h6 class="fw-semibold">Datos del cliente</h6>
            <p class="mb-0" v-if="solicitud.cliente">
              <strong>{{ solicitud.cliente.nombre }} {{ solicitud.cliente.apellidos }}</strong><br>
              <span v-if="solicitud.cliente.email">{{ solicitud.cliente.email }}<br></span>
              <span v-if="solicitud.cliente.telefono">{{ solicitud.cliente.telefono }}</span>
            </p>
            <p v-else class="text-muted">Sin datos de cliente.</p>
          </div>

          <div class="mt-3 text-muted small">
            Fecha solicitud: {{ solicitud.fecha || '—' }}
          </div>
        </div>

        <div class="modal-body text-center py-5" v-else>
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <div>Cargando solicitud...</div>
        </div>

        <div class="modal-footer bg-pro-primary">
          <button type="button" class="btn btn-sm bg-pro-secondary text-white" data-bs-dismiss="modal">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'SolicitudModal',
  data() {
    return {
      solicitud: {},
      loaded: false,
      modalInstance: null,
    };
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.modal);
  },
  methods: {
    async openModal(id) {
      this.loaded = false;
      this.solicitud = {};

      try {
        const resp = await window.axios.get(`/profesional/solicitudes/${id}`, {
          headers: { Accept: 'application/json' },
        });
        this.solicitud = resp.data;
        this.loaded = true;
        this.modalInstance.show();
      } catch (e) {
        console.error(e);
        alert('No se ha podido cargar la solicitud.');
      }
    },
    formatMoney(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
};
</script>
