<template>
  <div class="modal fade" id="trabajoProModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <!-- MODAL CON ESTILO PRO -->
      <div class="modal-content bg-pro-primary">
        <div class="modal-header bg-pro-secondary text-white">
          <h5 class="modal-title">
            Trabajo #{{ trabajo.id }}
            <span v-if="trabajo.solicitud && trabajo.solicitud.titulo">
              - {{ trabajo.solicitud.titulo }}
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body" v-if="loaded">
          <!-- Estado y fechas -->
          <div class="row mb-3">
            <div class="col-md-4">
              <h6 class="fw-semibold mb-1">Estado</h6>
              <span class="badge bg-secondary text-uppercase">
                {{ trabajo.estado || '—' }}
              </span>
            </div>
            <div class="col-md-4">
              <h6 class="fw-semibold mb-1">Fecha inicio</h6>
              <p class="mb-0">
                {{ trabajo.fecha_ini || 'Sin iniciar' }}
              </p>
            </div>
            <div class="col-md-4">
              <h6 class="fw-semibold mb-1">Fecha fin</h6>
              <p class="mb-0">
                {{ trabajo.fecha_fin || 'Sin finalizar' }}
              </p>
            </div>
          </div>

          <!-- Dirección obra -->
          <div class="mb-3">
            <h6 class="fw-semibold mb-1">Dirección de la obra</h6>
            <p class="mb-0">
              {{ trabajo.dir_obra || 'No indicada' }}
            </p>
          </div>

          <hr>

          <!-- DATOS DEL PRESUPUESTO -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Datos del presupuesto</h5>

            <p class="mb-1" v-if="trabajo.presupuesto">
              <strong>Nombre:</strong>
              <span>
                {{ trabajo.presupuesto.nombre || ('Presupuesto #' + trabajo.presupuesto.id) }}
              </span>
            </p>

            <p class="mb-1" v-if="trabajo.presupuesto">
              <strong>Total:</strong>
              <span v-if="trabajo.presupuesto.total != null">
                {{ formatMoney(trabajo.presupuesto.total) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>

            <!-- Notas - Detalle -->
            <div class="mb-3" v-if="trabajo.presupuesto && trabajo.presupuesto.notas">
              <h6 class="fw-semibold mb-1">Notas del presupuesto</h6>
              <div class="border rounded p-2 bg-pro-primary small border-pro-secondary"
                   v-html="trabajo.presupuesto.notas">
              </div>
            </div>

            <p v-if="!trabajo.presupuesto" class="text-muted mb-0">
              No hay datos de presupuesto asociados.
            </p>
          </div>

          <hr>

          <!-- Cliente -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Cliente</h5>
            <template v-if="trabajo.cliente">
              <p class="mb-1">
                <strong>
                  {{ trabajo.cliente.nombre }}
                  <span v-if="trabajo.cliente.apellidos">
                    {{ trabajo.cliente.apellidos }}
                  </span>
                </strong>
              </p>
              <p class="mb-0">
                <span v-if="trabajo.cliente.email">
                  {{ trabajo.cliente.email }}
                </span>
              </p>
            </template>
            <p v-else class="text-muted mb-0">
              No se han podido cargar los datos del cliente.
            </p>
          </div>
        </div>

        <div class="modal-body text-center py-5" v-else>
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <div>Cargando trabajo...</div>
        </div>

        <div class="modal-footer bg-pro-primary">
          <button
            type="button"
            class="btn btn-sm bg-pro-secondary text-white"
            data-bs-dismiss="modal"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { Modal } from "bootstrap";

export default {
  name: "TrabajoModalPro",
  data() {
    return {
      trabajo: {},
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
      this.trabajo = {};

      try {
        const resp = await window.axios.get(`/profesional/trabajos/${id}`, {
          headers: { Accept: "application/json" },
        });

        this.trabajo = resp.data;
        this.loaded = true;
        this.modalInstance.show();
      } catch (e) {
        console.error(e);
        alert("No se ha podido cargar el trabajo.");
      }
    },
    formatMoney(value) {
      if (value == null) return "";
      return Number(value).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
};
</script>
