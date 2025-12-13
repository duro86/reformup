/**
 * En el archivo resources/js/app.js se inicializa la aplicación Vue utilizada en el frontend.
Se importan los componentes Vue correspondientes a los distintos modales (usuarios, profesionales, solicitudes, trabajos, comentarios, etc.) y se registran de forma global.
Además, se definen métodos como openUserModal, openProfessionalModal o openTrabajoAdminModal, que actúan como puente entre las vistas Blade y los componentes Vue utilizando referencias ($refs).
Esto permite abrir modales dinámicos desde enlaces o botones HTML sin recargar la página, mejorando la experiencia de usuario y la fluidez de la interfaz.
 */
import "./bootstrap";
import { createApp } from "vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
window.ClassicEditor = ClassicEditor;

import UserModal from "./components/usuario/UserModal.vue";
import DeleteUserButton from "./components/admin/DeleteUserButton.vue";
import DeleteProfessionalButton from "./components/profesional/DeleteProfessionalButton.vue";
import ProfessionalModal from "./components/profesional/ProfessionalModal.vue";
import SolicitudModal from "./components/profesional/SolicitudModal.vue";
import TrabajoModal from "./components/usuario/TrabajoModalUser.vue";
import TrabajoModalPro from "./components/profesional/TrabajoModalPro.vue";
import ComentarioModalPro from "./components/profesional/ComentarioModalPro.vue";
import ComentarioModalAdmin from "./components/admin/ComentarioModalAdmin.vue";
import SolicitudAdminModal from "./components/admin/SolicitudAdminModal.vue";
import PresupuestoAdminModal from "./components/admin/PresupuestoAdminModal.vue";
import TrabajoAdminModal from "./components/admin/TrabajoAdminModal.vue";
import ProfesionalesGrid from "./components/public/ProfesionalesGrid.vue";
import SolicitudUsuarioModal from "./components/usuario/SolicitudUsuarioModal.vue";
import ComentarioModalUser from "./components/usuario/ComentarioModalUser.vue";

const app = createApp({
    methods: {
        openUserModal(id) {
            // accedemos al componente hijo por la ref
            this.$refs.userModal.openModal(id);
        },
        openProfessionalModal(id) {
            this.$refs.professionalModal.openModal(id);
        },
        openSolicitudModal(id, refPro = null) {
            this.$refs.solicitudModal.openModal(id, refPro);
        },
        openTrabajoModal(id, refCliente = null) {
            this.$refs.trabajoModal.openModal(id, refCliente);
        },
        openTrabajoProModal(id, refPro = null) {
            this.$refs.trabajoProModal.openModal(id, refPro);
        },
        openComentarioModalPro(id) {
            this.$refs.ComentarioModalPro.openModal(id);
        },
        openComentarioAdminModal(id) {
            this.$refs.comentarioAdminModal.openModal(id);
        },
        openSolicitudAdminModal(id) {
            //  NUEVO
            this.$refs.solicitudAdminModal.openModal(id);
        },
        openPresupuestoAdminModal(id) {
            this.$refs.presupuestoAdminModal.openModal(id);
        },
        openTrabajoAdminModal(id) {
            if (!this.$refs.trabajoAdminModal) {
                console.error("Ref trabajoAdminModal no encontrado");
                return;
            }
            this.$refs.trabajoAdminModal.openModal(id);
        },
        openSolicitudUsuarioModal(id, numeroSolicitud) {
            // Llama al método openModal del componente referenciado en Blade
            this.$refs.solicitudUsuarioModal.openModal(id, numeroSolicitud);
        },
        openComentarioUserModal(id, refCliente = null) {
            this.$refs.comentarioUserModal.openModal(id, refCliente);
        },
    },
});

app.component("user-modal", UserModal);
app.component("delete-user-button", DeleteUserButton);
app.component("professional-modal", ProfessionalModal);
app.component("delete-professional-button", DeleteProfessionalButton);
app.component("solicitud-modal", SolicitudModal);
app.component("trabajo-modal", TrabajoModal);
app.component("trabajo-pro-modal", TrabajoModalPro);
app.component("comentario-pro-modal", ComentarioModalPro);
app.component("comentario-admin-modal", ComentarioModalAdmin);
app.component("solicitud-admin-modal", SolicitudAdminModal);
app.component("presupuesto-admin-modal", PresupuestoAdminModal);
app.component("trabajo-admin-modal", TrabajoAdminModal);
app.component("profesionales-grid", ProfesionalesGrid);
app.component("solicitud-usuario-modal", SolicitudUsuarioModal);
app.component("comentario-user-modal", ComentarioModalUser);

const el = document.getElementById("app");
if (el) {
    app.mount(el);
}
