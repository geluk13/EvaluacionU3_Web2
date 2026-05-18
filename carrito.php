<?php
require_once __DIR__ . '/config/conexion.php';

requiereCliente();

$titulo = 'Carrito';
$carrito = obtenerCarritoCompleto($conexion);

include __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <h1 class="section-title mb-4">Carrito de compras</h1>

    <?php if (!$carrito['items']): ?>
        <div class="empty-state text-center">
            <h4>Tu carrito está vacío</h4>
            <a href="<?= url('index.php#catalogo') ?>" class="btn btn-rose rounded-pill">Ver catálogo</a>
        </div>
    <?php else: ?>
        <div class="table-card p-4">
            <form method="post" action="<?= url('carrito_accion.php') ?>">
                <input type="hidden" name="accion" value="actualizar">

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($carrito['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <img src="<?= url($item['imagen']) ?>" width="70" class="rounded-4 me-2">
                                        <strong><?= e($item['nombre']) ?></strong>
                                    </td>
                                    <td>Bs. <?= number_format($item['precio'], 2) ?></td>
                                    <td>
                                        <input type="number" min="1" max="<?= $item['stock'] ?>" name="cantidades[<?= $item['id_producto'] ?>]" value="<?= $item['cantidad'] ?>" class="form-control" style="max-width:90px">
                                    </td>
                                    <td>Bs. <?= number_format($item['subtotal'], 2) ?></td>
                                    <td>
                                        <button type="submit" form="eliminar<?= $item['id_producto'] ?>" class="btn btn-outline-danger btn-sm rounded-pill">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-rose rounded-pill">Actualizar</button>
                    <h3>Total: <span class="text-rose">Bs. <?= number_format($carrito['total'], 2) ?></span></h3>
                </div>
            </form>

            <?php foreach ($carrito['items'] as $item): ?>
                <form id="eliminar<?= $item['id_producto'] ?>" method="post" action="<?= url('carrito_accion.php') ?>" data-confirm="¿Eliminar este producto?">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_producto" value="<?= $item['id_producto'] ?>">
                </form>
            <?php endforeach; ?>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <form method="post" action="<?= url('carrito_accion.php') ?>" data-confirm="¿Vaciar carrito?">
                    <input type="hidden" name="accion" value="vaciar">
                    <button class="btn btn-light rounded-pill">Vaciar</button>
                </form>

                <a href="<?= url('comprar.php') ?>" class="btn btn-rose rounded-pill">Confirmar compra</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>