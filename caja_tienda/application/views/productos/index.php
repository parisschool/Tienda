<!-- Mantenimiento de Productos -->
<div class="row">
    <div class="col-sm-12">
        <!-- Tarjeta blanca principal -->
        <div class="card-box" style="background: #ffffff; padding: 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
            
            <!-- Botón completamente FUERA de la tabla y sus contenedores grises -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#modalAgregarProducto">
                    <i class="fa fa-plus m-r-5"></i> Agregar Producto
                </button>
            </div>

            <!-- Contenedor con borde gris que SÓLO envuelve a la tabla -->
            <div class="table-responsive" style="border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden;">
                <table class="table table-striped table-bordered table-hover m-0" style="background-color: #ffffff; color: #333333; width: 100%; border: none;">
                    <thead style="background-color: #3b3e47; color: #ffffff;">
                        <tr>
                            <th style="color: #ffffff; font-weight: 600; width: 80px;">ID</th>
                            <th style="color: #ffffff; font-weight: 600;">Código de Barras</th>
                            <th style="color: #ffffff; font-weight: 600;">Nombre del Producto</th>
                            <th style="color: #ffffff; font-weight: 600;">Precio de Venta</th>
                            <th style="color: #ffffff; font-weight: 600;">Stock</th>
                            <th style="color: #ffffff; font-weight: 600; text-align: center; width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($productos)): ?>
                            <?php foreach($productos as $row): ?>
                                <tr style="color: #333333;">
                                    <td><span class="label label-default" style="font-size: 11px; padding: 4px 8px;"><?php echo $row->id; ?></span></td>
                                    <td><code style="color: #d05; font-size: 13px;"><?php echo $row->codigo_barras; ?></code></td>
                                    <td style="font-weight: 600; color: #2b2c30;"><?php echo $row->nombre; ?></td>
                                    <td style="color: #2b957a; font-weight: bold;">$<?php echo number_format($row->precio_venta, 2); ?></td>
                                    <td>
                                        <?php if($row->stock > 0): ?>
                                            <span class="label label-success" style="font-size: 11px; padding: 4px 8px;"><?php echo $row->stock; ?> UDS</span>
                                        <?php else: ?>
                                            <span class="label label-danger" style="font-size: 11px; padding: 4px 8px;"><?php echo $row->stock; ?> UDS</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <!-- Botón Editar -->
                                        <button class="btn btn-warning btn-xs waves-effect waves-light btn-editar" 
                                                data-toggle="modal" 
                                                data-target="#modalEditarProducto"
                                                data-id="<?php echo $row->id; ?>"
                                                data-codigo="<?php echo $row->codigo_barras; ?>"
                                                data-nombre="<?php echo $row->nombre; ?>"
                                                data-precio="<?php echo $row->precio_venta; ?>"
                                                data-stock="<?php echo $row->stock; ?>"
                                                style="margin-right: 5px;">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        
                                        <!-- Botón Eliminar -->
                                        <a href="<?php echo base_url('productos/eliminar/'.$row->id); ?>" 
                                           class="btn btn-danger btn-xs waves-effect waves-light" 
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center" style="color: #777777; padding: 20px;">No hay productos registrados en la base de datos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div> <!-- Fin de table-responsive -->

        </div>
    </div>
</div>
<!-- Modal Agregar Producto -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #ffffff; border-radius: 6px;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e5e5; padding: 15px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" style="color: #333; font-weight: 600;">Nuevo Producto</h4>
            </div>
            
            <form action="<?php echo base_url('productos/guardar'); ?>" method="POST" autocomplete="off">
                <div class="modal-body" style="padding: 20px;">
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="color: #333; font-weight: 600;">Código de Barras</label>
                        <input type="text" name="codigo_barras" class="form-control" placeholder="Ej. 7501234567890" required style="border: 1px solid #ccc; color: #333;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="color: #333; font-weight: 600;">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Coca Cola 600ml" required style="border: 1px solid #ccc; color: #333;">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Precio de Venta</label>
                                <input type="number" step="0.01" name="precio_venta" class="form-control" placeholder="0.00" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Stock Inicial</label>
                                <input type="number" name="stock" class="form-control" placeholder="0" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="border-top: 1px solid #e5e5e5; padding: 15px;">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #ffffff; border-radius: 6px;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e5e5; padding: 15px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" style="color: #333; font-weight: 600;">Editar Producto</h4>
            </div>
            
            <form action="<?php echo base_url('productos/actualizar'); ?>" method="POST" autocomplete="off">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body" style="padding: 20px;">
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="color: #333; font-weight: 600;">Código de Barras</label>
                        <input type="text" name="codigo_barras" id="edit_codigo" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="color: #333; font-weight: 600;">Nombre del Producto</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Precio de Venta</label>
                                <input type="number" step="0.01" name="precio_venta" id="edit_precio" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Stock</label>
                                <input type="number" name="stock" id="edit_stock" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="border-top: 1px solid #e5e5e5; padding: 15px;">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning waves-effect waves-light">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script jQuery para transferir los datos al modal de edición -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-editar').on('click', function() {
        var id = $(this).data('id');
        var codigo = $(this).data('codigo');
        var nombre = $(this).data('nombre');
        var precio = $(this).data('precio');
        var stock = $(this).data('stock');

        $('#edit_id').val(id);
        $('#edit_codigo').val(codigo);
        $('#edit_nombre').val(nombre);
        $('#edit_precio').val(precio);
        $('#edit_stock').val(stock);
    });
});
</script>