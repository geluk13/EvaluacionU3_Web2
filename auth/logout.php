<?php
require_once __DIR__ . '/../config/conexion.php';

session_destroy();
session_start();

$_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Sesión cerrada correctamente.'];

header('Location: ' . url('index.php'));
exit;