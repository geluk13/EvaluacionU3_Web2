<?php
require_once __DIR__ . '/../config/conexion.php';

$titulo = 'Verificación 2FA';

if (!isset($_SESSION['pendiente_2fa'])) {
    header('Location: ' . url('auth/login.php'));
    exit;
}

$id_usuario = (int)$_SESSION['pendiente_2fa'];

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    session_destroy();
    header('Location: ' . url('auth/login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');

    if ($codigo === $usuario['codigo_2fa']) {
        $estado_2fa = 1;
        $nulo = null;

        $stmt = $conexion->prepare("UPDATE usuarios SET estado_2fa = ?, codigo_2fa = ? WHERE id_usuario = ?");
        $stmt->bind_param("isi", $estado_2fa, $nulo, $id_usuario);
        $stmt->execute();

        $_SESSION['usuario'] = [
            'id_usuario' => (int)$usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol']
        ];

        unset($_SESSION['pendiente_2fa'], $_SESSION['codigo_demo_2fa']);

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Bienvenido/a ' . $usuario['nombre']];

        if ($usuario['rol'] === 'admin') {
            header('Location: ' . url('admin/dashboard.php'));
        } else {
            header('Location: ' . url('index.php'));
        }

        exit;
    } else {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Código incorrecto.'];
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="auth-card text-center">
        <h1 class="section-title">Verificación 2FA</h1>
        <p class="text-muted">Ingresa el código temporal.</p>

        <div class="alert alert-warning rounded-4">
            <strong>Código demo:</strong> <?= e($_SESSION['codigo_demo_2fa']) ?>
        </div>

        <form method="post">
            <input name="codigo" maxlength="6" class="form-control text-center fs-3 fw-bold mb-3" required>
            <button class="btn btn-rose w-100 rounded-pill py-3">Verificar</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>