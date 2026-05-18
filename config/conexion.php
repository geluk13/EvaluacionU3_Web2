<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_URL', '/jarberaDouce');
define('ROOT_PATH', dirname(__DIR__));

$host = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'jarbera_douce';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $usuario, $password, $base_datos);
    $conexion->set_charset('utf8mb4');
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function url($ruta = '') {
    return APP_URL . '/' . ltrim($ruta, '/');
}

function estaLogueado() {
    return isset($_SESSION['usuario']);
}

function esAdmin() {
    return estaLogueado() && $_SESSION['usuario']['rol'] === 'admin';
}

function esCliente() {
    return estaLogueado() && $_SESSION['usuario']['rol'] === 'cliente';
}

function requiereLogin() {
    if (!estaLogueado()) {
        $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'Debes iniciar sesión.'];
        header('Location: ' . url('auth/login.php'));
        exit;
    }
}

function requiereAdmin() {
    requiereLogin();

    if (!esAdmin()) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'No tienes permiso para acceder al panel administrativo.'];
        header('Location: ' . url('index.php'));
        exit;
    }
}

function requiereCliente() {
    requiereLogin();

    if (!esCliente()) {
        $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'El administrador no puede usar funciones de cliente.'];
        header('Location: ' . url('admin/dashboard.php'));
        exit;
    }
}

function flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    return null;
}

function passwordSegura($password) {
    return strlen($password) >= 10
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password)
        && preg_match('/[^A-Za-z0-9]/', $password);
}

function obtenerOCrearCarritoActivo($conexion, $id_usuario) {
    $estado = 'activo';

    $stmt = $conexion->prepare("SELECT id_carrito FROM carritos WHERE id_usuario = ? AND estado = ? LIMIT 1");
    $stmt->bind_param("is", $id_usuario, $estado);
    $stmt->execute();
    $carrito = $stmt->get_result()->fetch_assoc();

    if ($carrito) {
        return (int)$carrito['id_carrito'];
    }

    $stmt = $conexion->prepare("INSERT INTO carritos (id_usuario, estado) VALUES (?, ?)");
    $stmt->bind_param("is", $id_usuario, $estado);
    $stmt->execute();

    return (int)$conexion->insert_id;
}

function obtenerCarritoUsuario($conexion) {
    if (!esCliente()) {
        return [];
    }

    $id_usuario = (int)$_SESSION['usuario']['id_usuario'];
    $id_carrito = obtenerOCrearCarritoActivo($conexion, $id_usuario);

    $stmt = $conexion->prepare("SELECT id_producto, cantidad FROM detalle_carrito WHERE id_carrito = ?");
    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $carrito = [];

    while ($row = $resultado->fetch_assoc()) {
        $carrito[(int)$row['id_producto']] = (int)$row['cantidad'];
    }

    return $carrito;
}

function guardarCarritoBD($conexion, $carrito) {
    if (!esCliente()) {
        return;
    }

    $id_usuario = (int)$_SESSION['usuario']['id_usuario'];
    $id_carrito = obtenerOCrearCarritoActivo($conexion, $id_usuario);

    $stmt = $conexion->prepare("DELETE FROM detalle_carrito WHERE id_carrito = ?");
    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();

    foreach ($carrito as $id_producto => $cantidad) {
        $id_producto = (int)$id_producto;
        $cantidad = (int)$cantidad;

        if ($cantidad > 0) {
            $stmt = $conexion->prepare("INSERT INTO detalle_carrito (id_carrito, id_producto, cantidad) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $id_carrito, $id_producto, $cantidad);
            $stmt->execute();
        }
    }
}

function obtenerCarritoCompleto($conexion) {
    $carrito = obtenerCarritoUsuario($conexion);
    $items = [];
    $total = 0;

    foreach ($carrito as $id_producto => $cantidad) {
        $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ? AND estado = 1");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $producto = $stmt->get_result()->fetch_assoc();

        if ($producto) {
            $cantidad = min((int)$cantidad, (int)$producto['stock']);
            $subtotal = $cantidad * (float)$producto['precio'];

            $producto['cantidad'] = $cantidad;
            $producto['subtotal'] = $subtotal;

            $items[] = $producto;
            $total += $subtotal;
        }
    }

    return ['items' => $items, 'total' => $total];
}

function contarCarrito($conexion) {
    if (!esCliente()) {
        return 0;
    }

    $carrito = obtenerCarritoUsuario($conexion);
    return array_sum($carrito);
}
?>