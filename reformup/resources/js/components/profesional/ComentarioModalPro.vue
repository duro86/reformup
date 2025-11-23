<template>
  <div class="modal fade" id="ComentarioModalPro" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Comentario #{{ comentario.id }}
            <span v-if="comentario.titulo">
              - {{ comentario.titulo }}
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Contenido cargado -->
        <div class="modal-body" v-if="loaded">

        <!-- Cabecera: Trabajo / Solicitud -->
        <div class="mb-3">
        <h5 class="fw-semibold mb-1">
            Trabajo #{{ comentario.trabajo_id }}
            <span v-if="comentario.titulo">
            - {{ comentario.titulo }}
            </span>
        </h5>

        <p class="mb-0 text-muted" v-if="comentario.ciudad">
            {{ comentario.ciudad }}
        </p>
        <p class="mb-0 text-muted" v-else>
            Ciudad no indicada
        </p>
        </div>

        <hr>

          <!-- Estado y puntuación -->
          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="fw-semibold mb-1">Estado</h6>
              <span class="badge" :class="estadoBadgeClass">
                {{ comentario.estado_label || comentario.estado }}
              </span>
            </div>
            <div class="col-md-6">
              <h6 class="fw-semibold mb-1">Puntuación</h6>
              <p class="mb-0">
                <span class="text-warning">
                  <i v-for="i in 5"
                     :key="i"
                     class="bi"
                     :class="i <= comentario.puntuacion ? 'bi-star-fill' : 'bi-star'"></i>
                </span>
                <span class="ms-2">{{ comentario.puntuacion }} / 5</span>
              </p>
            </div>
          </div>

          <!-- Fechas trabajo -->
          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="fw-semibold mb-1">Fecha inicio trabajo</h6>
              <p class="mb-0">
                {{ comentario.fecha_ini || 'Sin iniciar' }}
              </p>
            </div>
            <div class="col-md-6">
              <h6 class="fw-semibold mb-1">Fecha fin trabajo</h6>
              <p class="mb-0">
                {{ comentario.fecha_fin || 'Sin finalizar' }}
              </p>
            </div>
          </div>

          <!-- Dirección obra -->
          <div class="mb-3">
            <h6 class="fw-semibold mb-1">Dirección de la obra</h6>
            <p class="mb-0">
              {{ comentario.dir_obra || 'No indicada' }}
            </p>
          </div>

          <!-- Importe -->
          <div class="mb-3">
            <h6 class="fw-semibold mb-1">Importe del presupuesto</h6>
            <p class="mb-0">
              <span v-if="comentario.total != null">
                {{ formatMoney(comentario.total) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>
          </div>

          <hr>

          <!-- Cliente -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Cliente</h5>
            <template v-if="comentario.cliente">
              <p class="mb-1">
                <strong>
                  {{ comentario.cliente.nombre }}
                  <span v-if="comentario.cliente.apellidos">
                    {{ comentario.cliente.apellidos }}
                  </span>
                </strong>
              </p>
              <p class="mb-0">
                <span v-if="comentario.cliente.email">
                  {{ comentario.cliente.email }}
                </span>
              </p>
            </template>
            <p v-else class="text-muted mb-0">
              No se han podido cargar los datos del cliente.
            </p>
          </div>

          <hr>

          <!-- Opinión -->
          <div class="mb-2">
            <h5 class="fw-semibold mb-2">Opinión del cliente</h5>

            <template v-if="comentario.visible">
              <p v-if="comentario.opinion" class="mb-0">
                {{ comentario.opinion }}
              </p>
              <p v-else class="text-muted mb-0">
                El cliente no ha escrito opinión, solo puntuación.
              </p>
            </template>

            <p v-else class="text-muted mb-0">
              Este comentario no está visible públicamente.
            </p>
          </div>

          <div class="mt-3 text-muted small">
            Fecha comentario:
            {{ comentario.fecha || '—' }}
          </div>
        </div>

        <!-- Cargando -->
        <div class="modal-body text-center py-5" v-else>
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <div>Cargando comentario...</div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
  name: "ComentarioModalPro",
  data() {
    return {
      comentario: {},
      loaded: false,
      modalInstance: null,
    };
  },
  computed: {
    estadoBadgeClass() {
      switch (this.comentario.estado) {
        case "pendiente":
          return "bg-warning text-dark";
        case "publicado":
          return "bg-success";
        case "rechazado":
          return "bg-secondary";
        default:
          return "bg-light text-dark";
      }
    },
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.modal);
  },
  methods: {
    async openModal(id) {
      this.loaded = false;
      this.comentario = {};

      try {
        const resp = await window.axios.get(`/profesional/comentarios/${id}`, {
          headers: { Accept: "application/json" },
        });

        this.comentario = resp.data;
        this.loaded = true;
        this.modalInstance.show();
      } catch (e) {
        console.error(e);
        alert("No se ha podido cargar el comentario.");
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
