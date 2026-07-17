<!-- Mantenimiento de Usuarios -->
<div class="row">
    <div class="col-sm-12">
        <div class="card-box" style="background: #ffffff; padding: 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">

            <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#modalAgregarUsuario">
                    <i class="fa fa-plus m-r-5"></i> Agregar Usuario
                </button>
            </div>

            <div class="table-responsive" style="border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden;">
                <table class="table table-striped table-bordered table-hover m-0" style="background-color: #ffffff; color: #333333; width: 100%; border: none;">
                    <thead style="background-color: #3b3e47; color: #ffffff;">
                        <tr>
                            <th style="color: #ffffff; font-weight: 600; width: 70px;">ID</th>
                            <th style="color: #ffffff; font-weight: 600;">Nombre</th>
                            <th style="color: #ffffff; font-weight: 600;">Apellido</th>
                            <th style="color: #ffffff; font-weight: 600;">Usuario</th>
                            <th style="color: #ffffff; font-weight: 600;">Grupo</th>
                            <th style="color: #ffffff; font-weight: 600;">Estado</th>
                            <th style="color: #ffffff; font-weight: 600; text-align: center; width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $row): ?>
                                <?php $esSuperadmin = (strtolower($row->usuario_user) === 'superadmin'); ?>
                                <tr style="color: #333333;">
                                    <td><span class="label label-default" style="font-size: 11px; padding: 4px 8px;"><?php echo $row->usuario_id; ?></span></td>
                                    <td style="font-weight: 600; color: #2b2c30;"><?php echo htmlspecialchars($row->usuario_nombre); ?></td>
                                    <td><?php echo htmlspecialchars($row->usuario_apellido); ?></td>
                                    <td><code style="color: #d05; font-size: 13px;"><?php echo htmlspecialchars($row->usuario_user); ?></code></td>
                                    <td><?php echo htmlspecialchars($row->usuario_grupos ? $row->usuario_grupos : '—'); ?></td>
                                    <td>
                                        <?php if ($row->usuario_estado === 'activo'): ?>
                                            <span class="label label-success" style="font-size: 11px; padding: 4px 8px;">activo</span>
                                        <?php else: ?>
                                            <span class="label label-danger" style="font-size: 11px; padding: 4px 8px;"><?php echo htmlspecialchars($row->usuario_estado); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <button class="btn btn-warning btn-xs waves-effect waves-light btn-editar"
                                                data-toggle="modal"
                                                data-target="#modalEditarUsuario"
                                                data-id="<?php echo $row->usuario_id; ?>"
                                                data-nombre="<?php echo htmlspecialchars($row->usuario_nombre, ENT_QUOTES); ?>"
                                                data-apellido="<?php echo htmlspecialchars($row->usuario_apellido, ENT_QUOTES); ?>"
                                                data-user="<?php echo htmlspecialchars($row->usuario_user, ENT_QUOTES); ?>"
                                                data-estado="<?php echo htmlspecialchars($row->usuario_estado, ENT_QUOTES); ?>"
                                                data-grupo="<?php echo intval($row->grupo_id); ?>"
                                                data-essuper="<?php echo $esSuperadmin ? '1' : '0'; ?>"
                                                style="margin-right: 5px;">
                                            <i class="fa fa-pencil"></i>
                                        </button>

                                        <?php if (!$esSuperadmin): ?>
                                        <a href="<?php echo base_url('usuarios/eliminar/'.$row->usuario_id); ?>"
                                           class="btn btn-danger btn-xs waves-effect waves-light"
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="color: #777777; padding: 20px;">No hay usuarios registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Modal Agregar Usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #ffffff; border-radius: 6px;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e5e5; padding: 15px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" style="color: #333; font-weight: 600;">Nuevo Usuario</h4>
            </div>

            <form action="<?php echo base_url('usuarios/guardar'); ?>" method="POST" autocomplete="off">
                <div class="modal-body" style="padding: 20px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Nombre</label>
                                <input type="text" name="usuario_nombre" class="form-control" placeholder="Nombre" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Apellido</label>
                                <input type="text" name="usuario_apellido" class="form-control" placeholder="Apellido" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Usuario</label>
                                <input type="text" name="usuario_user" class="form-control" placeholder="usuario.login" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Contraseña</label>
                                <input type="password" name="usuario_pass" class="form-control" placeholder="Contraseña" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Grupo</label>
                                <select name="grupo_id" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                                    <?php foreach ($grupos as $g): ?>
                                        <option value="<?php echo $g->grupo_id; ?>" <?php echo ($g->grupo_id == 2) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($g->grupo_nombre); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Estado</label>
                                <select name="usuario_estado" class="form-control" style="border: 1px solid #ccc; color: #333;">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e5e5e5; padding: 15px;">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #ffffff; border-radius: 6px;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e5e5; padding: 15px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" style="color: #333; font-weight: 600;">Editar Usuario</h4>
            </div>

            <form action="<?php echo base_url('usuarios/actualizar'); ?>" method="POST" autocomplete="off">
                <input type="hidden" name="usuario_id" id="edit_usuario_id">

                <div class="modal-body" style="padding: 20px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Nombre</label>
                                <input type="text" name="usuario_nombre" id="edit_nombre" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Apellido</label>
                                <input type="text" name="usuario_apellido" id="edit_apellido" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Usuario</label>
                                <input type="text" name="usuario_user" id="edit_user" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Contraseña</label>
                                <input type="password" name="usuario_pass" id="edit_pass" class="form-control" placeholder="Dejar vacío para no cambiar" style="border: 1px solid #ccc; color: #333;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Grupo</label>
                                <select name="grupo_id" id="edit_grupo" class="form-control" required style="border: 1px solid #ccc; color: #333;">
                                    <?php foreach ($grupos as $g): ?>
                                        <option value="<?php echo $g->grupo_id; ?>">
                                            <?php echo htmlspecialchars($g->grupo_nombre); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="color: #333; font-weight: 600;">Estado</label>
                                <select name="usuario_estado" id="edit_estado" class="form-control" style="border: 1px solid #ccc; color: #333;">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-editar').on('click', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var apellido = $(this).data('apellido');
        var user = $(this).data('user');
        var estado = $(this).data('estado');
        var grupo = $(this).data('grupo');
        var esSuper = $(this).data('essuper') == 1;

        $('#edit_usuario_id').val(id);
        $('#edit_nombre').val(nombre);
        $('#edit_apellido').val(apellido);
        $('#edit_user').val(user);
        $('#edit_estado').val(estado);
        $('#edit_grupo').val(grupo);
        $('#edit_pass').val('');

        // El login superadmin no se puede renombrar
        $('#edit_user').prop('readonly', esSuper);
        if (esSuper) {
            $('#edit_grupo').val(1).prop('disabled', true);
            // disabled no se envía: usamos un hidden
            if ($('#edit_grupo_hidden').length === 0) {
                $('#edit_grupo').after('<input type="hidden" name="grupo_id" id="edit_grupo_hidden" value="1">');
            } else {
                $('#edit_grupo_hidden').val(1);
            }
        } else {
            $('#edit_grupo').prop('disabled', false);
            $('#edit_grupo_hidden').remove();
        }
    });
});
</script>
