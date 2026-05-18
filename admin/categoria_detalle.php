<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Detalle categoría';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$categoria = $stmt->get_result()->fetch_assoc();

if (!$categoria) {
    $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Categoría no encontrada.'];
    header('Location: ' . url('admin/categorias.php'));
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM productos WHERE id_categoria = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <div class="table-card p-4">
                <h1 class="section-title">Detalle de categoría</h1>

                <p><strong>ID:</strong> <?= $categoria['id_categoria'] ?></p>
                <p><strong>Nombre:</strong> <?= e($categoria['nombre_categoria']) ?></p>
                <p><strong>Descripción:</strong> <?= e($categoria['descripcion']) ?></p>

                <h4 class="mt-4">Productos asociados</h4>

                <?php if (!$productos): ?>
                    <p class="text-muted">No hay productos asociados.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($productos as $p): ?>
                            <li><?= e($p['nombre']) ?> - Bs. <?= number_format($p['precio'], 2) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <a href="<?= url('admin/categorias.php') ?>" class="btn btn-outline-rose rounded-pill">Volver</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>