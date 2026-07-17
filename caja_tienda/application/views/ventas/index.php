<style>
    /* Ocultar flechas por defecto de los inputs de cantidad */
    .cant-item::-webkit-outer-spin-button,
    .cant-item::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .cant-item {
        -moz-appearance: textfield;
    }
</style>

<div class="row">
    <!-- COLUMNA IZQUIERDA: Escáner y Lista de Productos -->
    <div class="col-md-8">
        <div class="card-box" style="background: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); min-height: 500px;">
            
            <!-- Input del Buscador / Escáner con posicionamiento relativo para el desplegable -->
            <div class="form-group" style="margin-bottom: 20px; position: relative;">
                <label style="color: #333; font-weight: 600; font-size: 16px;">Captura de código o nombre del artículo</label>
                <div class="input-group">
                    <span class="input-group-addon" style="background: #eee; font-size: 20px; padding: 6px 15px;"><i class="fa fa-barcode"></i></span>
                    <input type="text" id="codigo_barras" class="form-control" placeholder="Escanea o escribe el nombre del producto..." autocomplete="off" autofocus style="border: 1px solid #ccc; color: #333; font-size: 18px; height: 50px;">
                </div>

                <!-- MENÚ DESPLEGABLE DE SUGERENCIAS -->
                <div id="contenedor-sugerencias" style="display: none; position: absolute; top: 80px; left: 0; right: 0; background: #ffffff; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 9999; max-height: 250px; overflow-y: auto;">
                    <!-- Las opciones se generarán aquí con JS -->
                </div>
            </div>

            <!-- Tabla de artículos escaneados -->
            <div class="table-responsive" style="border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden; min-height: 300px;">
                <table class="table table-striped table-hover m-0" id="tabla-ventas" style="background-color: #ffffff; color: #333333; width: 100%;">
                    <thead style="background-color: #3b3e47; color: #ffffff;">
                        <tr>
                            <th style="color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 10px;">Artículo</th>
                            <th style="color: #ffffff; font-weight: 600; width: 120px; text-align: center; font-size: 15px; padding: 12px 10px;">Cantidad</th>
                            <th style="color: #ffffff; font-weight: 600; width: 120px; text-align: right; font-size: 15px; padding: 12px 10px;">Precio</th>
                            <th style="color: #ffffff; font-weight: 600; width: 120px; text-align: right; font-size: 15px; padding: 12px 10px;">Total</th>
                            <th style="color: #ffffff; font-weight: 600; text-align: center; width: 90px; font-size: 15px; padding: 12px 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalle-venta">
                        <tr id="fila-vacia">
                            <td colspan="5" class="text-center" style="color: #999; padding: 80px 0; font-size: 15px;">
                                <i class="fa fa-shopping-cart" style="font-size: 40px; display: block; margin-bottom: 10px; color: #ccc;"></i>
                                Esperando productos... Escanea o ingresa un código de barras.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- COLUMNA DERECHA: Total de Venta y Acciones -->
    <div class="col-md-4">
        <div class="card-box" style="background: #2b3e50; padding: 30px 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center; color: #ffffff; margin-bottom: 20px;">
            <h4 style="margin: 0 0 10px 0; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #aab2bd; font-size: 16px;">Total a Pagar</h4>
            <div style="font-size: 56px; font-weight: bold; font-family: monospace; color: #2ecc71;" id="total-gigante">
                $0.00
            </div>
            <div style="font-size: 16px; color: #ffffff; margin-top: 10px; opacity: 0.8;" id="items-count">
                0 artículos en el carrito
            </div>
        </div>

        <div class="card-box" style="background: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <button class="btn btn-success btn-lg btn-block waves-effect waves-light" style="margin-bottom: 15px; font-weight: bold; font-size: 20px; height: 55px; border-radius: 6px;">
                <i class="fa fa-money m-r-5"></i> COBRAR
            </button>
            <button class="btn btn-danger btn-block waves-effect" style="font-weight: bold; font-size: 16px; height: 45px; border-radius: 6px;">
                <i class="fa fa-ban m-r-5"></i> Cancelar Venta
            </button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Variable global para guardar el total numérico a pagar
    var totalAPagarGlobal = 0;

    // --- SECCIÓN: CONTROL DEL MODAL DE COBRO ---

    // Al presionar el botón COBRAR
    $(document).on('click', '.btn-success.btn-lg.btn-block', function() {
        abrirModalCobro();
    });

    // Soporte para tecla rápida F12
    $(document).on('keydown', function(e) {
        if (e.which === 123) { // 123 es F12
            e.preventDefault();
            abrirModalCobro();
        }
    });

    function abrirModalCobro() {
        // Validar que haya productos agregados
        if ($('.item-carrito').length === 0) {
            alert('Agrega al menos un artículo para poder cobrar.');
            return;
        }

        // Obtener el total actual calculándolo del DOM
        var granTotal = 0;
        $('.item-carrito').each(function() {
            var cantidad = parseInt($(this).find('.cant-item').val());
            var precio = parseFloat($(this).data('precio'));
            granTotal += (cantidad * precio);
        });

        totalAPagarGlobal = granTotal;

        // Limpiar inputs del modal
        $('#modal-total-cobrar').text('$' + totalAPagarGlobal.toFixed(2));
        $('#pago-recibido').val('').css('border-color', '#2b957a');
        $('#modal-cambio').text('$0.00').css('color', '#d9534f');
        $('#btn-finalizar-venta').prop('disabled', true);

        // Mostrar modal y enfocar el input de pago recibido
        $('#modal-cobrar').modal('show');
        setTimeout(function() {
            $('#pago-recibido').focus();
        }, 500);
    }

    // Calcular el cambio en tiempo real mientras el usuario escribe
    $(document).on('input', '#pago-recibido', function() {
        var recibido = parseFloat($(this).val());
        
        if (isNaN(recibido) || recibido < 0) {
            $('#modal-cambio').text('$0.00').css('color', '#d9534f');
            $('#btn-finalizar-venta').prop('disabled', true);
            $(this).css('border-color', '#d9534f');
            return;
        }

        var cambio = recibido - totalAPagarGlobal;

        if (cambio >= 0) {
            $('#modal-cambio').text('$' + cambio.toFixed(2)).css('color', '#2b957a');
            $('#btn-finalizar-venta').prop('disabled', false);
            $(this).css('border-color', '#2b957a');
        } else {
            // Si el dinero recibido es menor al total de la compra
            $('#modal-cambio').text('Falta: $' + Math.abs(cambio).toFixed(2)).css('color', '#d9534f');
            $('#btn-finalizar-venta').prop('disabled', true);
            $(this).css('border-color', '#f0ad4e');
        }
    });

    // Enter en el modal de cobro = botón Listo
    $(document).on('keydown', '#pago-recibido, #modal-cobrar', function(e) {
        if (e.which === 13 || e.key === 'Enter') {
            e.preventDefault();
            if ($('#modal-cobrar').is(':visible') && !$('#btn-finalizar-venta').prop('disabled')) {
                $('#btn-finalizar-venta').click();
            }
        }
    });

    // Acción del botón LISTO (Guardar Venta, actualizar Stock y registrar en Pagos)
    $('#btn-finalizar-venta').on('click', function() {
        var articulos = [];

        // Extraer id, cantidad y precio de la tabla
        $('.item-carrito').each(function() {
            var id = $(this).find('.cant-item').data('id');
            var cantidad = $(this).find('.cant-item').val();
            var precio = $(this).data('precio');
            articulos.push({
                id: id,
                cantidad: cantidad,
                precio: precio
            });
        });

        var efectivoRecibido = $('#pago-recibido').val();

        // Cambiar temporalmente el botón a un estado de carga mientras responde el servidor
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin" style="font-size: 16px;"></i> Procesando...');

        var $btn = $(this);

        $.ajax({
            url: "<?php echo base_url('ventas/procesar_venta'); ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                articulos: articulos,
                efectivo: efectivoRecibido,
                pago_forma_id: 1
            },
            success: function(response) {
                if (response.status === 'success') {
                    // 1. Reemplazamos el cuerpo del modal con un diseño estético de éxito
                    $('#modal-cobrar .modal-body').html(`
                        <div class="text-center" style="padding: 30px 10px; animation: fadeIn 0.5s;">
                            <i class="fa fa-check-circle" style="font-size: 60px; color: #2ecc71; margin-bottom: 15px; display: block;"></i>
                            <h3 style="color: #2c3e50; font-weight: bold; margin-bottom: 5px;">¡Listo!</h3>
                            <p style="color: #7f8c8d; font-size: 15px; margin: 0;">Operación realizada con éxito</p>
                        </div>
                    `);

                    // Ocultamos el footer del modal para que no se vean los botones de "Cancelar" y "Listo"
                    $('#modal-cobrar .modal-footer').hide();

                    // 2. Esperamos 1.5 segundos, cerramos el modal y recargamos la página
                    setTimeout(function() {
                        $('#modal-cobrar').modal('hide');
                        location.reload(); // Recarga la página limpia
                    }, 1500);

                } else {
                    // Si el servidor detecta un error (ej. se acabó el stock mientras cobraba)
                    alert('Error: ' + response.message);
                    $btn.prop('disabled', false).html('<i class="fa fa-check-circle" style="font-size: 16px;"></i> Listo');
                }
            },
            error: function(xhr, status, error) {
                var detalle = '';
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp && resp.message) {
                        detalle = resp.message;
                    }
                } catch (e) {
                    detalle = (xhr.responseText || error || status).toString().substring(0, 300);
                }
                alert('Error al procesar la venta: ' + (detalle || 'fallo de red'));
                console.error("Detalle del error:", status, error, xhr.responseText);
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle" style="font-size: 16px;"></i> Listo');
            }
        });
    });


    // --- SECCIÓN: FUNCIONAMIENTO DE LA BÚSQUEDA Y LA TABLA ---

    $('#codigo_barras').focus();
    
    // Al dar clic fuera del buscador, ocultamos las sugerencias
    $(document).click(function(e) {
        if (!$(e.target).closest('#codigo_barras').length && !$(e.target).closest('#contenedor-sugerencias').length) {
            $('#contenedor-sugerencias').hide();
        }
    });

    // Volver a enfocar el input principal si se hace clic en el fondo blanco de la app
    $('.card-box').click(function(e) {
        if (!$(e.target).is('input') && !$(e.target).is('button') && !$(e.target).is('i') && !$(e.target).closest('#contenedor-sugerencias').length) {
            $('#codigo_barras').focus();
        }
    });

    // Detectar escritura en tiempo real
    $('#codigo_barras').on('input', function() {
        var busqueda = $(this).val().trim();

        if (busqueda.length >= 2) {
            $.ajax({
                url: "<?php echo base_url('ventas/buscar_por_codigo'); ?>",
                type: "POST",
                dataType: "JSON",
                data: { codigo_barras: busqueda },
                success: function(response) {
                    if (response.status === 'success' && response.action === 'show_suggestions') {
                        renderizarSugerencias(response.productos);
                    } else {
                        $('#contenedor-sugerencias').hide();
                    }
                }
            });
        } else {
            $('#contenedor-sugerencias').hide();
        }
    });

    // Manejar el evento de presionar Enter directo (Escáner de código de barras)
    $('#codigo_barras').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            var codigo = $(this).val().trim();

            if (codigo !== '') {
                $.ajax({
                    url: "<?php echo base_url('ventas/buscar_por_codigo'); ?>",
                    type: "POST",
                    dataType: "JSON",
                    data: { codigo_barras: codigo },
                    success: function(response) {
                        if (response.status === 'success' && response.action === 'add_direct') {
                            agregarFilaTabla(response.producto);
                            $('#codigo_barras').val('');
                            $('#contenedor-sugerencias').hide();
                        } else {
                            var primerItem = $('.item-sugerido').first();
                            if (primerItem.length > 0) {
                                primerItem.click();
                            }
                        }
                    }
                });
            }
        }
    });

    function renderizarSugerencias(productos) {
        var html = '';
        productos.forEach(function(p) {
            html += `
                <div class="item-sugerido" 
                     data-id="${p.id}" 
                     data-codigo="${p.codigo_barras}" 
                     data-nombre="${p.nombre}" 
                     data-precio="${p.precio_venta}" 
                     data-stock="${p.stock}"
                     style="padding: 12px 18px; border-bottom: 1px solid #f1f1f1; cursor: pointer; display: flex; justify-content: space-between; transition: background 0.2s;">
                    <div>
                        <strong style="color: #333; font-size: 16px;">${p.nombre}</strong> <br>
                        <small style="color: #777; font-size: 12px;">Cód: ${p.codigo_barras}</small>
                    </div>
                    <div style="text-align: right; vertical-align: middle;">
                        <span style="color: #2b957a; font-weight: bold; display: block; font-size: 16px;">$${parseFloat(p.precio_venta).toFixed(2)}</span>
                        <small class="label label-default" style="font-size: 11px;">Stock: ${p.stock}</small>
                    </div>
                </div>
            `;
        });
        $('#contenedor-sugerencias').html(html).show();

        $('.item-sugerido').hover(
            function() { $(this).css('background-color', '#f5f7f8'); },
            function() { $(this).css('background-color', '#ffffff'); }
        );
    }

    $(document).on('click', '.item-sugerido', function() {
        var prod = {
            id: $(this).data('id'),
            codigo_barras: $(this).data('codigo'),
            nombre: $(this).data('nombre'),
            precio_venta: $(this).data('precio'),
            stock: $(this).data('stock')
        };

        if (prod.stock <= 0) {
            alert('Este producto no cuenta con stock disponible.');
            return;
        }

        agregarFilaTabla(prod);
        $('#codigo_barras').val('').focus();
        $('#contenedor-sugerencias').hide();
    });

    function agregarFilaTabla(producto) {
        $('#fila-vacia').remove();
        var filaExistente = $('#fila-prod-' + producto.id);

        if (filaExistente.length > 0) {
            var inputCant = filaExistente.find('.cant-item');
            var nuevaCantidad = parseInt(inputCant.val()) + 1;
            
            if (nuevaCantidad > producto.stock) {
                alert('No puedes agregar más. Stock máximo: ' + producto.stock + ' Uds.');
                return;
            }
            
            inputCant.val(nuevaCantidad);
            actualizarFila(filaExistente, nuevaCantidad, parseFloat(producto.precio_venta));
        } else {
            var nuevaFila = `
                <tr id="fila-prod-${producto.id}" class="item-carrito" data-precio="${producto.precio_venta}">
                    <td style="font-weight: 600; color: #2b2c30; vertical-align: middle; font-size: 16px; padding: 12px 10px;">${producto.nombre}</td>
                    <td style="vertical-align: middle; text-align: center; padding: 12px 10px;">
                        <div class="input-group" style="width: 115px; margin: 0 auto;">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-menos" style="height: 34px; padding: 5px 10px; border-radius: 4px 0 0 4px; border: 1px solid #ccc; background: #eee;">
                                    <i class="fa fa-minus" style="font-size: 11px; color: #555;"></i>
                                </button>
                            </span>
                            <input type="number" class="form-control cant-item" value="1" min="1" max="${producto.stock}" data-id="${producto.id}" data-precio="${producto.precio_venta}" style="width: 50px; height: 34px; text-align: center; padding: 2px; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; border-left: 0; border-right: 0; font-weight: bold; font-size: 15px;">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-mas" style="height: 34px; padding: 5px 10px; border-radius: 0 4px 4px 0; border: 1px solid #ccc; background: #eee;">
                                    <i class="fa fa-plus" style="font-size: 11px; color: #555;"></i>
                                </button>
                            </span>
                        </div>
                    </td>
                    <td style="vertical-align: middle; text-align: right; font-weight: 600; font-size: 16px; padding: 12px 10px;">$${parseFloat(producto.precio_venta).toFixed(2)}</td>
                    <td style="vertical-align: middle; text-align: right; font-weight: bold; color: #2b957a; font-size: 17px; padding: 12px 10px;" class="total-item">$${parseFloat(producto.precio_venta).toFixed(2)}</td>
                    <td style="vertical-align: middle; text-align: center; padding: 12px 10px;">
                        <button class="btn btn-danger btn-sm btn-eliminar-item" style="border-radius: 4px; padding: 4px 8px; font-size: 13px;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#detalle-venta').append(nuevaFila);
        }
        calcularGranTotal();
    }

    function actualizarFila(fila, cantidad, precio) {
        var subtotal = cantidad * precio;
        fila.find('.total-item').text('$' + subtotal.toFixed(2));
    }

    $(document).on('change', '.cant-item', function() {
        var cantidad = parseInt($(this).val());
        var maxStock = parseInt($(this).attr('max'));
        var precio = parseFloat($(this).data('precio'));
        var fila = $('#fila-prod-' + $(this).data('id'));

        if (isNaN(cantidad) || cantidad < 1) {
            $(this).val(1);
            cantidad = 1;
        }

        if (cantidad > maxStock) {
            alert('Stock insuficiente. Solo quedan ' + maxStock + ' unidades.');
            $(this).val(maxStock);
            cantidad = maxStock;
        }

        actualizarFila(fila, cantidad, precio);
        calcularGranTotal();
    });

    $(document).on('click', '.btn-eliminar-item', function() {
        $(this).closest('tr').remove();
        
        if ($('.item-carrito').length === 0) {
            var filaVacia = `
                <tr id="fila-vacia">
                    <td colspan="5" class="text-center" style="color: #999; padding: 80px 0; font-size: 15px;">
                        <i class="fa fa-shopping-cart" style="font-size: 40px; display: block; margin-bottom: 10px; color: #ccc;"></i>
                        Esperando productos... Escanea o ingresa un código de barras.
                    </td>
                </tr>
            `;
            $('#detalle-venta').append(filaVacia);
        }
        calcularGranTotal();
    });

    $(document).on('click', '.btn-menos', function() {
        var input = $(this).closest('.input-group').find('.cant-item');
        var cantidad = parseInt(input.val()) - 1;
        if (cantidad >= 1) {
            input.val(cantidad).change();
        }
    });

    $(document).on('click', '.btn-mas', function() {
        var input = $(this).closest('.input-group').find('.cant-item');
        var cantidad = parseInt(input.val()) + 1;
        var maxStock = parseInt(input.attr('max'));
        
        if (cantidad <= maxStock) {
            input.val(cantidad).change();
        } else {
            alert('Stock insuficiente. Solo quedan ' + maxStock + ' unidades.');
        }
    });

    $(document).on('click', '.btn-danger.btn-block', function() {
        if ($('.item-carrito').length > 0) {
            if (confirm('¿Estás seguro de que deseas vaciar el carrito actual?')) {
                limpiarCarritoCompleto();
            }
        }
    });

    function limpiarCarritoCompleto() {
        $('#detalle-venta').html(`
            <tr id="fila-vacia">
                <td colspan="5" class="text-center" style="color: #999; padding: 80px 0; font-size: 15px;">
                    <i class="fa fa-shopping-cart" style="font-size: 40px; display: block; margin-bottom: 10px; color: #ccc;"></i>
                    Esperando productos... Escanea o ingresa un código de barras.
                </td>
            </tr>
        `);
        totalAPagarGlobal = 0;
        $('#total-gigante').text('$0.00');
        $('#items-count').text('0 artículos en el carrito');
        $('#codigo_barras').val('').focus();
    }

    function calcularGranTotal() {
        var granTotal = 0;
        var totalArticulos = 0;

        $('.item-carrito').each(function() {
            var cantidad = parseInt($(this).find('.cant-item').val());
            var precio = parseFloat($(this).data('precio'));
            
            granTotal += (cantidad * precio);
            totalArticulos += cantidad;
        });

        $('#total-gigante').text('$' + granTotal.toFixed(2));
        $('#items-count').text(totalArticulos + ' artículo(s) en el carrito');
    }
}); 
</script>

<!-- EL MODAL SE QUEDA AQUÍ FUERA -->
<div id="modal-cobrar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" style="margin-top: 100px;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: #2b3e50; color: #ffffff; padding: 15px;">
                <h4 class="modal-title" style="margin: 0; font-weight: bold; text-align: center;"><i class="fa fa-money"></i> REGISTRAR PAGO</h4>
            </div>
            <div class="modal-body" style="background: #fdfdfd; padding: 25px 20px;">
                
                <!-- Total a cobrar -->
                <div class="text-center" style="margin-bottom: 20px;">
                    <span style="color: #777; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Total a Cobrar</span>
                    <h2 id="modal-total-cobrar" style="margin: 5px 0 0 0; font-weight: bold; color: #333; font-size: 32px;">$0.00</h2>
                </div>

                <!-- Input Recibo -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color: #555; font-weight: bold; font-size: 13px;">Efectivo Recibido ($)</label>
                    <input type="number" id="pago-recibido" class="form-control input-lg" placeholder="0.00" min="0" step="any" style="text-align: center; font-size: 24px; font-weight: bold; color: #2b957a; height: 50px; border: 2px solid #2b957a; border-radius: 4px;">
                </div>

                <!-- Cambio a entregar -->
                <div class="text-center" style="background: #f5f7f8; padding: 15px; border-radius: 6px; border: 1px solid #e3e6e8; margin-bottom: 20px;">
                    <span style="color: #666; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Cambio a entregar</span>
                    <h3 id="modal-cambio" style="margin: 5px 0 0 0; font-weight: bold; color: #d9534f; font-size: 28px;">$0.00</h3>
                </div>

            </div>
            <!-- Footer con diseño Flexbox limpio y moderno -->
            <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #ebebeb; padding: 15px 20px; display: flex; justify-content: space-between; gap: 12px;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="flex: 1; height: 46px; font-weight: bold; font-size: 14px; border-radius: 6px; border: 1px solid #ccc; background: #ffffff; color: #555; transition: all 0.2s; margin: 0;">
                    Cancelar
                </button>
                <button type="button" id="btn-finalizar-venta" class="btn btn-success" style="flex: 1; height: 46px; font-weight: bold; font-size: 14px; border-radius: 6px; background-color: #2ecc71; border: none; color: #ffffff; transition: all 0.2s; margin: 0; display: flex; align-items: center; justify-content: center; gap: 6px;" disabled>
                    <i class="fa fa-check-circle" style="font-size: 16px;"></i> Listo
                </button>
            </div>
        </div>
    </div>
</div>