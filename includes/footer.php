</main>

<footer id="contacto" class="footer mt-5 pt-5">
    <div class="container pb-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="brand mb-3">
                    <span class="brand-icon">✿</span>
                    <span class="brand-title">Jarbera Douce</span>
                    <small>Flores que duran, recuerdos que quedan</small>
                </div>

                <p class="text-muted">
                    Flores artesanales hechas con limpiapipas, ideales para regalos únicos y personalizados.
                </p>

                <div class="socials">
                    <span><i class="bi bi-instagram"></i></span>
                    <span><i class="bi bi-facebook"></i></span>
                    <span><i class="bi bi-tiktok"></i></span>
                    <span><i class="bi bi-whatsapp"></i></span>
                </div>
            </div>

            <div class="col-lg-2">
                <h6>Enlaces</h6>
                <a href="<?= url('index.php') ?>">Inicio</a>
                <a href="<?= url('index.php#catalogo') ?>">Catálogo</a>
                <a href="<?= url('index.php#categorias') ?>">Categorías</a>
                <a href="<?= url('index.php#contacto') ?>">Contacto</a>
            </div>

            <div class="col-lg-3">
                <h6>Información</h6>
                <a href="#">Envíos</a>
                <a href="#">Métodos de pago</a>
                <a href="#">Cambios y devoluciones</a>
                <a href="#">Términos y condiciones</a>
            </div>

            <div class="col-lg-3">
                <h6>Contáctanos</h6>
                <p><i class="bi bi-whatsapp text-success"></i> +591 71234567</p>
                <p><i class="bi bi-envelope-heart text-rose"></i> hola@jarberadouce.com</p>
                <p><i class="bi bi-geo-alt text-rose"></i> La Paz, Bolivia</p>
            </div>
        </div>
    </div>

    <div class="footer-bottom text-center py-3">
        © <?= date('Y') ?> Jarbera Douce. Todos los derechos reservados.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>