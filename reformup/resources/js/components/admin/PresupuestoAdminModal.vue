<template>
  <div class="modal fade" id="presupuestoAdminModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">
            Presupuesto #{{ presupuesto.id }}
            <span v-if="presupuesto.solicitud && presupuesto.solicitud.titulo">
              – {{ presupuesto.solicitud.titulo }}
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Contenido cargado -->
        <div class="modal-body" v-if="loaded">
          <!-- Estado + total -->
          <div class="mb-3">
            <h6 class="fw-semibold">Resumen del presupuesto</h6>
            <p class="mb-1">
              <strong>Estado:</strong>
              <span class="badge bg-secondary">
                {{ presupuesto.estado || '—' }}
              </span>
            </p>
            <p class="mb-0">
              <strong>Total:</strong>
              <span v-if="presupuesto.total != null">
                {{ formatMoney(presupuesto.total) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>
          </div>

          <hr>

          <!-- Solicitud -->
          <div class="mb-3" v-if="presupuesto.solicitud">
            <h6 class="fw-semibold">Solicitud asociada</h6>
            <p class="mb-1">
              <strong>Título:</strong>
              {{ presupuesto.solicitud.titulo || ('Solicitud #' + presupuesto.solicitud.id) }}
            </p>
            <p class="mb-1">
              <strong>Estado solicitud:</strong>
              {{ formatEstado(presupuesto.solicitud.estado) }}
            </p>
            <p class="mb-1">
              <strong>Ubicación:</strong>
              <span v-if="presupuesto.solicitud.ciudad">{{ presupuesto.solicitud.ciudad }}</span>
              <span v-if="presupuesto.solicitud.ciudad && presupuesto.solicitud.provincia"> - </span>
              <span v-if="presupuesto.solicitud.provincia">{{ presupuesto.solicitud.provincia }}</span>
            </p>
            <p class="mb-0">
              <strong>Presupuesto máx. cliente:</strong>
              <span v-if="presupuesto.solicitud.presupuesto_max">
                {{ formatMoney(presupuesto.solicitud.presupuesto_max) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>
          </div>

          <hr>

          <!-- Cliente -->
          <div class="mb-3">
            <h6 class="fw-semibold">Datos del cliente</h6>
            <p class="mb-0" v-if="presupuesto.cliente">
              <strong>
                {{ presupuesto.cliente.nombre }}
                <span v-if="presupuesto.cliente.apellidos"> {{ presupuesto.cliente.apellidos }}</span>
              </strong><br>
              <span v-if="presupuesto.cliente.email">
                {{ presupuesto.cliente.email }}<br>
              </span>
              <span v-if="presupuesto.cliente.telefono">
                {{ presupuesto.cliente.telefono }}
              </span>
            </p>
            <p v-else class="text-muted mb-0">Sin datos de cliente asociados.</p>
          </div>

          <!-- Profesional -->
          <div class="mb-3">
            <h6 class="fw-semibold">Datos del profesional</h6>
            <p class="mb-0" v-if="presupuesto.profesional">
              <strong>{{ presupuesto.profesional.empresa }}</strong><br>
              <span v-if="presupuesto.profesional.email_empresa">
                {{ presupuesto.profesional.email_empresa }}<br>
              </span>
              <span v-if="presupuesto.profesional.telefono_empresa">
                {{ presupuesto.profesional.telefono_empresa }}<br>
              </span>
              <span class="text-muted" v-if="presupuesto.profesional.ciudad || presupuesto.profesional.provincia">
                {{ presupuesto.profesional.ciudad }}
                <span v-if="presupuesto.profesional.ciudad && presupuesto.profesional.provincia"> - </span>
                {{ presupuesto.profesional.provincia }}
              </span>
            </p>
            <p v-else class="text-muted mb-0">Sin profesional asignado.</p>
          </div>

          <hr>
          <!-- Notas -->
          <!--<div class="mb-3">
            <h6 class="fw-semibold">Notas del presupuesto</h6>
            <p v-if="presupuesto.notas">
              {{ presupuesto.notas }}
            </p>
            <p v-else class="text-muted mb-0">Sin notas.</p>
          </div>-->

          <!-- Notas - Detalle -->
        <div class="mb-3" v-if="presupuesto.notas">
          <h6 class="fw-semibold mb-1">Notas del presupuesto</h6>
          <div class="border rounded p-2 bg-light small"
              v-html="presupuesto.notas">
          </div>
        </div>

          <!-- Fechas -->
          <div class="small text-muted">
            <div>Fecha presupuesto: {{ presupuesto.fecha || '—' }}</div>
            <div>Creado: {{ presupuesto.created_at || '—' }}</div>
            <div>Última actualización: {{ presupuesto.updated_at || '—' }}</div>
          </div>

          <!-- PDF -->
          <div class="mt-3">
            <h6 class="fw-semibold">Documento adjunto</h6>
            <div v-if="presupuesto.docu_pdf">
              <a :href="presupuesto.docu_pdf" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-pdf"></i>
                Ver PDF
              </a>
            </div>
            <p v-else class="text-muted mb-0">No hay documento PDF asociado.</p>
          </div>
        </div>

        <!-- Estado cargando -->
        <div class="modal-body text-center py-5" v-else>
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <div>Cargando presupuesto...</div>
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
import { Modal } from 'bootstrap';

export default {
  name: 'PresupuestoAdminModal',
  data() {
    return {
      presupuesto: {},
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
      this.presupuesto = {};

      try {
        const resp = await window.axios.get(`/admin/presupuestos/${id}`, {
          headers: { Accept: 'application/json' },
        });

        this.presupuesto = resp.data;
        this.loaded = true;
        this.modalInstance.show();
      } catch (e) {
        console.error(e);
        alert('No se ha podido cargar el presupuesto.');
      }
    },
    formatMoney(value) {
      if (value == null) return '';
      return Number(value).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    formatEstado(estado) {
      if (!estado) return '—';
      return estado.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    },
  },
};
</script>
