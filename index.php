<?php
require_once __DIR__ . '/config/conexion.php';

$titulo = 'Jarbera Douce | Flores de limpiapipas';

$buscar = trim($_GET['buscar'] ?? '');
$id_categoria = (int)($_GET['categoria'] ?? 0);

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre_categoria")->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT p.*, c.nombre_categoria 
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.estado = 1";

$params = [];
$tipos = '';

if ($buscar !== '') {
    $sql .= " AND (p.nombre LIKE ? OR p.marca LIKE ? OR c.nombre_categoria LIKE ?)";
    $like = "%$buscar%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $tipos .= 'sss';
}

if ($id_categoria > 0) {
    $sql .= " AND p.id_categoria = ?";
    $params[] = $id_categoria;
    $tipos .= 'i';
}

$sql .= " ORDER BY p.destacado DESC, p.nombre ASC";

$stmt = $conexion->prepare($sql);

if ($params) {
    $stmt->bind_param($tipos, ...$params);
}

$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div id="sliderPrincipal" class="carousel slide hero-card p-4 p-lg-5" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <span class="badge rounded-pill bg-light text-rose mb-3">Hechas a mano</span>
                        <h1 class="hero-title">Flores eternas hechas con <span>limpiapipas</span></h1>
                        <p class="lead text-muted">
                            Regalos únicos, artesanales y llenos de color que nunca se marchitan.
                        </p>
                        <a href="#catalogo" class="btn btn-rose btn-lg rounded-pill px-4">
                            Comprar ahora
                        </a>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="<?= url('assets/img/default.svg') ?>" class="hero-img" alt="Flores de limpiapipas">
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <span class="badge rounded-pill bg-light text-rose mb-3">Personalizados</span>
                        <h1 class="hero-title">Diseños para fechas <span>especiales</span></h1>
                        <p class="lead text-muted">
                            Ramos, rosas, tulipanes, girasoles y cajas sorpresa.
                        </p>
                        <a href="#contacto" class="btn btn-outline-rose btn-lg rounded-pill px-4">
                            Contactarnos
                        </a>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="<?= url('assets/img/default.svg') ?>" class="hero-img" alt="Regalos personalizados">
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#sliderPrincipal" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#sliderPrincipal" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<section id="catalogo" class="container py-4">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h2 class="section-title">Catálogo de flores</h2>
            <p class="text-muted">Busca por nombre, marca o categoría.</p>
        </div>

        <form method="get" class="d-flex flex-wrap gap-2">
            <input type="search" name="buscar" class="form-control" placeholder="Buscar..." value="<?= e($buscar) ?>">

            <select name="categoria" class="form-select">
                <option value="0">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= $id_categoria == $cat['id_categoria'] ? 'selected' : '' ?>>
                        <?= e($cat['nombre_categoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-rose rounded-pill px-4">Filtrar</button>
        </form>
    </div>

    <div class="row g-4">
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="product-card h-100 position-relative">
                    <?php if (esCliente()): ?>
                        <form method="post" action="<?= url('favorito_accion.php') ?>">
                            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                            <button class="heart-btn" title="Favorito">
                                <i class="bi bi-heart"></i>
                            </button>
                        </form>
                    <?php endif; ?>

                    <img src="<?= url($producto['imagen']) ?>" class="product-img" alt="<?= e($producto['nombre']) ?>">

                    <div class="product-body">
                        <span class="badge text-bg-light rounded-pill mb-2"><?= e($producto['nombre_categoria']) ?></span>
                        <h5 class="product-title"><?= e($producto['nombre']) ?></h5>
                        <p class="text-muted small"><?= e($producto['descripcion']) ?></p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">Bs. <?= number_format($producto['precio'], 2) ?></span>

                            <?php if (esCliente()): ?>
                                <form method="post" action="<?= url('carrito_accion.php') ?>">
                                    <input type="hidden" name="accion" value="agregar">
                                    <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                    <button class="icon-btn"><i class="bi bi-plus-lg"></i></button>
                                </form>
                            <?php elseif (!estaLogueado()): ?>
                                <a href="<?= url('auth/login.php') ?>" class="btn btn-sm btn-outline-rose rounded-pill">Comprar</a>
                            <?php endif; ?>
                        </div>

                        <small class="text-muted">Stock: <?= (int)$producto['stock'] ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!$productos): ?>
            <div class="col-12">
                <div class="empty-state text-center">
                    <h4>No se encontraron productos</h4>
                    <a href="<?= url('index.php#catalogo') ?>" class="btn btn-rose rounded-pill">Limpiar filtros</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="categorias" class="container py-5">
    <h2 class="section-title mb-4">Categorías</h2>

    <div class="row g-3">
        <?php foreach ($categorias as $cat): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a class="category-card text-center" href="<?= url('index.php?categoria=' . $cat['id_categoria'] . '#catalogo') ?>">
                    <?= e($cat['nombre_categoria']) ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container py-5">
    <div class="benefits p-4 p-lg-5">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="benefit-icon mb-3"><i class="bi bi-flower1"></i></div>
                <h5>Hechas a mano</h5>
                <p class="text-muted">Cada flor está elaborada artesanalmente.</p>
            </div>

            <div class="col-md-3">
                <div class="benefit-icon mb-3"><i class="bi bi-pencil-heart"></i></div>
                <h5>Personalizadas</h5>
                <p class="text-muted">Diseños por color, ocasión o mensaje.</p>
            </div>

            <div class="col-md-3">
                <div class="benefit-icon mb-3"><i class="bi bi-truck"></i></div>
                <h5>Delivery</h5>
                <p class="text-muted">Envíos coordinados en Bolivia.</p>
            </div>

            <div class="col-md-3">
                <div class="benefit-icon mb-3"><i class="bi bi-gift"></i></div>
                <h5>Regalos únicos</h5>
                <p class="text-muted">Detalles que no se marchitan.</p>
            </div>
        </div>
    </div>
</section>

<section id="contacto" class="container py-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <h2 class="section-title">Contáctanos</h2>
            <p class="text-muted">Escríbenos para pedidos personalizados.</p>

            <div class="soft-card p-4">
                <p><i class="bi bi-whatsapp text-success"></i> WhatsApp: +591 71234567</p>
                <p><i class="bi bi-envelope text-rose"></i> Correo: hola@jarberadouce.com</p>
                <p><i class="bi bi-geo-alt text-rose"></i> La Paz, Bolivia</p>
            </div>
        </div>

        <div class="col-lg-6">
            <form class="soft-card p-4 needs-validation" novalidate onsubmit="return false;">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mensaje</label>
                    <textarea class="form-control" rows="4" required></textarea>
                </div>

                <button class="btn btn-rose rounded-pill px-4">Enviar mensaje</button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>