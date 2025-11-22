<template>
  <div class="modal fade" id="solicitudAdminModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Solicitud #{{ solicitud.id }}
            <span v-if="solicitud.titulo">
              - {{ solicitud.titulo }}
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Contenido -->
        <div class="modal-body" v-if="loaded">
          <!-- Datos generales -->
          <div class="mb-3">
            <h6 class="fw-semibold mb-1">Datos generales</h6>
            <p class="mb-1">
              <strong>Estado:</strong>
              <span class="badge" :class="estadoBadgeClass">
                {{ estadoLabel }}
              </span>
            </p>
            <p class="mb-1">
              <strong>Ciudad:</strong>
              <span>{{ solicitud.ciudad || 'No indicada' }}</span>
              <span v-if="solicitud.provincia">
                - {{ solicitud.provincia }}
              </span>
            </p>
            <p class="mb-1">
              <strong>Presupuesto máx.:</strong>
              <span v-if="solicitud.presupuesto_max != null">
                {{ formatMoney(solicitud.presupuesto_max) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>
            <p class="mb-1">
              <strong>Fecha de solicitud:</strong>
              <span>{{ solicitud.fecha || solicitud.created_at || '—' }}</span>
            </p>
            <p class="mb-1">
              <strong>Última actualización:</strong>
              <span>{{ solicitud.updated_at || '—' }}</span>
            </p>
          </div>

          <!-- Descripción -->
          <div class="mb-3" v-if="solicitud.descripcion">
            <h6 class="fw-semibold mb-1">Descripción de la solicitud</h6>
            <p class="mb-0">
              {{ solicitud.descripcion }}
            </p>
          </div>

          <hr>

          <!-- Cliente -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Cliente</h5>
            <template v-if="solicitud.cliente">
              <p class="mb-1">
                <strong>
                  {{ solicitud.cliente.nombre }}
                  <span v-if="solicitud.cliente.apellidos">
                    {{ solicitud.cliente.apellidos }}
                  </span>
                </strong>
              </p>
              <p class="mb-0">
                <span v-if="solicitud.cliente.email">
                  {{ solicitud.cliente.email }}
                </span>
                <br v-if="solicitud.cliente.telefono && solicitud.cliente.email">
                <span v-if="solicitud.cliente.telefono">
                  {{ solicitud.cliente.telefono }}
                </span>
              </p>
            </template>
            <p v-else class="text-muted mb-0">
              Sin datos de cliente.
            </p>
          </div>

          <hr>

          <!-- Profesional asignado -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Profesional asignado</h5>
            <template v-if="solicitud.profesional">
              <p class="mb-1">
                <strong>{{ solicitud.profesional.empresa }}</strong>
              </p>
              <p class="mb-0">
                <span v-if="solicitud.profesional.email_empresa">
                  {{ solicitud.profesional.email_empresa }}
                </span>
                <br v-if="solicitud.profesional.telefono_empresa && solicitud.profesional.email_empresa">
                <span v-if="solicitud.profesional.telefono_empresa">
                  {{ solicitud.profesional.telefono_empresa }}
                </span>
                <br v-if="solicitud.profesional.ciudad">
                <span v-if="solicitud.profesional.ciudad">
                  {{ solicitud.profesional.ciudad }}
                  <span v-if="solicitud.profesional.provincia">
                    - {{ solicitud.profesional.provincia }}
                  </span>
                </span>
              </p>
            </template>
            <p v-else class="text-muted mb-0">
              No hay profesional asignado a esta solicitud.
            </p>
          </div>

          <hr>

          <!-- Presupuesto y trabajo asociados -->
          <div class="row">
            <!-- Presupuesto asociado -->
            <div class="col-md-6 mb-3">
              <h5 class="fw-semibold mb-2">Presupuesto asociado</h5>

              <template v-if="solicitud.presupuesto">
                <p class="mb-1">
                  <strong>Ref #</strong> {{ solicitud.presupuesto.id }}
                </p>
                <p class="mb-1">
                  <strong>Estado:</strong>
                  <span class="badge" :class="presuBadgeClass">
                    {{ presuEstadoLabel }}
                  </span>
                </p>
                <p class="mb-0">
                  <strong>Total:</strong>
                  <span v-if="solicitud.presupuesto.total != null">
                    {{ formatMoney(solicitud.presupuesto.total) }} €
                  </span>
                  <span v-else class="text-muted">No indicado</span>
                </p>
              </template>

              <p v-else class="text-muted mb-0">
                No hay presupuesto asociado a esta solicitud.
              </p>
            </div>

            <!-- Trabajo asociado -->
            <div class="col-md-6 mb-3">
              <h5 class="fw-semibold mb-2">Trabajo asociado</h5>

              <template v-if="solicitud.trabajo">
                <p class="mb-1">
                  <strong>Ref #</strong> {{ solicitud.trabajo.id }}
                </p>
                <p class="mb-1">
                  <strong>Estado:</strong>
                  <span class="badge" :class="trabajoBadgeClass">
                    {{ trabajoEstadoLabel }}
                  </span>
                </p>
                <p class="mb-0">
                  <strong>Fechas:</strong>
                  <span>
                    {{ solicitud.trabajo.fecha_ini || 'Sin inicio' }}
                    &nbsp;–&nbsp;
                    {{ solicitud.trabajo.fecha_fin || 'Sin fin' }}
                  </span>
                </p>
              </template>

              <p v-else class="text-muted mb-0">
                No hay trabajo asociado a esta solicitud.
              </p>
            </div>
          </div>

        </div>

        <!-- Cargando -->
        <div class="modal-body text-center py-5" v-else>
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <div>Cargando solicitud...</div>
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
  name: "SolicitudAdminModal",
  data() {
    return {
      solicitud: {},
      loaded: false,
      modalInstance: null,
    };
  },
  computed: {
    estadoLabel() {
      if (!this.solicitud.estado) return "—";
      return this.solicitud.estado
        .replace("_", " ")
        .replace(/^\w/, c => c.toUpperCase());
    },
    estadoBadgeClass() {
      switch (this.solicitud.estado) {
        case "abierta":
          return "bg-primary";
        case "en_revision":
          return "bg-warning text-dark";
        case "cerrada":
          return "bg-success";
        case "cancelada":
          return "bg-secondary";
        default:
          return "bg-light text-dark";
      }
    },
    // 🔹 Presupuesto
  presuEstadoLabel() {
    const est = this.solicitud.presupuesto?.estado;
    if (!est) return "—";
    return est.replace("_", " ").replace(/^\w/, c => c.toUpperCase());
  },
  presuBadgeClass() {
    const est = this.solicitud.presupuesto?.estado;
    switch (est) {
      case "pendiente":
        return "bg-warning text-dark";
      case "aceptado":
        return "bg-success";
      case "rechazado":
        return "bg-secondary";
      default:
        return "bg-light text-dark";
    }
  },

  // 🔹 Trabajo
  trabajoEstadoLabel() {
    const est = this.solicitud.trabajo?.estado;
    if (!est) return "—";
    return est.replace("_", " ").replace(/^\w/, c => c.toUpperCase());
  },
  trabajoBadgeClass() {
    const est = this.solicitud.trabajo?.estado;
    switch (est) {
      case "previsto":
        return "bg-primary";
      case "en_curso":
        return "bg-warning text-dark";
      case "finalizado":
        return "bg-success";
      case "cancelado":
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
      this.solicitud = {};

      try {
        const resp = await window.axios.get(`/admin/solicitudes/${id}`, {
          headers: { Accept: "application/json" },
        });

        this.solicitud = resp.data;
        this.loaded = true;
        this.modalInstance.show();
      } catch (e) {
        console.error(e);
        alert("No se ha podido cargar la solicitud.");
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
