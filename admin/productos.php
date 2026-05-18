<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Productos';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id_producto = (int)($_POST['id_producto'] ?? 0);

    if ($accion === 'eliminar') {
        try {
            $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();

            $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto eliminado.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'No se puede eliminar porque tiene ventas asociadas. Puedes desactivarlo.'];
        }

        header('Location: ' . url('admin/productos.php'));
        exit;
    }

    if ($accion === 'estado') {
        $estado = (int)($_POST['estado'] ?? 0);

        $stmt = $conexion->prepare("UPDATE productos SET estado = ? WHERE id_producto = ?");
        $stmt->bind_param("ii", $estado, $id_producto);
        $stmt->execute();

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Estado actualizado.'];
        header('Location: ' . url('admin/productos.php'));
        exit;
    }
}

$productos = $conexion->query("
    SELECT p.*, c.nombre_categoria
    FROM productos p
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    ORDER BY p.id_producto DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between mb-4">
                <h1 class="section-title">CRUD de productos</h1>
                <a href="<?= url('admin/producto_form.php') ?>" class="btn btn-rose rounded-pill">Nuevo producto</a>
            </div>

            <div class="table-card p-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($productos as $p): ?>
                                <tr>
                                    <td><img src="<?= url($p['imagen']) ?>" width="70" class="rounded-4"></td>
                                    <td><?= e($p['nombre']) ?></td>
                                    <td><?= e($p['nombre_categoria']) ?></td>
                                    <td>Bs. <?= number_format($p['precio'], 2) ?></td>
                                    <td><?= $p['stock'] ?></td>
                                    <td>
                                        <?= $p['estado'] ? '<span class="badge text-bg-success">Activo</span>' : '<span class="badge text-bg-secondary">Inactivo</span>' ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-light rounded-pill" href="<?= url('admin/producto_detalle.php?id=' . $p['id_producto']) ?>">Detalle</a>
                                        <a class="btn btn-sm btn-outline-rose rounded-pill" href="<?= url('admin/producto_form.php?id=' . $p['id_producto']) ?>">Editar</a>

                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="accion" value="estado">
                                            <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                            <input type="hidden" name="estado" value="<?= $p['estado'] ? 0 : 1 ?>">
                                            <button class="btn btn-sm btn-warning rounded-pill">
                                                <?= $p['estado'] ? 'Desactivar' : 'Activar' ?>
                                            </button>
                                        </form>

                                        <form method="post" class="d-inline" data-confirm="¿Eliminar producto?">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                            <button class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>