<?php
require_once __DIR__ . '/../config/conexion.php';

requiereAdmin();

$titulo = 'Formulario producto';

$id = (int)($_GET['id'] ?? 0);
$producto = null;

if ($id > 0) {
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $producto = $stmt->get_result()->fetch_assoc();
}

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre_categoria")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $marca = trim($_POST['marca'] ?? 'Jarbera Douce');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $estado = isset($_POST['estado']) ? 1 : 0;
    $imagen = $producto['imagen'] ?? 'assets/img/default.svg';

    if (!empty($_FILES['imagen']['name'])) {
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'producto_' . time() . '.' . $extension;
        $destino = ROOT_PATH . '/assets/uploads/' . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
            $imagen = 'assets/uploads/' . $nombreArchivo;
        }
    }

    if ($producto) {
        $stmt = $conexion->prepare("
            UPDATE productos 
            SET id_categoria=?, nombre=?, marca=?, descripcion=?, precio=?, stock=?, imagen=?, destacado=?, estado=?
            WHERE id_producto=?
        ");

        $stmt->bind_param("isssdisiii", $id_categoria, $nombre, $marca, $descripcion, $precio, $stock, $imagen, $destacado, $estado, $id);
        $stmt->execute();

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto actualizado.'];
    } else {
        $stmt = $conexion->prepare("
            INSERT INTO productos (id_categoria, nombre, marca, descripcion, precio, stock, imagen, destacado, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("isssdisii", $id_categoria, $nombre, $marca, $descripcion, $precio, $stock, $imagen, $destacado, $estado);
        $stmt->execute();

        $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto registrado.'];
    }

    header('Location: ' . url('admin/productos.php'));
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <div class="table-card p-4">
                <h1 class="section-title"><?= $producto ? 'Editar producto' : 'Nuevo producto' ?></h1>

                <form method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Categoría</label>
                            <select name="id_categoria" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= (($producto['id_categoria'] ?? '') == $cat['id_categoria']) ? 'selected' : '' ?>>
                                        <?= e($cat['nombre_categoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input name="nombre" class="form-control" value="<?= e($producto['nombre'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label>Marca</label>
                            <input name="marca" class="form-control" value="<?= e($producto['marca'] ?? 'Jarbera Douce') ?>">
                        </div>

                        <div class="col-md-3">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" value="<?= e($producto['precio'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label>Stock</label>
                            <input type="number" name="stock" class="form-control" value="<?= e($producto['stock'] ?? 0) ?>" required>
                        </div>

                        <div class="col-12">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="4"><?= e($producto['descripcion'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label>Imagen</label>
                            <input type="file" name="imagen" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="d-block">Destacado</label>
                            <input type="checkbox" name="destacado" <?= !empty($producto['destacado']) ? 'checked' : '' ?>>
                        </div>

                        <div class="col-md-3">
                            <label class="d-block">Activo</label>
                            <input type="checkbox" name="estado" <?= !isset($producto['estado']) || $producto['estado'] ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-rose rounded-pill">Guardar</button>
                        <a href="<?= url('admin/productos.php') ?>" class="btn btn-light rounded-pill">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>