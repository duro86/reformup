import "./bootstrap";
import "../scss/app.scss";
import { createApp } from "vue";

import UserModal from "./components/UserModal.vue";
import DeleteUserButton from "./components/DeleteUserButton.vue";

const app = createApp({
    methods: {
        openUserModal(id) {
            // accedemos al componente hijo por la ref
            this.$refs.userModal.openModal(id);
        },
    },
});

app.component("user-modal", UserModal);
app.component("delete-user-button", DeleteUserButton);

const el = document.getElementById('app');
if (el) {
  app.mount(el);
}
