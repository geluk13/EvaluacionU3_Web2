<?php
require_once __DIR__ . '/config/conexion.php';

requiereCliente();

$accion = $_POST['accion'] ?? '';
$id_producto = (int)($_POST['id_producto'] ?? 0);
$redir = $_SERVER['HTTP_REFERER'] ?? url('index.php');

$carrito = obtenerCarritoUsuario($conexion);

if ($accion === 'agregar' && $id_producto > 0) {
    $stmt = $conexion->prepare("SELECT stock FROM productos WHERE id_producto = ? AND estado = 1");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();

    $producto = $stmt->get_result()->fetch_assoc();

    if ($producto && $producto['stock'] > 0) {
        $actual = $carrito[$id_producto] ?? 0;
        $carrito[$id_producto] = min($actual + 1, (int)$producto['stock']);

        guardarCarritoBD($conexion, $carrito);

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto agregado al carrito.'];
    } else {
        $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'Producto sin stock.'];
    }
}

if ($accion === 'actualizar') {
    $nuevo = [];

    foreach ($_POST['cantidades'] ?? [] as $id => $cantidad) {
        $id = (int)$id;
        $cantidad = max(1, (int)$cantidad);

        $stmt = $conexion->prepare("SELECT stock FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $producto = $stmt->get_result()->fetch_assoc();

        if ($producto) {
            $nuevo[$id] = min($cantidad, (int)$producto['stock']);
        }
    }

    guardarCarritoBD($conexion, $nuevo);
    $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Carrito actualizado.'];
    $redir = url('carrito.php');
}

if ($accion === 'eliminar' && $id_producto > 0) {
    unset($carrito[$id_producto]);
    guardarCarritoBD($conexion, $carrito);

    $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto eliminado.'];
    $redir = url('carrito.php');
}

if ($accion === 'vaciar') {
    guardarCarritoBD($conexion, []);

    $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Carrito vaciado.'];
    $redir = url('carrito.php');
}

header('Location: ' . $redir);
exit;