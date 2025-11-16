import "./bootstrap";
import "../scss/app.scss";
import { createApp } from "vue";

import UserModal from "./components/UserModal.vue";
import DeleteUserButton from "./components/DeleteUserButton.vue";
import DeleteProfessionalButton from "./components/DeleteProfessionalButton.vue";
import ProfessionalModal from "./components/ProfessionalModal.vue";


const app = createApp({
    methods: {
        openUserModal(id) {
            // accedemos al componente hijo por la ref
            this.$refs.userModal.openModal(id);
        },
        openProfessionalModal(id) {
            this.$refs.professionalModal.openModal(id);
        },
    },
});

app.component("user-modal", UserModal);
app.component("delete-user-button", DeleteUserButton);
app.component("professional-modal", ProfessionalModal);
app.component("delete-professional-button", DeleteProfessionalButton);

const el = document.getElementById("app");
if (el) {
    app.mount(el);
}
