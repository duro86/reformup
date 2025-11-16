@once
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- AVISO AL QUITAR ROL PROFESIONAL ---
        const proCheckbox = document.querySelector('input#role_profesional[data-tiene-perfil-profesional="1"]');
        if (proCheckbox) {
            proCheckbox.addEventListener('change', async function () {
                if (this.checked === false) {
                    let continuar = true;
                    if (window.Swal) {
                        const res = await Swal.fire({
                            icon: 'warning',
                            title: 'Quitar rol profesional',
                            html: `
                                Este usuario tiene un <b>perfil profesional / empresa</b> asociado.<br><br>
                                Quitar el rol <b>no eliminará</b> automáticamente los datos de la empresa,
                                pero el usuario dejará de ser profesional activo.<br><br>
                                <span class="text-danger fw-semibold">¿Seguro que quieres continuar?</span>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Sí, quitar rol',
                            cancelButtonText: 'Cancelar',
                        });
                        continuar = res.isConfirmed;
                    } else {
                        continuar = window.confirm(
                            'Este usuario tiene un perfil profesional asociado.\n' +
                            'Quitar el rol profesional NO elimina los datos de empresa,\n' +
                            'pero el usuario dejará de ser profesional.\n\n' +
                            '¿Seguro que quieres continuar?'
                        );
                    }
                    if (!continuar) {
                        this.checked = true;
                    }
                }
            });
        }

        // --- AVISO AL QUITAR ROL USUARIO ---
        const usuarioCheckbox = document.getElementById('role_usuario');
        if (usuarioCheckbox) {
            usuarioCheckbox.addEventListener('change', async function () {
                if (this.checked === false) {
                    let continuar = true;
                    if (window.Swal) {
                        const res = await Swal.fire({
                            icon: 'warning',
                            title: 'Quitar rol usuario',
                            html: `
                                Estás a punto de quitar el rol <b>usuario</b> a esta cuenta.<br><br>
                                Sin el rol usuario, podría haber limitaciones en cómo accede o usa la plataforma.<br><br>
                                <span class="text-danger fw-semibold">¿Seguro que quieres continuar?</span>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Sí, quitar rol usuario',
                            cancelButtonText: 'Cancelar',
                        });
                        continuar = res.isConfirmed;
                    } else {
                        continuar = window.confirm('Estás a punto de quitar el rol "usuario".\n¿Seguro que quieres continuar?');
                    }
                    if (!continuar) {
                        this.checked = true;
                    }
                }
            });
        }
    });
    </script>
    @endpush
@endonce
