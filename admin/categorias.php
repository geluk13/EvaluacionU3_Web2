<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Categorías';

$id_editar = (int)($_GET['editar'] ?? 0);
$categoriaEditar = null;

if ($id_editar > 0) {
    $stmt = $conexion->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();

    $categoriaEditar = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);

    if ($accion === 'guardar') {
        $nombre = trim($_POST['nombre_categoria'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($nombre === '') {
            $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'El nombre es obligatorio.'];
        } else {
            if ($id_categoria > 0) {
                $stmt = $conexion->prepare("UPDATE categorias SET nombre_categoria = ?, descripcion = ? WHERE id_categoria = ?");
                $stmt->bind_param("ssi", $nombre, $descripcion, $id_categoria);
                $stmt->execute();

                $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Categoría actualizada.'];
            } else {
                $stmt = $conexion->prepare("INSERT INTO categorias (nombre_categoria, descripcion) VALUES (?, ?)");
                $stmt->bind_param("ss", $nombre, $descripcion);
                $stmt->execute();

                $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Categoría registrada.'];
            }
        }

        header('Location: ' . url('admin/categorias.php'));
        exit;
    }

    if ($accion === 'eliminar') {
        try {
            $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
            $stmt->bind_param("i", $id_categoria);
            $stmt->execute();

            $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Categoría eliminada.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'No se puede eliminar porque tiene productos asociados.'];
        }

        header('Location: ' . url('admin/categorias.php'));
        exit;
    }
}

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY id_categoria DESC")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <h1 class="section-title mb-4">CRUD de categorías</h1>

            <div class="row g-4">
                <div class="col-lg-5">
                    <form method="post" class="auth-card m-0">
                        <h4><?= $categoriaEditar ? 'Editar categoría' : 'Nueva categoría' ?></h4>

                        <input type="hidden" name="accion" value="guardar">
                        <input type="hidden" name="id_categoria" value="<?= e($categoriaEditar['id_categoria'] ?? 0) ?>">

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input name="nombre_categoria" class="form-control" value="<?= e($categoriaEditar['nombre_categoria'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="4"><?= e($categoriaEditar['descripcion'] ?? '') ?></textarea>
                        </div>

                        <button class="btn btn-rose w-100 rounded-pill">Guardar</button>
                    </form>
                </div>

                <div class="col-lg-7">
                    <div class="table-card p-3">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($categorias as $cat): ?>
                                    <tr>
                                        <td><?= $cat['id_categoria'] ?></td>
                                        <td><?= e($cat['nombre_categoria']) ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-light rounded-pill" href="<?= url('admin/categoria_detalle.php?id=' . $cat['id_categoria']) ?>">Detalle</a>
                                            <a class="btn btn-sm btn-outline-rose rounded-pill" href="<?= url('admin/categorias.php?editar=' . $cat['id_categoria']) ?>">Editar</a>

                                            <form method="post" class="d-inline" data-confirm="¿Eliminar categoría?">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                                                <button class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>