<?php
require_once __DIR__ . '/config/conexion.php';

requiereCliente();

$titulo = 'Favoritos';
$id_usuario = (int)$_SESSION['usuario']['id_usuario'];

$stmt = $conexion->prepare("
    SELECT p.*, c.nombre_categoria
    FROM favoritos f
    INNER JOIN productos p ON f.id_producto = p.id_producto
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE f.id_usuario = ?
    ORDER BY f.fecha DESC
");

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$favoritos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <h1 class="section-title mb-4">Mis favoritos</h1>

    <?php if (!$favoritos): ?>
        <div class="empty-state text-center">
            <h4>No tienes favoritos</h4>
            <a href="<?= url('index.php#catalogo') ?>" class="btn btn-rose rounded-pill">Ver catálogo</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($favoritos as $producto): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="product-card h-100">
                        <img src="<?= url($producto['imagen']) ?>" class="product-img">

                        <div class="product-body">
                            <h5><?= e($producto['nombre']) ?></h5>
                            <p class="text-muted small"><?= e($producto['descripcion']) ?></p>
                            <strong class="text-rose">Bs. <?= number_format($producto['precio'], 2) ?></strong>

                            <form method="post" action="<?= url('favorito_accion.php') ?>" class="mt-3" data-confirm="¿Eliminar favorito?">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                <button class="btn btn-outline-danger btn-sm rounded-pill w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>