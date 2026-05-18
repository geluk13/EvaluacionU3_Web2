<?php
require_once __DIR__ . '/../config/conexion.php';

$titulo = 'Login | Jarbera Douce';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();

    $usuario = $stmt->get_result()->fetch_assoc();

    if (!$usuario || !password_verify($contrasena, $usuario['contrasena'])) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Correo o contraseña incorrectos.'];
    } elseif ($usuario['estado'] === 'bloqueado') {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Tu cuenta está bloqueada.'];
    } elseif ($usuario['estado'] === 'inactivo') {
        $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'Tu cuenta está inactiva.'];
    } else {
        $codigo = (string)random_int(100000, 999999);
        $estado_2fa = 0;

        $stmt = $conexion->prepare("UPDATE usuarios SET codigo_2fa = ?, estado_2fa = ? WHERE id_usuario = ?");
        $stmt->bind_param("sii", $codigo, $estado_2fa, $usuario['id_usuario']);
        $stmt->execute();

        $_SESSION['pendiente_2fa'] = (int)$usuario['id_usuario'];
        $_SESSION['codigo_demo_2fa'] = $codigo;

        header('Location: ' . url('auth/verificar_2fa.php'));
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="auth-card">
        <h1 class="section-title text-center">Iniciar sesión</h1>

        <form method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="contrasena" class="form-control" required>
            </div>

            <button class="btn btn-rose w-100 rounded-pill py-3">Continuar</button>
        </form>

        <div class="alert alert-light border rounded-4 mt-4 small">
            <strong>Admin:</strong> admin@jarberadouce.com / Admin123#JD<br>
            <strong>Cliente:</strong> cliente@demo.com / Cliente123#JD
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>