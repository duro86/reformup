<template>
  <div class="modal fade" id="ComentarioModalUser" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">

      <div class="modal-content bg-light">

        <!-- CABECERA -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            Comentario
            <span v-if="refCliente">#{{ refCliente }}</span>
            <span v-if="comentario.titulo"> - {{ comentario.titulo }}</span>
        </h5>

          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- CONTENIDO -->
        <div class="modal-body" v-if="loaded">

          <!-- Contexto (Trabajo / Profesional) -->
          <div class="mb-3">
            <h6 class="fw-semibold mb-1">
              <span v-if="comentario.titulo">  {{ comentario.titulo }}</span>
            </h6>

            <p class="mb-0 text-muted" v-if="comentario.profesional?.empresa">
              Profesional: {{ comentario.profesional.empresa }}
            </p>
            <p class="mb-0 text-muted" v-else>
              Profesional no disponible
            </p>

            <p class="mb-0 text-muted" v-if="comentario.ciudad">
              {{ comentario.ciudad }}
            </p>
            <p class="mb-0 text-muted" v-else>
              Ciudad no indicada
            </p>
          </div>

          <hr>

          <!-- Estado / Visible / Puntuación -->
          <div class="row mb-3">
            <div class="col-md-4">
              <h6 class="fw-semibold mb-1">Estado</h6>
              <span class="badge" :class="estadoBadgeClass">
                {{ comentario.estado_label || comentario.estado }}
              </span>
            </div>

            <div class="col-md-4">
              <h6 class="fw-semibold mb-1">Visibilidad</h6>
              <span class="badge" :class="comentario.visible ? 'bg-success' : 'bg-warning text-dark'">
                {{ comentario.visible ? 'Visible' : 'Oculto (pendiente)' }}
              </span>
            </div>

            <div class="col-md-4">
              <h6 class="fw-semibold mb-1">Puntuación</h6>
              <p class="mb-0">
                <span class="text-warning">
                  <i v-for="i in 5"
                     :key="i"
                     class="bi"
                     :class="i <= puntuacionSafe ? 'bi-star-fill' : 'bi-star'"></i>
                </span>
                <span class="ms-2">{{ puntuacionSafe }} / 5</span>
              </p>
            </div>
          </div>

          <!-- Fechas (si las mandas) -->
          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="fw-semibold mb-1">Fecha inicio</h6>
              <p class="mb-0">{{ comentario.fecha_ini || 'Sin iniciar' }}</p>
            </div>
            <div class="col-md-6">
              <h6 class="fw-semibold mb-1">Fecha fin</h6>
              <p class="mb-0">{{ comentario.fecha_fin || 'Sin finalizar' }}</p>
            </div>
          </div>

          <!-- Opinión -->
          <div class="mb-2">
            <h6 class="fw-semibold mb-2">Tu opinión</h6>

            <!-- Si NO es visible, lo avisamos pero seguimos mostrando el detalle -->
            <p v-if="!comentario.visible" class="text-muted mb-2">
              Este comentario está pendiente de revisión, por eso no es visible públicamente.
            </p>

            <div v-if="opinionSafe"
                 class="border rounded p-2 bg-white small"
                 v-html="opinionSafe"></div>

            <p v-else class="text-muted mb-0">
              No has escrito opinión, solo puntuación.
            </p>
          </div>

          <!-- Imágenes (si las mandas como array) -->
          <div class="mt-3" v-if="imagenesSafe.length">
            <h6 class="fw-semibold mb-2">Imágenes</h6>
            <div class="d-flex flex-wrap gap-2">
              <a v-for="(img, idx) in imagenesSafe"
                 :key="idx"
                 :href="img.url"
                 target="_blank"
                 rel="noopener"
                 class="d-inline-block">
                <img :src="img.url" alt="Imagen comentario"
                     style="width:96px;height:96px;object-fit:cover;border-radius:8px;">
              </a>
            </div>
          </div>

        </div>

        <!-- CARGANDO -->
        <div class="modal-body text-center py-5" v-else>
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <div>Cargando comentario...</div>
        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
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
  name: "ComentarioModalUser",
  data() {
    return {
      comentario: {},
      loaded: false,
      modalInstance: null,
      refCliente: null,
    };
  },
  computed: {
    estadoBadgeClass() {
      switch ((this.comentario.estado || "").toLowerCase().trim()) {
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
    // FIX CLAVE: nunca null
    opinionSafe() {
      const raw = this.comentario?.opinion;
      const str = (raw == null) ? "" : String(raw);
      return str.trim().length ? str : "";
    },
    puntuacionSafe() {
      const n = Number(this.comentario?.puntuacion || 0);
      return Number.isFinite(n) ? n : 0;
    },
    imagenesSafe() {
      const imgs = this.comentario?.imagenes;
      return Array.isArray(imgs) ? imgs : [];
    },
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.modal);
  },
  methods: {
    async openModal(id, refCliente = null) {
      this.loaded = false;
      this.comentario = {};
       this.refCliente = refCliente;

      try {
        const resp = await window.axios.get(`/usuario/comentarios/${id}`, {
          headers: { Accept: "application/json" },
        });

        this.comentario = resp.data || {};
        this.loaded = true;
        this.modalInstance.show();
      } catch (e) {
        console.error(e);
        alert("No se ha podido cargar el comentario.");
      }
    },
  },
};
</script>
