<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Usuarios';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    $estado = $_POST['estado'] ?? '';

    $permitidos = ['activo', 'inactivo', 'bloqueado'];

    if ($id_usuario === (int)$_SESSION['usuario']['id_usuario']) {
        $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'No puedes cambiar tu propio estado.'];
    } elseif (in_array($estado, $permitidos)) {
        $stmt = $conexion->prepare("UPDATE usuarios SET estado = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $estado, $id_usuario);
        $stmt->execute();

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Estado de usuario actualizado.'];
    }

    header('Location: ' . url('admin/usuarios.php'));
    exit;
}

$q = trim($_GET['q'] ?? '');
$rol = $_GET['rol'] ?? '';
$estado = $_GET['estado'] ?? '';

$sql = "SELECT * FROM usuarios WHERE 1=1";
$params = [];
$tipos = '';

if ($q !== '') {
    $sql .= " AND (nombre LIKE ? OR correo LIKE ?)";
    $like = "%$q%";
    $params[] = $like;
    $params[] = $like;
    $tipos .= 'ss';
}

if ($rol !== '') {
    $sql .= " AND rol = ?";
    $params[] = $rol;
    $tipos .= 's';
}

if ($estado !== '') {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $tipos .= 's';
}

$sql .= " ORDER BY fecha_registro DESC";

$stmt = $conexion->prepare($sql);

if ($params) {
    $stmt->bind_param($tipos, ...$params);
}

$stmt->execute();

$usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <h1 class="section-title mb-4">Usuarios</h1>

            <form method="get" class="soft-card p-3 mb-4">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input name="q" class="form-control" placeholder="Nombre o correo" value="<?= e($q) ?>">
                    </div>

                    <div class="col-md-3">
                        <select name="rol" class="form-select">
                            <option value="">Todos los roles</option>
                            <option value="admin" <?= $rol === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="cliente" <?= $rol === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            <option value="bloqueado" <?= $estado === 'bloqueado' ? 'selected' : '' ?>>Bloqueado</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-rose w-100 rounded-pill">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="table-card p-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= $u['id_usuario'] ?></td>
                                    <td><?= e($u['nombre']) ?></td>
                                    <td><?= e($u['correo']) ?></td>
                                    <td><?= e($u['rol']) ?></td>
                                    <td><?= e($u['estado']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></td>
                                    <td>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                            <input type="hidden" name="estado" value="activo">
                                            <button class="btn btn-sm btn-success rounded-pill">Activar</button>
                                        </form>

                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                            <input type="hidden" name="estado" value="inactivo">
                                            <button class="btn btn-sm btn-warning rounded-pill">Desactivar</button>
                                        </form>

                                        <form method="post" class="d-inline" data-confirm="¿Bloquear usuario?">
                                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                            <input type="hidden" name="estado" value="bloqueado">
                                            <button class="btn btn-sm btn-danger rounded-pill">Bloquear</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$usuarios): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay usuarios con esos filtros.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>