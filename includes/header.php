<?php
require_once __DIR__ . '/../config/conexion.php';
$flash = flash();
$pagina = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= e($titulo ?? 'Jarbera Douce') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600;700&family=Sacramento&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>">
</head>
<body>

<div class="top-strip text-center py-2">
    🌸 Envíos a toda Bolivia | Flores eternas hechas con limpiapipas
</div>

<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top py-3">
    <div class="container">
        <a href="<?= url('index.php') ?>" class="navbar-brand brand">
            <span class="brand-icon">✿</span>
            <span class="brand-title">Jarbera Douce</span>
            <small>Flores que duran, recuerdos que quedan</small>
        </a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="menu" class="collapse navbar-collapse">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('index.php') ?>">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('index.php#catalogo') ?>">Catálogo</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('index.php#categorias') ?>">Categorías</a></li>

                <?php if (!esAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('favoritos.php') ?>">Favoritos</a></li>
                <?php endif; ?>

                <li class="nav-item"><a class="nav-link" href="<?= url('index.php#contacto') ?>">Contacto</a></li>
            </ul>

            <div class="d-flex gap-2 flex-wrap">
                <?php if (esCliente()): ?>
                    <a class="btn btn-light rounded-pill position-relative" href="<?= url('carrito.php') ?>">
                        <i class="bi bi-bag-heart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-rose">
                            <?= contarCarrito($conexion) ?>
                        </span>
                    </a>
                <?php endif; ?>

                <?php if (estaLogueado()): ?>
                    <?php if (esAdmin()): ?>
                        <a class="btn btn-outline-rose rounded-pill" href="<?= url('admin/dashboard.php') ?>">Panel admin</a>
                    <?php else: ?>
                        <a class="btn btn-outline-rose rounded-pill" href="<?= url('cliente/historial.php') ?>">Mis compras</a>
                    <?php endif; ?>

                    <a class="btn btn-rose rounded-pill" href="<?= url('auth/logout.php') ?>">Salir</a>
                <?php else: ?>
                    <a class="btn btn-outline-rose rounded-pill" href="<?= url('auth/login.php') ?>">Iniciar sesión</a>
                    <a class="btn btn-rose rounded-pill" href="<?= url('auth/register.php') ?>">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if ($flash): ?>
<div class="container mt-3">
    <div class="alert alert-<?= e($flash['tipo']) ?> alert-dismissible fade show rounded-4 shadow-sm">
        <?= e($flash['mensaje']) ?>
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<main>