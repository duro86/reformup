<footer class="text-light py-4 mt-5 border-0 footer-bg">
    <div class="container">
        <div class="row text-center text-md-start">
            <!-- Logo y texto -->
            <div class="col-12 col-md-4 mb-4 mb-md-0 d-flex flex-column align-items-center text-center">
                <img src="{{ asset('img/footer/reformupFooter.svg') }}" alt="Logo" class="mb-2"
                    style="width: 120px; height:auto;">

                <p class="mb-0" style="color: #bec2cf; line-height: 1.4;">
                    Conectamos a profesionales y clientes<br>
                    con tecnología, confianza y agilidad.
                </p>
            </div>
            <!-- Enlaces rápidos -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h6 class="fw-bold mb-2">ENLACES RÁPIDOS</h6>
                <ul class="list-unstyled">

                    <li><a href="{{ route('public.profesionales.index') }}" class="footer-link">Búsqueda de
                            profesionales</a></li>
                    <li><a href="{{ route('registrar.profesional.opciones') }}"class="footer-link">Registro de
                            Profesionales</a></li>
                </ul>
            </div>
            <!-- Legal -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h6 class="fw-bold mb-2">LEGAL</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('privacidad') }}" class="footer-link">Política de privacidad</a></li>
                    <li><a href="{{ route('public.contacto') }}" class="footer-link">Contacto</a></li>
                </ul>
            </div>
            <!-- Redes sociales -->
            <div class="col-12 col-md-2 d-flex flex-column align-items-center align-items-md-start">
                <h6 class="fw-bold mb-2 justify-content-center">SÍGANOS</h6>
                <div>
                    <a href="https://www.facebook.com/?locale=es_ES" target="_blank" class="me-4"><i
                            class="bi bi-facebook fs-3 text-white"></i></a>
                    <a href="https://www.instagram.com/?hl=es" class="me-4">
                        <i class="bi bi-instagram fs-3 text-white"></i>
                    </a>
                    <a href="https://es.linkedin.com/">
                        <i class="bi bi-linkedin fs-3 text-white"></i>
                    </a>
                </div>
            </div>
        </div>
        <hr class="footer-line">
        <div class="text-center" style="color: #bec2cf;">
            © {{ date('Y') }} ReformUp. Todos los derechos reservados.
        </div>
    </div>
</footer>
