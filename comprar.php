<?php
require_once __DIR__ . '/config/conexion.php';

requiereCliente();

$titulo = 'Confirmar compra';
$carrito = obtenerCarritoCompleto($conexion);

if (!$carrito['items']) {
    $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'Tu carrito está vacío.'];
    header('Location: ' . url('carrito.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = (int)$_SESSION['usuario']['id_usuario'];
    $metodo_pago = $_POST['metodo_pago'] ?? 'QR';

    try {
        $conexion->begin_transaction();

        $total = 0;
        $items = [];

        foreach (obtenerCarritoUsuario($conexion) as $id_producto => $cantidad) {
            $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ? AND estado = 1 FOR UPDATE");
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();

            $producto = $stmt->get_result()->fetch_assoc();

            if (!$producto || $producto['stock'] < $cantidad) {
                throw new Exception('Stock insuficiente.');
            }

            $subtotal = $producto['precio'] * $cantidad;
            $total += $subtotal;
            $items[] = [$id_producto, $cantidad, $subtotal];
        }

        $estado = 'pagada';

        $stmt = $conexion->prepare("INSERT INTO ventas (id_usuario, total, estado_venta, metodo_pago) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $id_usuario, $total, $estado, $metodo_pago);
        $stmt->execute();

        $id_venta = $conexion->insert_id;

        foreach ($items as [$id_producto, $cantidad, $subtotal]) {
            $stmt = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $id_venta, $id_producto, $cantidad, $subtotal);
            $stmt->execute();

            $stmt = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
            $stmt->bind_param("ii", $cantidad, $id_producto);
            $stmt->execute();
        }

        $id_carrito = obtenerOCrearCarritoActivo($conexion, $id_usuario);
        $estadoCarrito = 'comprado';

        $stmt = $conexion->prepare("UPDATE carritos SET estado = ? WHERE id_carrito = ?");
        $stmt->bind_param("si", $estadoCarrito, $id_carrito);
        $stmt->execute();

        $conexion->commit();

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Compra registrada correctamente.'];
        header('Location: ' . url('cliente/historial.php'));
        exit;
    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Error: ' . $e->getMessage()];
        header('Location: ' . url('carrito.php'));
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="table-card p-4">
                <h1 class="section-title mb-4">Confirmar compra</h1>

                <?php foreach ($carrito['items'] as $item): ?>
                    <div class="d-flex justify-content-between border-bottom py-3">
                        <span><?= e($item['nombre']) ?> x<?= $item['cantidad'] ?></span>
                        <strong>Bs. <?= number_format($item['subtotal'], 2) ?></strong>
                    </div>
                <?php endforeach; ?>

                <h3 class="text-end mt-4">Total: <span class="text-rose">Bs. <?= number_format($carrito['total'], 2) ?></span></h3>
            </div>
        </div>

        <div class="col-lg-5">
            <form method="post" class="auth-card m-0">
                <h3>Método de pago</h3>

                <select name="metodo_pago" class="form-select my-3">
                    <option value="QR">QR</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Tigo Money">Tigo Money</option>
                    <option value="Efectivo">Efectivo</option>
                </select>

                <button class="btn btn-rose w-100 rounded-pill py-3">Pagar y registrar venta</button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>