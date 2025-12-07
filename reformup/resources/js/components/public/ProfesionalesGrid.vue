<template>
  <div class="my-4">
    <div class="row align-items-start">
      <!-- Filtros: izquierda en desktop, arriba en móvil -->
      <div class="col-12 col-lg-3 mb-3 mb-lg-0">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title mb-3">Buscar profesionales</h5>

            <!-- Empresa -->
            <div class="mb-2">
              <label class="form-label small mb-1">Empresa</label>
              <input
                v-model="filters.empresa"
                type="text"
                class="form-control form-control-sm"
                placeholder="Nombre de la empresa"
              />
            </div>

            <!-- Ciudad -->
            <div class="mb-2">
              <label class="form-label small mb-1">Ciudad</label>
              <input
                v-model="filters.ciudad"
                type="text"
                class="form-control form-control-sm"
                placeholder="Ciudad"
              />
            </div>

            <!-- Provincia -->
            <div class="mb-2">
              <label class="form-label small mb-1">Provincia</label>
              <input
                v-model="filters.provincia"
                type="text"
                class="form-control form-control-sm"
                placeholder="Provincia"
              />
            </div>

            <!-- Valoración mínima -->
            <div class="mb-3">
              <label class="form-label small mb-1">Valoración mínima</label>
              <input
                v-model.number="filters.min_rating"
                type="number"
                min="0"
                max="5"
                step="0.5"
                class="form-control form-control-sm"
                placeholder="Ej: 4"
              />
            </div>

            <!-- Oficios -->
            <div class="mb-3" v-if="oficios.length">
              <label class="form-label small mb-1 d-block">Oficios</label>

              <div class="d-flex flex-wrap gap-2">
                <button
                  type="button"
                  v-for="oficio in oficios"
                  :key="oficio.id"
                  class="btn btn-sm rounded-pill"
                  :class="filters.oficios.includes(oficio.id)
                    ? 'btn-success'
                    : 'btn-outline-success'"
                  @click="toggleOficio(oficio.id, oficio)"
                >
                  {{ oficio.nombre }}
                </button>
              </div>

              <!-- Descripción del oficio seleccionado -->
              <p v-if="oficioSeleccionado" class="mt-2 small text-muted">
                {{ oficioSeleccionado.descripcion }}
              </p>
            </div>

            <div class="d-flex justify-content-between">
              <button
                class="btn btn-primary btn-sm w-100 me-1"
                @click="fetchProfesionales(1)"
              >
                Buscar
              </button>
              <button
                class="btn btn-outline-secondary btn-sm w-100 ms-1"
                @click="resetFilters"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Resultados: derecha en desktop, abajo en móvil -->
      <div class="col-12 col-lg-9">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>

        <div v-else>
          <div v-if="profesionales.length === 0" class="alert alert-info">
            No se han encontrado profesionales con esos criterios.
          </div>

          <div class="row g-3">
            <div
              v-for="pro in profesionales"
              :key="pro.id"
              class="col-12 col-md-6 col-lg-4"
            >
              <div class="card h-100">
                <div class="card-body d-flex flex-column">
                  <div class="d-flex align-items-center mb-2">
                    <div class="me-2">
                      <img
                        v-if="pro.avatar"
                        :src="avatarUrl(pro.avatar)"
                        class="rounded-circle"
                        style="width:40px;height:40px;object-fit:cover;"
                        alt="avatar"
                      />
                      <i
                        v-else
                        class="bi bi-building"
                        style="font-size: 2rem;"
                      ></i>
                    </div>
                    <div style="min-width:0;">
                      <h5 class="card-title mb-0 text-truncate">
                        {{ pro.empresa }}
                      </h5>
                      <small class="text-muted d-block text-truncate">
                        {{ pro.ciudad }}
                        <span v-if="pro.provincia"> - {{ pro.provincia }}</span>
                      </small>
                    </div>
                  </div>

                  <!-- Chips de oficios del profesional -->
                  <div
                    v-if="pro.oficios && pro.oficios.length"
                    class="mb-2"
                  >
                    <span
                      v-for="oficio in pro.oficios"
                      :key="oficio.id"
                      class="badge rounded-pill bg-success text-white me-1 mb-1"
                      :title="oficio.descripcion || ''"
                    >
                      {{ oficio.nombre }}
                    </span>
                  </div>

                  <!-- Valoración -->
                  <p class="mb-1" v-if="pro.puntuacion_media != null">
                    <strong>Valoración:</strong>

                    <span v-for="i in 5" :key="i">
                      <i
                        v-if="i <= Math.floor(Number(pro.puntuacion_media))"
                        class="bi bi-star-fill text-warning"
                      ></i>
                      <i
                        v-else-if="i - Number(pro.puntuacion_media) < 1"
                        class="bi bi-star-half text-warning"
                      ></i>
                      <i
                        v-else
                        class="bi bi-star text-muted"
                      ></i>
                    </span>

                    <span class="ms-1">
                      {{ Number(pro.puntuacion_media).toFixed(1) }} / 5
                    </span>
                  </p>

                  <p class="mb-1" v-if="pro.telefono_empresa">
                    <i class="bi bi-telephone me-1"></i>
                    {{ pro.telefono_empresa }}
                  </p>

                  <p class="mb-1 text-truncate" v-if="pro.email_empresa">
                    <i class="bi bi-envelope me-1"></i>
                    {{ pro.email_empresa }}
                  </p>

                  <p class="mb-2 text-truncate" v-if="pro.web">
                    <i class="bi bi-globe me-1"></i>
                    <a
                      :href="pro.web"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      {{ pro.web }}
                    </a>
                  </p>

                  <div class="mt-auto pt-2">
                    <a
                      :href="`/profesionales/${pro.id}`"
                      class="btn btn-sm btn-outline-primary w-100"
                    >
                      Ver perfil
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Paginación -->
          <nav
            v-if="meta.last_page > 1"
            class="mt-3 d-flex justify-content-center"
          >
            <ul class="pagination pagination-sm mb-0">
              <li
                class="page-item"
                :class="{ disabled: meta.current_page === 1 }"
              >
                <button
                  class="page-link"
                  @click="fetchProfesionales(meta.current_page - 1)"
                  :disabled="meta.current_page === 1"
                >
                  «
                </button>
              </li>

              <li
                class="page-item"
                v-for="page in pagesToShow"
                :key="page"
                :class="{ active: page === meta.current_page }"
              >
                <button class="page-link" @click="fetchProfesionales(page)">
                  {{ page }}
                </button>
              </li>

              <li
                class="page-item"
                :class="{ disabled: meta.current_page === meta.last_page }"
              >
                <button
                  class="page-link"
                  @click="fetchProfesionales(meta.current_page + 1)"
                  :disabled="meta.current_page === meta.last_page"
                >
                  »
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProfesionalesGrid",
  data() {
    return {
      oficioSeleccionado: null,
      profesionales: [],
      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 6,
        total: 0,
      },
      filters: {
        empresa: "",
        ciudad: "",
        provincia: "",
        min_rating: null,
        oficios: [], // varios oficios
      },
      oficios: [], // lista global
      loading: false,
    };
  },
  computed: {
    pagesToShow() {
      const pages = [];
      const from = Math.max(1, this.meta.current_page - 2);
      const to = Math.min(this.meta.last_page, this.meta.current_page + 2);
      for (let i = from; i <= to; i++) {
        pages.push(i);
      }
      return pages;
    },
  },
  mounted() {
    this.fetchProfesionales(1);
  },
  methods: {
    async fetchProfesionales(page = 1) {
      this.loading = true;
      try {
        const params = {
          page,
          per_page: this.meta.per_page,
        };

        if (this.filters.empresa)   params.empresa   = this.filters.empresa;
        if (this.filters.ciudad)    params.ciudad    = this.filters.ciudad;
        if (this.filters.provincia) params.provincia = this.filters.provincia;
        if (this.filters.min_rating != null)
          params.min_rating = this.filters.min_rating;

        if (this.filters.oficios.length) {
          // axios lo convertirá en oficios[]=1&oficios[]=3...
          params.oficios = this.filters.oficios;
        }

        const resp = await window.axios.get("/api/profesionales", { params });

        this.profesionales = resp.data.data || [];
        this.meta = resp.data.meta || this.meta;

        // Cargamos oficios desde la misma respuesta
        if (resp.data.oficios) {
          this.oficios = resp.data.oficios;
        }
      } catch (e) {
        console.error("Error cargando profesionales:", e);

        if (window.Swal) {
          const msg =
            e.response?.data?.message ||
            "Ha ocurrido un error al cargar los profesionales.";

          window.Swal.fire({
            icon: "error",
            title: "Error",
            text: msg,
          });
        } else {
          alert("No se han podido cargar los profesionales.");
        }
      } finally {
        this.loading = false;
      }
    },

    resetFilters() {
      this.filters = {
        empresa: "",
        ciudad: "",
        provincia: "",
        min_rating: null,
        oficios: [],
      };
      this.oficioSeleccionado = null;
      this.fetchProfesionales(1);
    },

    toggleOficio(id, oficioObj) {
      const idx = this.filters.oficios.indexOf(id);

      if (idx === -1) {
        // Lo añadimos al filtro
        this.filters.oficios.push(id);
        // Mostramos la descripción del último oficio pulsado
        this.oficioSeleccionado = oficioObj;
      } else {
        // Lo quitamos del filtro
        this.filters.oficios.splice(idx, 1);

        // Si el oficio deseleccionado es el que está mostrado, lo limpiamos
        if (this.oficioSeleccionado && this.oficioSeleccionado.id === id) {
          this.oficioSeleccionado = null;
        }
      }
    },

    avatarUrl(path) {
      return `/storage/${path}`;
    },
  },
};
</script>
