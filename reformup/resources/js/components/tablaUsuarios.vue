<template>
  <div>
    <input v-model="search" placeholder="Buscar por nombre o teléfono..." class="form-control mb-3">

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Avatar</th>
          <th>Nombre</th>
          <th>Apellidos</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="usuario in filteredUsers" :key="usuario.id">
          <td><img :src="usuario.avatar ? avatarUrl(usuario.avatar) : defaultAvatar" class="rounded-circle" style="width: 40px; height: 40px;"></td>
          <td>{{ usuario.nombre }}</td>
          <td>{{ usuario.apellidos }}</td>
          <td>{{ usuario.email }}</td>
          <td>{{ usuario.telefono }}</td>
          <td>
            <button class="btn btn-primary btn-sm" @click="ver(usuario)">Ver</button>
            <button class="btn btn-warning btn-sm" @click="editar(usuario)">Editar</button>
            <button class="btn btn-danger btn-sm" @click="eliminar(usuario)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Paginación básica -->
    <nav>
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: !usuarios.prev_page_url }">
          <button class="page-link" @click="fetchPage(usuarios.current_page - 1)" :disabled="!usuarios.prev_page_url">Anterior</button>
        </li>
        <li class="page-item disabled">
          <span class="page-link">{{ usuarios.current_page }} / {{ usuarios.last_page }}</span>
        </li>
        <li class="page-item" :class="{ disabled: !usuarios.next_page_url }">
          <button class="page-link" @click="fetchPage(usuarios.current_page + 1)" :disabled="!usuarios.next_page_url">Siguiente</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
export default {
  props: {
    initialUsers: {
      type: Object, // Laravel paginación llega como objeto con data y metadatos
      required: true,
    },
  },
  data() {
    return {
      usuarios: this.initialUsers,
      search: '',
      defaultAvatar: '/img/default_avatar.png', // Cambia a ruta real
    };
  },
  computed: {
    filteredUsers() {
      let filtered = this.usuarios.data;

      if (this.search) {
        const searchLower = this.search.toLowerCase();
        filtered = filtered.filter(u =>
          u.nombre.toLowerCase().includes(searchLower) ||
          u.telefono.toLowerCase().includes(searchLower));
      }
      return filtered;
    },
  },
  methods: {
    avatarUrl(path) {
      return `/storage/${path}`; // O la ruta donde guardes avatars
    },
    ver(usuario) {
      alert('Ver usuario ' + usuario.nombre);
      // Redirigir o abrir modal
    },
    editar(usuario) {
      alert('Editar usuario ' + usuario.nombre);
      // Abrir formulario editar
    },
    eliminar(usuario) {
      if (confirm(`¿Eliminar usuario ${usuario.nombre}?`)) {
        alert('Usuario eliminado (simulado)');
        // Llamar API para eliminar y luego refrescar lista
      }
    },
    fetchPage(page) {
      if (!page || page < 1 || page > this.usuarios.last_page) return;
      axios.get(`/api/users?page=${page}`).then(({ data }) => {
        this.usuarios = data;
      });
    },
  },
};
</script>

<style scoped>
.page-link {
  cursor: pointer;
}
</style>
