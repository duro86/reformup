import "./bootstrap";
import "../scss/app.scss";
import { createApp } from "vue";

import UserModal from "./components/UserModal.vue";
import DeleteUserButton from "./components/DeleteUserButton.vue";
import DeleteProfessionalButton from "./components/DeleteProfessionalButton.vue";
import ProfessionalModal from "./components/ProfessionalModal.vue";
import SolicitudModal from "./components/SolicitudModal.vue";
import TrabajoModal from "./components/TrabajoModalUser.vue";
import TrabajoModalPro from "./components/TrabajoModalPro.vue";
import ComentarioModalPro from "./components/ComentarioModalPro.vue";
import ComentarioModalAdmin from "./components/ComentarioModalAdmin.vue";
import SolicitudAdminModal from "./components/SolicitudAdminModal.vue"; 



const app = createApp({
    methods: {
        openUserModal(id) {
            // accedemos al componente hijo por la ref
            this.$refs.userModal.openModal(id);
        },
        openProfessionalModal(id) {
            this.$refs.professionalModal.openModal(id);
        },
        openSolicitudModal(id) {
            this.$refs.solicitudModal.openModal(id);
        },
        openTrabajoModal(id) {
            this.$refs.trabajoModal.openModal(id);
        },
        openTrabajoProModal(id) { // profesional
            this.$refs.trabajoProModal.openModal(id);
        },
        openComentarioModalPro(id) {
            this.$refs.ComentarioModalPro.openModal(id);
        },
        openComentarioAdminModal(id) {
            this.$refs.comentarioAdminModal.openModal(id);
        },
        openSolicitudAdminModal(id) {               // 👈 NUEVO
            this.$refs.solicitudAdminModal.openModal(id);
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


const el = document.getElementById("app");
if (el) {
    app.mount(el);
}
