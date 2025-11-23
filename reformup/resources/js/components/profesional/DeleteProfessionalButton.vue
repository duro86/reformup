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
  name: "DeleteProfessionalButton",
  props: {
    formId: {
      type: String,
      required: true,
    },
    empresa: {
      type: String,
      required: true,
    },
    userNombre: {
      type: String,
      required: false,
      default: "",
    },
    userEmail: {
      type: String,
      required: false,
      default: "",
    },
  },
  methods: {
    async confirmDelete() {
      const empresa = this.empresa;
      const userNombre = this.userNombre;
      const userEmail = this.userEmail;

      let title = "Eliminar perfil profesional";
      let text =
        `¿Seguro que quieres eliminar el perfil profesional de "${empresa}"? `;

      if (userNombre || userEmail) {
        text += `\nUsuario asociado: ${userNombre} (${userEmail}). `;
      }

      text += "Esta acción no se puede deshacer.";

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
