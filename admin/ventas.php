<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Ventas';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_venta = (int)($_POST['id_venta'] ?? 0);
    $estado = $_POST['estado_venta'] ?? 'pagada';

    if (in_array($estado, ['pendiente','pagada','entregada','cancelada'])) {
        $stmt = $conexion->prepare("UPDATE ventas SET estado_venta = ? WHERE id_venta = ?");
        $stmt->bind_param("si", $estado, $id_venta);
        $stmt->execute();

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Estado actualizado.'];
    }

    header('Location: ' . url('admin/ventas.php'));
    exit;
}

$ventas = $conexion->query("
    SELECT v.*, u.nombre, u.correo
    FROM ventas v
    INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
    ORDER BY v.fecha DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <h1 class="section-title mb-4">Ventas</h1>

            <div class="accordion" id="ventas">
                <?php foreach ($ventas as $venta): ?>
                    <?php
                    $stmt = $conexion->prepare("
                        SELECT d.*, p.nombre
                        FROM detalle_ventas d
                        INNER JOIN productos p ON d.id_producto = p.id_producto
                        WHERE d.id_venta = ?
                    ");

                    $stmt->bind_param("i", $venta['id_venta']);
                    $stmt->execute();

                    $detalles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    ?>

                    <div class="accordion-item mb-3 rounded-4 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ventaAdmin<?= $venta['id_venta'] ?>">
                                Venta #<?= $venta['id_venta'] ?> - <?= e($venta['nombre']) ?> - Bs. <?= number_format($venta['total'], 2) ?>
                            </button>
                        </h2>

                        <div id="ventaAdmin<?= $venta['id_venta'] ?>" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <p><strong>Cliente:</strong> <?= e($venta['nombre']) ?></p>
                                <p><strong>Correo:</strong> <?= e($venta['correo']) ?></p>
                                <p><strong>Método:</strong> <?= e($venta['metodo_pago']) ?></p>

                                <form method="post" class="d-flex gap-2 mb-3">
                                    <input type="hidden" name="id_venta" value="<?= $venta['id_venta'] ?>">

                                    <select name="estado_venta" class="form-select">
                                        <?php foreach (['pendiente','pagada','entregada','cancelada'] as $estado): ?>
                                            <option value="<?= $estado ?>" <?= $venta['estado_venta'] === $estado ? 'selected' : '' ?>>
                                                <?= ucfirst($estado) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button class="btn btn-rose rounded-pill">Guardar</button>
                                </form>

                                <?php foreach ($detalles as $d): ?>
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span><?= e($d['nombre']) ?> x<?= $d['cantidad'] ?></span>
                                        <strong>Bs. <?= number_format($d['subtotal'], 2) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$ventas): ?>
                    <div class="empty-state text-center">No hay ventas registradas.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>