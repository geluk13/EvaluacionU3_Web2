<?php
$actual = basename($_SERVER['PHP_SELF']);
?>

<div class="admin-sidebar">
    <h5 class="text-rose fw-bold mb-3">
        <i class="bi bi-speedometer2"></i> Panel Admin
    </h5>

    <a class="<?= $actual === 'dashboard.php' ? 'active' : '' ?>" href="<?= url('admin/dashboard.php') ?>">
        <i class="bi bi-house-heart"></i> Dashboard
    </a>

    <a class="<?= in_array($actual, ['productos.php','producto_form.php','producto_detalle.php']) ? 'active' : '' ?>" href="<?= url('admin/productos.php') ?>">
        <i class="bi bi-flower1"></i> Productos
    </a>

    <a class="<?= in_array($actual, ['categorias.php','categoria_detalle.php']) ? 'active' : '' ?>" href="<?= url('admin/categorias.php') ?>">
        <i class="bi bi-tags"></i> Categorías
    </a>

    <a class="<?= $actual === 'usuarios.php' ? 'active' : '' ?>" href="<?= url('admin/usuarios.php') ?>">
        <i class="bi bi-people"></i> Usuarios
    </a>

    <a class="<?= $actual === 'ventas.php' ? 'active' : '' ?>" href="<?= url('admin/ventas.php') ?>">
        <i class="bi bi-receipt"></i> Ventas
    </a>

    <hr>

    <a href="<?= url('index.php') ?>">
        <i class="bi bi-shop"></i> Ver tienda
    </a>

    <a href="<?= url('auth/logout.php') ?>" class="text-danger">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
</div>