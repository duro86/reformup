<template>
  <button
    type="button"
    class="btn btn-danger btn-sm px-2 py-1"
    @click="confirmDelete"
  >
    Eliminar
  </button>
</template>

<script>
export default {
  name: "DeleteUserButton",
  props: {
    formId: {
      type: String,
      required: true,
    },
    userNombre: {
      type: String,
      required: true,
    },
    userEmail: {
      type: String,
      required: true,
    },
    tienePerfil: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    async confirmDelete() {
      const nombre = this.userNombre;
      const email = this.userEmail;
      const tienePerfil = this.tienePerfil;

      let title;
      let text;

      if (tienePerfil) {
        title = "Eliminar usuario y empresa";
        text =
          `El usuario ${nombre} (${email}) tiene un perfil profesional / empresa registrado. ` +
          `Si continúas, se eliminará el usuario y también los datos de su empresa. ` +
          `Esta acción no se puede deshacer.`;
      } else {
        title = "Eliminar usuario";
        text =
          `¿Seguro que quieres eliminar al usuario ${nombre} (${email})? ` +
          `Esta acción no se puede deshacer.`;
      }

      // Si existe SweetAlert2, usamos eso
      if (window.Swal) {
        const result = await Swal.fire({
          icon: "warning",
          title: title,
          text: text,
          showCancelButton: true,
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar",
        });

        if (!result.isConfirmed) {
          return;
        }
      } else {
        // Fallback: confirm nativo del navegador
        const ok = window.confirm(text);
        if (!ok) return;
      }

      const form = document.getElementById(this.formId);
      if (form) {
        form.submit();
      }
    },
  },
};
</script>
