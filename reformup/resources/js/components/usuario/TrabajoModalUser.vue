<template>
  <div class="modal fade" id="trabajoModal" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Trabajo #{{ trabajo.id }}
            <span v-if="trabajo.solicitud && trabajo.solicitud.titulo">
              - {{ trabajo.solicitud.titulo }}
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Contenido cargado -->
        <div class="modal-body" v-if="loaded">
          <!-- Bloque: Estado y fechas -->
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

          <!-- Presupuesto -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Datos del presupuesto</h5>

            <p class="mb-1" v-if="trabajo.presupuesto">
              <strong>Nombre:</strong>
              <span>
                {{ trabajo.presupuesto.nombre || (' Presupuesto #' + trabajo.presupuesto.id) }}
              </span>
            </p>

            <p class="mb-1" v-if="trabajo.solicitud && trabajo.solicitud.presupuesto_max">
              <strong>Presupuesto Solicitud Max:</strong>
              <span v-if="trabajo.solicitud.presupuesto_max != null">
                {{ (' ') + formatMoney( trabajo.solicitud.presupuesto_max) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>

            <p class="mb-1" v-if="trabajo.presupuesto">
              <strong>Total Presupuesto Profesional:</strong>
              <span v-if="trabajo.presupuesto.total != null">
                {{ (' ') + formatMoney( trabajo.presupuesto.total) }} €
              </span>
              <span v-else class="text-muted">No indicado</span>
            </p>

            <!--<p class="mb-1" v-if="trabajo.presupuesto && trabajo.presupuesto.notas">
              <strong>Notas:</strong><br>
              <span>{{ trabajo.presupuesto.notas }}</span>
            </p>-->
            
            <!-- Notas - Detalle -->
        <div class="mb-3" v-if="trabajo.presupuesto && trabajo.presupuesto.notas">
          <h6 class="fw-semibold mb-1">Notas del presupuesto</h6>
          <div class="border rounded p-2 bg-light small"
              v-html="trabajo.presupuesto.notas">
          </div>
        </div>

            <p v-if="!trabajo.presupuesto" class="text-muted mb-0">
              No hay datos de presupuesto asociados.
            </p>
          </div>

          <!-- Solicitud -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Datos de la Solicitud</h5>

            <p class="mb-1" v-if="trabajo.solicitud">
              <strong>ID:</strong>
              <span>
                {{ ('  Solicitud #' + trabajo.solicitud.id) }}
              </span>
            </p>

            <p class="mb-1" v-if="trabajo.solicitud && trabajo.solicitud.ciudad">
              <strong>Ciudad:</strong>
              <span>{{ (' ') +trabajo.solicitud.ciudad }}</span>
            </p>

            <p v-if="!trabajo.presupuesto" class="text-muted mb-0">
              No hay datos de presupuesto asociados.
            </p>
          </div>

          <!-- Profesional -->
          <div class="mb-3">
            <h5 class="fw-semibold mb-2">Profesional</h5>
            <template v-if="trabajo.presupuesto && trabajo.presupuesto.profesional">
              <p class="mb-1">
                <strong>{{ trabajo.presupuesto.profesional.empresa }}</strong>
              </p>
              <p class="mb-1">
                <span v-if="trabajo.presupuesto.profesional.email_empresa">
                  {{ trabajo.presupuesto.profesional.email_empresa }}<br>
                </span>
                <span v-if="trabajo.presupuesto.profesional.telefono_empresa">
                  {{ trabajo.presupuesto.profesional.telefono_empresa }}
                </span>
              </p>
              <p class="mb-0 text-muted small">
                {{ trabajo.presupuesto.profesional.ciudad }}
                <span v-if="trabajo.presupuesto.profesional.ciudad && trabajo.presupuesto.profesional.provincia"> - </span>
                {{ trabajo.presupuesto.profesional.provincia }}
              </p>
            </template>
            <p v-else class="text-muted mb-0">
              No hay datos de profesional asociados.
            </p>
          </div>

          <hr>

          <!-- Cliente -->
          <div>
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
              <p class="mb-0">
                <span v-if="trabajo.cliente.telefono">
                  {{ trabajo.cliente.telefono }}
                </span>
              </p>
            </template>
            <p v-else class="text-muted mb-0">
              No se han podido cargar los datos del cliente.
            </p>
          </div>
        </div>

        <!-- Estado: cargando -->
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
  name: 'TrabajoModal',
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
      this.loaded  = false;
      this.trabajo = {};

      try {
        const resp = await window.axios.get(`/usuario/trabajos/${id}`, {
          headers: { Accept: 'application/json' },
        });

        this.trabajo = resp.data;
        this.loaded  = true;
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
  },
};
</script>
