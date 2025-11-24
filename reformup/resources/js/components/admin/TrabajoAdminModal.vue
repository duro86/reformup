<template>
  <div class="modal fade" id="trabajoAdminModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">
            Trabajo #{{ trabajo.id }}
            <span v-if="trabajo.solicitud && trabajo.solicitud.titulo">
              – {{ trabajo.solicitud.titulo }}
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Contenido cargado -->
        <div class="modal-body" v-if="loaded">

          <!-- Estado + fechas -->
          <div class="mb-3">
            <h6 class="fw-semibold">Estado y fechas</h6>
            <p class="mb-1">
              <strong>Estado:</strong>
              <span class="badge bg-secondary">
                {{ formatEstado(trabajo.estado) }}
              </span>
            </p>
            <p class="mb-1">
              <strong>Fecha inicio:</strong>
              {{ trabajo.fecha_ini || '—' }}
            </p>
            <p class="mb-0">
              <strong>Fecha fin:</strong>
              {{ trabajo.fecha_fin || '—' }}
            </p>
          </div>

          <!-- Descripción del trabajo -->
          <div class="mb-3">
            <h6 class="fw-semibold">Descripción del trabajo</h6>
            <p v-if="trabajo.descripcion">
              {{ trabajo.descripcion }}
            </p>
            <p v-else class="text-muted mb-0">
              Sin descripción específica del trabajo.
            </p>
          </div>

           <!-- DIrección del trabajo -->
          <div class="mb-3">
            <h6 class="fw-semibold">Dirección del trabajo</h6>
            <p v-if="trabajo.dir_obra">
              {{ trabajo.dir_obra }}
            </p>
            <p v-else class="text-muted mb-0">
              Sin descripción específica del trabajo.
            </p>
          </div>

          <hr>

          <!-- Solicitud asociada -->
          <div class="mb-3" v-if="trabajo.solicitud">
            <h6 class="fw-semibold">Solicitud asociada</h6>
            <p class="mb-1">
              <strong>Título:</strong>
              {{ trabajo.solicitud.titulo || ('Solicitud #' + trabajo.solicitud.id) }}
            </p>
            <p class="mb-1">
              <strong>Estado solicitud:</strong>
              {{ formatEstado(trabajo.solicitud.estado) }}
            </p>
            <p class="mb-1">
              <strong>Ubicación:</strong>
              <span v-if="trabajo.solicitud.ciudad">{{ trabajo.solicitud.ciudad }}</span>
              <span v-if="trabajo.solicitud.ciudad && trabajo.solicitud.provincia"> - </span>
              <span v-if="trabajo.solicitud.provincia">{{ trabajo.solicitud.provincia }}</span>
            </p>
            <p class="mb-0">
              <strong>Presupuesto máximo cliente:</strong>
              <span v-if="trabajo.solicitud.presupuesto_max">
                {{ formatMoney(trabajo.solicitud.presupuesto_max) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>
          </div>

          <!-- Presupuesto asociado -->
          <div class="mb-3" v-if="trabajo.presupuesto">
            <h6 class="fw-semibold">Presupuesto asociado</h6>
            <p class="mb-1">
              <strong>ID:</strong> #{{ trabajo.presupuesto.id }}
            </p>
            <p class="mb-1">
              <strong>Estado:</strong> {{ formatEstado(trabajo.presupuesto.estado) }}
            </p>
            <p class="mb-0">
              <strong>Total:</strong>
              <span v-if="trabajo.presupuesto.total != null">
                {{ formatMoney(trabajo.presupuesto.total) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>
          </div>

          <hr>

          <!-- Cliente -->
          <div class="mb-3">
            <h6 class="fw-semibold">Datos del cliente</h6>
            <p v-if="trabajo.cliente" class="mb-0">
              <strong>
                {{ trabajo.cliente.nombre }}
                <span v-if="trabajo.cliente.apellidos"> {{ trabajo.cliente.apellidos }}</span>
              </strong><br>
              <span v-if="trabajo.cliente.email">
                {{ trabajo.cliente.email }}<br>
              </span>
              <span v-if="trabajo.cliente.telefono">
                {{ trabajo.cliente.telefono }}
              </span>
            </p>
            <p v-else class="text-muted mb-0">Sin datos de cliente.</p>
          </div>

          <!-- Profesional -->
          <div class="mb-3">
            <h6 class="fw-semibold">Datos del profesional</h6>
            <p v-if="trabajo.profesional" class="mb-0">
              <strong>{{ trabajo.profesional.empresa }}</strong><br>
              <span v-if="trabajo.profesional.email_empresa">
                {{ trabajo.profesional.email_empresa }}<br>
              </span>
              <span v-if="trabajo.profesional.telefono_empresa">
                {{ trabajo.profesional.telefono_empresa }}<br>
              </span>
              <span v-if="trabajo.profesional.ciudad || trabajo.profesional.provincia" class="text-muted">
                {{ trabajo.profesional.ciudad }}
                <span v-if="trabajo.profesional.ciudad && trabajo.profesional.provincia"> - </span>
                {{ trabajo.profesional.provincia }}
              </span>
            </p>
            <p v-else class="text-muted mb-0">Sin profesional asignado.</p>
          </div>

          <!-- Fechas sistema -->
          <div class="small text-muted">
            <div>Creado: {{ trabajo.created_at || '—' }}</div>
            <div>Última actualización: {{ trabajo.updated_at || '—' }}</div>
          </div>

        </div>

        <!-- Estado cargando -->
        <div class="modal-body text-center py-5" v-else>
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <div>Cargando trabajo...</div>
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
  name: 'TrabajoAdminModal',
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
        const resp = await window.axios.get(`/admin/trabajos/${id}`, {
          headers: { Accept: 'application/json' },
        });

        this.trabajo = resp.data;
        this.loaded = true;
        this.modalInstance.show();
      } catch (e) {
        console.error(e);
        alert('No se ha podido cargar el trabajo.');
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
      return estado
        .toString()
        .replace('_', ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
    },
  },
};
</script>
