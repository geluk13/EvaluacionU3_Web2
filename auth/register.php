<?php
require_once __DIR__ . '/../config/conexion.php';

$titulo = 'Registro | Jarbera Douce';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($nombre === '' || $correo === '' || $contrasena === '' || $confirmar === '') {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Completa todos los campos.'];
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Correo inválido.'];
    } elseif (!passwordSegura($contrasena)) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'La contraseña debe tener mínimo 10 caracteres, mayúscula, minúscula, número y símbolo.'];
    } elseif ($contrasena !== $confirmar) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Las contraseñas no coinciden.'];
    } else {
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'Ese correo ya está registrado.'];
        } else {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $rol = 'cliente';
            $estado = 'activo';

            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, estado) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nombre, $correo, $hash, $rol, $estado);
            $stmt->execute();

            $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Cuenta creada correctamente.'];
            header('Location: ' . url('auth/login.php'));
            exit;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="auth-card">
        <h1 class="section-title text-center">Crear cuenta</h1>
        <p class="text-muted text-center">Regístrate como cliente.</p>

        <form method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Nombre completo</label>
                <input name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña segurísima</label>
                <input type="password" name="contrasena" class="form-control" required>
                <small class="text-muted">Mínimo 10 caracteres, mayúscula, minúscula, número y símbolo.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" name="confirmar" class="form-control" required>
            </div>

            <button class="btn btn-rose w-100 rounded-pill py-3">Registrarme</button>
        </form>

        <p class="text-center mt-3">
            ¿Ya tienes cuenta?
            <a class="text-rose fw-bold" href="<?= url('auth/login.php') ?>">Inicia sesión</a>
        </p>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>