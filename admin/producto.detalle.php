<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Detalle producto';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("
    SELECT p.*, c.nombre_categoria
    FROM productos p
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.id_producto = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$producto = $stmt->get_result()->fetch_assoc();

if (!$producto) {
    $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Producto no encontrado.'];
    header('Location: ' . url('admin/productos.php'));
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <div class="table-card p-4">
                <h1 class="section-title">Detalle del producto</h1>

                <div class="row g-4">
                    <div class="col-md-4">
                        <img src="<?= url($producto['imagen']) ?>" class="w-100 rounded-4">
                    </div>

                    <div class="col-md-8">
                        <p><strong>ID:</strong> <?= $producto['id_producto'] ?></p>
                        <p><strong>Nombre:</strong> <?= e($producto['nombre']) ?></p>
                        <p><strong>Categoría:</strong> <?= e($producto['nombre_categoria']) ?></p>
                        <p><strong>Marca:</strong> <?= e($producto['marca']) ?></p>
                        <p><strong>Precio:</strong> Bs. <?= number_format($producto['precio'], 2) ?></p>
                        <p><strong>Stock:</strong> <?= $producto['stock'] ?></p>
                        <p><strong>Estado:</strong> <?= $producto['estado'] ? 'Activo' : 'Inactivo' ?></p>
                        <p><strong>Descripción:</strong> <?= e($producto['descripcion']) ?></p>

                        <a href="<?= url('admin/productos.php') ?>" class="btn btn-outline-rose rounded-pill">Volver</a>
                        <a href="<?= url('admin/producto_form.php?id=' . $producto['id_producto']) ?>" class="btn btn-rose rounded-pill">Editar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>