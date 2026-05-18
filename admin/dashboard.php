<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Dashboard';

$totalProductos = $conexion->query("SELECT COUNT(*) total FROM productos")->fetch_assoc()['total'];
$totalCategorias = $conexion->query("SELECT COUNT(*) total FROM categorias")->fetch_assoc()['total'];
$totalUsuarios = $conexion->query("SELECT COUNT(*) total FROM usuarios")->fetch_assoc()['total'];
$totalVentas = $conexion->query("SELECT COUNT(*) total FROM ventas")->fetch_assoc()['total'];
$montoVentas = $conexion->query("SELECT IFNULL(SUM(total),0) total FROM ventas")->fetch_assoc()['total'];

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <h1 class="section-title mb-4">Dashboard</h1>

            <div class="row g-3">
                <div class="col-md-3"><div class="stat"><h3><?= $totalProductos ?></h3><p>Productos</p></div></div>
                <div class="col-md-3"><div class="stat"><h3><?= $totalCategorias ?></h3><p>Categorías</p></div></div>
                <div class="col-md-3"><div class="stat"><h3><?= $totalUsuarios ?></h3><p>Usuarios</p></div></div>
                <div class="col-md-3"><div class="stat"><h3><?= $totalVentas ?></h3><p>Ventas</p></div></div>
            </div>

            <div class="soft-card p-4 mt-4">
                <h4>Ingresos totales</h4>
                <h2 class="text-rose">Bs. <?= number_format($montoVentas, 2) ?></h2>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>