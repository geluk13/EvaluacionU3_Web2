<?php
require_once __DIR__ . '/config/conexion.php';

requiereCliente();

$id_usuario = (int)$_SESSION['usuario']['id_usuario'];
$id_producto = (int)($_POST['id_producto'] ?? 0);
$accion = $_POST['accion'] ?? 'toggle';

$redir = $_SERVER['HTTP_REFERER'] ?? url('index.php');

if ($id_producto > 0) {
    if ($accion === 'eliminar') {
        $stmt = $conexion->prepare("DELETE FROM favoritos WHERE id_usuario = ? AND id_producto = ?");
        $stmt->bind_param("ii", $id_usuario, $id_producto);
        $stmt->execute();

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Favorito eliminado.'];
    } else {
        $stmt = $conexion->prepare("SELECT id_favorito FROM favoritos WHERE id_usuario = ? AND id_producto = ?");
        $stmt->bind_param("ii", $id_usuario, $id_producto);
        $stmt->execute();

        if ($stmt->get_result()->fetch_assoc()) {
            $stmt = $conexion->prepare("DELETE FROM favoritos WHERE id_usuario = ? AND id_producto = ?");
            $stmt->bind_param("ii", $id_usuario, $id_producto);
            $stmt->execute();

            $_SESSION['flash'] = ['tipo' => 'info', 'mensaje' => 'Producto quitado de favoritos.'];
        } else {
            $stmt = $conexion->prepare("INSERT INTO favoritos (id_usuario, id_producto) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_usuario, $id_producto);
            $stmt->execute();

            $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto agregado a favoritos.'];
        }
    }
}

header('Location: ' . $redir);
exit;