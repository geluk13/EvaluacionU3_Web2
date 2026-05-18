<?php
require_once __DIR__ . '/../config/conexion.php';

requiereCliente();

$titulo = 'Historial de compras';
$id_usuario = (int)$_SESSION['usuario']['id_usuario'];

$stmt = $conexion->prepare("SELECT * FROM ventas WHERE id_usuario = ? ORDER BY fecha DESC");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$ventas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <h1 class="section-title mb-4">Historial de compras</h1>

    <?php if (!$ventas): ?>
        <div class="empty-state text-center">
            <h4>No tienes compras registradas</h4>
            <a href="<?= url('index.php#catalogo') ?>" class="btn btn-rose rounded-pill">Comprar</a>
        </div>
    <?php else: ?>
        <div class="accordion" id="historial">
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
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#venta<?= $venta['id_venta'] ?>">
                            Compra #<?= $venta['id_venta'] ?> - Bs. <?= number_format($venta['total'], 2) ?>
                        </button>
                    </h2>

                    <div id="venta<?= $venta['id_venta'] ?>" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <p><strong>Estado:</strong> <?= e($venta['estado_venta']) ?></p>
                            <p><strong>Método:</strong> <?= e($venta['metodo_pago']) ?></p>

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
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>