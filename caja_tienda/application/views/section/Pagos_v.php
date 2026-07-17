<?php
	//setlocale(LC_TIME, 'spanish');
    //echo ucwords(strftime("%B", strtotime("13-12-2018 14:26:05")));
    //var_dump($_SERVER);
    //echo $this->input->ip_address();
?>
<style>
.select2-results .select2-result-label{
    font-size: 11px
}
#form-agregar input,
#form-agregar input[type="date"].form-control, 
#form-agregar input[type="time"].form-control, 
#form-agregar input[type="datetime-local"].form-control, 
#form-agregar input[type="month"].form-control,
#form-agregar select.form-control{
    font-size: 11px
}
fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
    box-shadow:  0px 0px 0px 0px #000;
    background-color: #f8f9fa
}
legend.scheduler-border {
    font-size: 1.2em !important;
    font-weight: bold !important;
    text-align: left !important;
    width:auto;
    padding:0 10px;
    border-bottom:none;
}
</style>
<div id="div-prueba"></div>
<table id="datatable" class="table table-sm table-bordered" style="font-family: 'Arial'">
    <thead class="label-gris-total text-center">
        <tr>
            <th data-priority="1">Ticket</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Cobrador</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Operaciones</th>
        </tr>
    </thead>
</table>
</div>
</div>
</div>
<!-- end row -->

<!-- /.modal AGREGAR -->
<div id="modal-form-agregar" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="myBtn">×</button>
                <h4 class="modal-title col-md-2"></h4>
            </div>
            <div class="modal-body" style="font-size: 11px">
                <form id="form-agregar" data-parsley-validate novalidate role="form" autocomplete="off">
                    <!--input type="hidden" id="cliente_id" value="" name="cliente_id"-->
                    <div class="row" style="background-color: #eff3f8">
                        <div class="col-md-7 col-sm-7 col-xs-12" style="background-color: #fff">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <select class="form-control select2" id="cliente_id_padre" name="cliente_id_padre" onChange="mostrar_detalle_cliente_adherente_planes(this), mostrar_ultimo_pago(this)" required>
                                    </select>
                                    <div class="btn-group m-b-10">
                                        <button type="button" class="btn btn-default waves-effect btn-xs m-b-5">
                                            ÚLTIMO PAGO: <span class="label label-inverse" id="ultimo_pago"></span></button>
                                        <button type="button" class="btn btn-default waves-effect btn-xs m-b-5">
                                            MES/ES: <span class="label label-inverse" id="ultimo_pago_desde"></span>                                            
                                        </button>
                                        <button type="button" class="btn btn-default waves-effect btn-xs m-b-5">
                                            FECHA PAGO: <span class="label label-inverse" id="ultimo_pago_hasta"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="pago_cliente_fecha" class="control-label">Desde</label>
                                    <input type="date" class="form-control" id="pago_cliente_fecha" value="<?=date("Y-m-d")?>" name="pago_cliente_fecha" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="pago_cliente_fecha_hasta" class="control-label text-center">Hasta</label>
                                    <input type="date" class="form-control" id="pago_cliente_fecha_hasta" value="<?=date("Y-m-d")?>" name="pago_cliente_fecha_hasta" required>								
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="pago_forma_id" class="control-label">Forma de pago</label>
                                    <select class="form-control" id="pago_forma_id" name="pago_forma_id" onChange="mostrar_pago_forma(this)" required></select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 div_pago_monto_efectivo_tarjeta" style="display:none">
                                <div class="form-group div_pago_efectivo">
                                    <label for="pago_forma_efectivo_monto" class="control-label div_pago_efectivo"></label>
                                    <input type="number" class="form-control div_pago_efectivo text-center" id="pago_forma_efectivo_monto" value="" name="pago_forma_efectivo_monto">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 div_pago_monto_efectivo_tarjeta" style="display:none">
                                <div class="form-group div_pago_tarjeta">
                                    <label for="pago_forma_tarjeta_monto" class="control-label div_pago_tarjeta">Tarjeta</label>
                                    <input type="number" class="form-control div_pago_tarjeta text-center" id="pago_forma_tarjeta_monto" value="" name="pago_forma_tarjeta_monto">
                                </div>
                            </div>   
                        </div>

                        <div class="col-md-5 col-sm-5 col-xs-12" style="background-color: #eff3f8;padding: 10px 0 5px;">
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input id="generar_factura" type="checkbox" name="generar_factura" onclick="generarFactura(this.checked)">
                                        <label for="generar_factura">¿Factura?</label>
                                    </div>
                                </div>
                            </div>
                            <style>
                                input.uppercase { text-transform: uppercase; }
                                input.uppercase::placeholder  { text-transform: lowercase;font-size: 12px}                            
                            </style>
                            <div class="col-md-8 col-sm-8 col-xs-8">
                                <div class="form-group">
                                    <input type="text" class="form-control uppercase" id="cliente_ruc" name="cliente_ruc" placeholder="factura a ruc de:" disabled style="font-size: 13px;" required="true">
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <input type="text" class="form-control uppercase" id="cliente_nombre" name="cliente_nombre" placeholder="factura a nombre de:" parsley-trigger="change" data-parsley-length="[3,45]" disabled style="font-size: 13px;" required="true">
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="factura_concepto" name="factura_concepto" placeholder="factura en concepto de:" parsley-trigger="change" data-parsley-length="[3, 350]" disabled required="true">
                                </div>
                            </div>
                            <!--div style="clear:both"></div-->
                            
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <button type="submit" id="btn-guardar" onclick="enviar_datos()" class="btn btn-success waves-effect waves-light col-md-12 col-sm-12 col-xs-12" disabled="">
                                        <i class="fa fa-check-circle"></i> 
                                        Generar pago
                                    </button>
                                </div>
                            </div> 

                        </div>
                    </div>
                </form>
                    <div class="row">
                       <table id="datatableFacturaDetalle" class="table table-bordered"
                            style="font-size: 12px">
                            <thead class="label-gris-claro text-center">
                                <tr>
                                    <th data-priority="1" class="text-center" style="width:20px">ID</th>
                                    <th class="text-center">Cliente</th>
                                    <th class="text-center">Edad</th>
                                    <th class="text-center">Plan</th>
                                    <th style="width:100px" class="text-center">Monto Plan</th>
                                    <th style="width:120px" class="text-center">Monto adicional</th>
                                </tr>
                            </thead>
                            <tfoot class="label-gris-claro">
                               <tr>
                                    <th colspan="4" class="text-right"></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>                        
                    </div>
                </div>            
        </div>
    </div>
</div>
<!-- /.modal -->

<!-- /.modal VER DETALLE -->
<div id="modal-form-ver-detalle" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
			<style>
				.pleft{padding-left:0}
			</style>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 col-sm-12 col-xs-12">
                        <strong><span id="cliente"></span> </strong><span class="label label-gris-claro" id="tipo">Titular</span>
                        <br>
                        <span id="cliente_direccion"></span>
                        <br>
                        <span id="cliente_estado"></span>
                        <br>
                        <abbr title="Teléfono">T:</abbr> <span id="cliente_cel"></span>
                    </div>                    
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <strong class="col-md-6 text-left pleft">Fecha de pago: </strong><span id="pago_cliente_fecha"></span>
                        <br>
                        <strong class="col-md-6 text-left pleft">Hora de pago: </strong><span id="pago_cliente_hora"></span>
                        <br>
                        <strong class="col-md-6 text-left pleft">Forma de pago: </strong><span id="pago_forma_descripcion"></span>
                        <br>
                        <strong class="col-md-6 text-left pleft">Estado: </strong><span id="pago_cliente_estado"></span>
                        <br>
                        <strong class="col-md-6 text-left pleft">Registrado por: </strong><span id="cobrador"></span>
                    </div>
                    <div class="col-md-5 col-sm-6 col-xs-12">
                        <strong class="col-md-6 text-left pleft">Total monto plan: </strong>Gs. <span id="pago_cliente_monto_plan"></span>
                        <br>
                        <strong class="col-md-6 text-left pleft">Total monto adicional: </strong>Gs. <span id="pago_cliente_detalle_monto_adicional"></span>
                        <br>
                        <strong class="col-md-6 text-left pleft">Cuotas: </strong><span id="pago_cliente_cuotas"></span> [ <span id="pago_cliente_desde_hasta"></span> ]
						<br>
                        <hr style="margin: 0;border-bottom: 1px solid #c2c2c2;width: 85%;">
                        <strong class="col-md-6 text-left pleft"></strong>Gs. <span style="font-weight:bold" id="subtotal_pago"></span>
						<br>
                        <strong class="col-md-6 text-left pleft">Total IVA: </strong>Gs. <span id="pago_cliente_monto_iva"></span>
						<br>
                        <hr style="margin: 0;border:0;border-bottom: 1px solid seagreen;width: 85%;">
                        <strong class="col-md-6 text-left pleft">TOTAL PAGO: </strong><span style="background-color: seagreen;color: antiquewhite;padding: 4px;border-radius: 0 0 5px 5px;" id="pago_cliente_monto_total"></span>
                    </div>
                </div>
                <hr>
                
                <fieldset class="scheduler-border" id="detailFactura">
                    <legend class="scheduler-border" id="factura_nro">Detalle de la factura: </legend>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <strong class="col-md-4 text-left pleft">RUC: </strong><span id="factura_ruc"></span><br>
                        <strong class="col-md-4 text-left pleft">Razón social: </strong><span id="factura_razon_social"></span>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <strong class="col-md-3 text-left pleft">Concepto: </strong><span id="factura_concepto"></span>
                    </div>
                </fieldset>
                <div class="row">
                    <table id="datatableVerDetalle" class="table table-bordered" style="font-size: 12px">
                        <thead class="label-gris-claro text-center">
                            <tr>
                                <th data-priority="1" class="text-center" style="width:50px">ID</th>
                                <th style="width:350px !important" class="text-center">Cliente</th>
                                <!--th class="text-center">Edad</th>
                                <th class="text-center">Plan</th-->
                                <th class="text-center">Monto Plan</th>
                                <th class="text-center">Monto adicional</th>
                                <th class="text-center">SUBTOTAL</th>
                                <th class="text-center">IVA</th>
                            </tr>
                        </thead>
                        <tbody id="datatableVerDetalleBody">                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th class="text-center">G. <span id="pago_cliente_monto_plan"></th>
                                <th class="text-center">G. <span id="pago_cliente_detalle_monto_adicional"></th>
                                <th class="text-center">G. <span id="pago_cliente_detalle_subtotal"></th>
                                <th class="text-center">G. <span id="pago_cliente_detalle_iva"></th>
                            </tr>
                        </tfoot>
                    </table>                        
                </div>                
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->
<!-- AJAX -->
<script src="<?=base_url('template/assets/js/jquery.min.js')?>"></script>
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script-->
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    /**
    *
    *   Configuración Datatable
    */

    $(document).ready(function(){  
        // Tabla principal
        var table = $('#datatable').DataTable({
            //dom: '<"btn-datatable dt-buttons btn-group">Blfrtip',
            dom: '<"btn-datatable dt-buttons btn-group">Blfrtip',
            //dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'print',
                    text: 'Imprimir',
                    autoPrint: true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5 ]
                    },
                    customize: function ( win ) {
                        $(win.document.body)
                            .css( 'font-size', '10pt' )
                            .prepend(
                            '<img src="<?=base_url('template/assets/images/logo-1.png'); ?>" style="position:absolute; top:0; left:0;opacity: 0.01" />'
                        );

                        $(win.document.body).find( 'table' )
                            .addClass( 'table table-sm' )
                            .css( 'font-size', 'inherit' );
                    }
                }
            ],
            "language": {
                "decimal": ".",
                "thousands": ","
            },
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "Todos"]],
            pageLength: 10,
            pagingType: "full",
            fixedHeader: true,
            "stateSave": true,
            responsive: true,
            "processing":true,
            searchHighlight: true,
            "serverSide":true,
            "orderable":false,
            "order":[],  
            "ajax":{
                url:"<?=base_url(strtolower($this->router->class.'/datatable_datos')); ?>",  
                type:"POST"  
            },
            columnDefs: [
                { className : "text-center", targets: [0, 2, 3, 4, 5, 6]},
                { targets:['_all'], "orderable":false, },
                //{ targets : -1, data: null, defaultContent: "<button>Click!</button>"}
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 3, targets: 1 },
                { responsivePriority: 4, targets: -2 },
                { responsivePriority: 2, targets: -1 },

            ],
            "createdRow": function ( row, data, index ) {
                var esSuperadmin = <?= !empty($es_superadmin) ? 'true' : 'false' ?>;
                var botones = '<div class="btn-group">' +
                    '<a class="btn btn-default waves-effect waves-light" title="Imprimir" onclick="imprimir_ticket('+data[0]+')"><i class="fa fa-print"></i></a>';

                // Solo superadmin puede anular, y solo si no está anulado
                if (esSuperadmin && data[4] !== 'Anulado') {
                    botones += '<a class="btn btn-default waves-effect waves-light btn-eliminar" title="Anular" onclick="event.preventDefault(); event.stopPropagation(); eliminar(event, '+data[0]+')"><i class="fa fa-times"></i></a>';
                }
                botones += '</div>';

                if(data[4] == 'Pagado') {
                    $("td", row).eq(4).html("<span class='label label-success'>"+data[4]+'</span>');
                }else if(data[4] == 'Anulado') {
                    $("td", row).eq(4).html("<span class='label label-danger'>"+data[4]+'</span>');
                }else if(data[4] == 'Pendiente') {
                    $("td", row).eq(4).html("<span class='label label-warning'>"+data[4]+'</span>');
                }
                $("td", row).eq(-1).append(botones);
            }
        });
        $('#datatable tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('info') ) {
                $(this).removeClass('info');
            }
            else {
                table.$('tr.info').removeClass('info');
                $(this).addClass('info');
            }
        });
        // Botones cabecera
        var esSuperadmin = <?= !empty($es_superadmin) ? 'true' : 'false' ?>;
        var cabeceraHtml = '<a id="refresh" class="btn btn-default waves-effect waves-light"><i class="fa fa-refresh"></i></a>' +
                           '<a id="btnGenerarReporte" class="btn-agregar">Generar reporte <i class="fa fa-bar-chart"></i></a>';
        if (esSuperadmin) {
            cabeceraHtml += '<a id="btnLimpiarTransacciones" class="btn btn-danger waves-effect waves-light m-l-10" style="font-weight: bold; border-radius: 4px; padding: 6px 12px; height: 34px;"><i class="fa fa-trash-o m-r-5"></i> Limpiar</a>';
        }
        $("div.btn-datatable").html(cabeceraHtml);
        
        //  Acciones de los botones
        $('#btnGenerarReporte').click( function () { abrirModalReporte(); } );
        $('#refresh').click( function (){$('#datatable').dataTable().fnClearTable();});
        $(document).on('click', '#btnLimpiarTransacciones', function(e) {
            e.preventDefault();
            e.stopPropagation();
            limpiarTransacciones(e);
        });
    });

    function abrirModalReporte() {
        $('#reporte-tipo').val('semana');
        $('#reporte-rango-fechas').hide();
        $('#modal-generar-reporte').modal('show');
    }

    $(document).on('change', '#reporte-tipo', function() {
        if ($(this).val() === 'rango') {
            $('#reporte-rango-fechas').slideDown(150);
        } else {
            $('#reporte-rango-fechas').slideUp(150);
        }
    });

    $(document).on('click', '#btn-ver-reporte', function() {
        var tipo = $('#reporte-tipo').val();
        var data = { tipo: tipo };

        if (tipo === 'rango') {
            data.fecha_desde = $('#reporte-fecha-desde').val();
            data.fecha_hasta = $('#reporte-fecha-hasta').val();
            if (!data.fecha_desde || !data.fecha_hasta) {
                alert('Seleccioná fecha desde y fecha hasta.');
                return;
            }
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generando...');

        $.ajax({
            url: "<?=base_url(strtolower($this->router->class.'/generar_reporte')); ?>",
            type: "POST",
            dataType: "JSON",
            data: data,
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Ver reporte');
                if (resp.status !== 'success') {
                    alert(resp.message || 'No se pudo generar el reporte.');
                    return;
                }

                $('#modal-generar-reporte').modal('hide');
                mostrarResultadoReporte(resp);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Ver reporte');
                alert('Error al generar el reporte.');
                console.error(xhr.responseText);
            }
        });
    });

    function mostrarResultadoReporte(resp) {
        $('#reporte-periodo-label').text(resp.periodo.etiqueta);
        $('#reporte-total-ventas').text('$' + parseFloat(resp.total_ventas).toFixed(2));
        $('#reporte-total-productos').text(resp.total_productos);
        $('#reporte-total-tickets').text(resp.total_tickets);

        var html = '';
        if (!resp.desglose || resp.desglose.length === 0) {
            html = '<tr><td colspan="4" class="text-center" style="padding: 30px; color: #888;">No hay productos vendidos en este período.<br><small>Las ventas nuevas ya guardan el desglose automáticamente.</small></td></tr>';
        } else {
            resp.desglose.forEach(function(item) {
                html += '<tr>' +
                    '<td style="font-weight: 600;">' + (item.producto_nombre || '') + '</td>' +
                    '<td class="text-center">' + parseInt(item.cantidad, 10) + '</td>' +
                    '<td class="text-right">$' + parseFloat(item.precio_unitario).toFixed(2) + '</td>' +
                    '<td class="text-right" style="font-weight: bold; color: #2b957a;">$' + parseFloat(item.subtotal).toFixed(2) + '</td>' +
                    '</tr>';
            });
        }
        $('#reporte-desglose-body').html(html);
        $('#modal-resultado-reporte').modal('show');
    }    

    /**
    *   
    *   Agregar, editar, actualizar, eliminar
    */
    function agregar() {
        save_method = 'agregar';
        $('#modal-form-agregar').modal('show'); // mostrar bootstrap modal
        $('.modal-title').text('Nuevo pago');
        //$('#form-agregar')[0].empty();        
        //if ($("#cliente_id_padre" ).val()){$("#btn-guardar").prop("disabled", false);}else{$("#btn-guardar").prop("disabled", true);}
        cliente_titular(0);
        mostrar_forma_pago();
        //
        $("#cliente_id_padre").select2("val", ""); // Resetear select2
        $("#btn-guardar").prop("disabled", true); // Se bloquea botón Generar ticket al abrir form
        mostrar_ultimo_pago(0); // Se setea info último pago
        // Inicializar a 0 datataable
        $('#datatableFacturaDetalle').dataTable().empty(); // Se vacía datatable
        $('#datatableFacturaDetalle_wrapper').hide(); // Se oculta datatable wrap
        $('#form-agregar')[0].reset();
        //console.log('abrir modal agregar');
        //$('#form-agregar').reset();
        $(".div_pago_monto_efectivo_tarjeta").css('display', 'none');
        $('#pago_forma_efectivo_monto, #pago_forma_tarjeta_monto').val('');
    }

    function cliente_titular(id){
        //  Inicializar select2
        //$("#cliente_id_padre").select2();
        $.ajax({
            url: "<?=base_url(strtolower($this->router->class.'/cliente_titular/')); ?>" + id,
            type:  'POST',
            beforeSend: function () {
                $("#cliente_id_padre").html("Procesando, espere por favor...");
            },
            success:  function (response) {
                $("#cliente_id_padre").html(response);
                //alert($("#cliente_id_padre").val());
                //$("#cliente_id_padre").select2();
            }
        });
    }
    function mostrar_forma_pago(){
        $.ajax({
            url: "<?=base_url(strtolower($this->router->class.'/mostrar_forma_pago')); ?>",
            type:  'POST',
            beforeSend: function () {
                $("#pago_forma_id").html("Procesando, espere por favor...");
            },
            success:  function (response) {
                $("#pago_forma_id").html(response);
            }
        });
    }

    function ver_detalle(id) {
        $('legend#factura_nro').empty();
        $.ajax({
            url: "<?=base_url(strtolower($this->router->class.'/ver_detalle/')); ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('span#cliente').html(data.cliente);
                $('span#cliente_direccion').html(data.cliente_direccion);
                $('span#cliente_estado').html('Usuario ' + data.cliente_estado);
                $('span#cliente_cel').html(data.cliente_cel);                
                $('span#pago_cliente_fecha').html(data.pago_cliente_fecha);
                $('span#pago_cliente_hora').html(data.pago_cliente_hora);
                $('span#pago_forma_descripcion').html(data.pago_forma_alias);
                $('span#pago_cliente_estado').html(data.pago_cliente_estado);
                $('span#pago_cliente_monto_plan').html(data.pago_cliente_monto_plan);
                $('span#pago_cliente_detalle_monto_adicional').html(data.pago_cliente_detalle_monto_adicional);
                $('span#pago_cliente_detalle_iva').html(data.pago_cliente_detalle_iva);
                $('span#pago_cliente_detalle_subtotal').html(data.pago_cliente_detalle_subtotal);
                $('span#total_pago').html(data.total_pago);
                $('span#pago_cliente_cuotas').html(data.pago_cliente_cuotas);
                $('span#subtotal_pago').html(data.subtotal_pago);
                $('span#pago_cliente_monto_iva').html(data.pago_cliente_monto_iva);
                $('span#pago_cliente_monto_total').html("Gs. " + data.pago_cliente_monto_total);
                $('span#pago_cliente_desde_hasta').html(data.pago_cliente_desde_hasta);
                $('span#cobrador').html(data.cobrador);
                $('legend#factura_nro').append("Detalle de la factura: " + data.factura_nro);
                $('span#factura_ruc').html(data.factura_ruc);
                $('span#factura_razon_social').html(data.factura_razon_social);
                $('span#factura_concepto').html(data.factura_concepto);
                console.log(data.factura_nro);
                if (data.factura_nro === null)
                    $('#detailFactura').hide();
                    else
                        $('#detailFactura').show();
                    

                $('#modal-form-ver-detalle').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Detalles del pago n° ' + id); // Set title to Bootstrap modal title
                
                //planes(data.cliente_fecha_nacimiento, data.cliente_id);
                //planes_clientes_fecha_ingreso(id, 0)

                datatableVerDetalle(id);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error al obtener datos de ajax');
            }
        });
        //cliente_titular(id);
    }
    
    function datatableVerDetalle(pago_cliente_id){
        $.ajax({
            url: "<?=base_url(strtolower($this->router->class.'/datatableVerDetalle/')); ?>" + pago_cliente_id,
            //data: {},
            type:  'POST',
            dataType: "JSON",
            beforeSend: function () {
                $("#datatableVerDetalleBody").append("<tr><td class='text-center' colspan='6'>Procesando, espere por favor...</td></tr>");
            },
            success:  function (response) {
                $("#datatableVerDetalleBody").empty(); // vacio la tabla y vuelvo a recargarla
                for (  i = 0 ; i < response.data.length; i++){ //cuenta la cantidad de registros
                    var nuevafila= "<tr><td class='text-center'>" +
                    response.data[i].cliente_id + "</td><td>" +
                    response.data[i].cliente + "</td><td class='text-center'>" +
                    //response.data[i].edad + "</td><td>" +
                    //response.data[i].plan + "</td><td class='text-center'>" +
                    response.data[i].pago_cliente_detalle_monto_plan + "</td><td class='text-center'>" +
                    response.data[i].pago_cliente_detalle_monto_adicional + "</td><td class='text-center'>" +
                    response.data[i].pago_cliente_detalle_subtotal + "</td><td class='text-center'>" + 
                    response.data[i].pago_cliente_detalle_iva + "</td></tr>"

                    $("#datatableVerDetalleBody").append(nuevafila).fadeIn();
                }
            }
        });
    }
    
    function guardar() {
        var table = $('#datatableFacturaDetalle').DataTable();
        //  Serializar 2 objetos
        var data = $('#datatableFacturaDetalle input, #form-agregar').serialize();
        //alert("Los siguientes datos se enviarán al servidor: \n\n"+ data);
        //return false;
        // Se vacía la tabla
        //$('#datatableFacturaDetalle').dataTable().empty();
        mostrar_ultimo_pago(0);

        var url;
        url = "<?=base_url(strtolower($this->router->class.'/agregar')); ?>";
        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "JSON",
            success: function(data) {
                //if success close modal and reload ajax table
                $('#modal-form-agregar').modal('hide');
                $('#form-agregar')[0].reset();
                $('#datatable').dataTable().fnDraw('page'); // Recargar en la misma página
                
                notificacion(data.tipo, data.message, "toast-top-right");
            },
            error: function(jqXHR, textStatus, errorThrown, data) {
                notificacion("error", data.message, "toast-top-right");
            }
        });
    }
    $("#myBtn").click(function(){
        //var table = $('#datatableFacturaDetalle').DataTable();
        // Se vacía la tabla
        //$('#datatableFacturaDetalle').dataTable().empty();
    });

    function enviar_datos() {
        $("#form-agregar").submit(function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            //console.log("sending...");
            $("#btn-guardar").prop("disabled", true); // Se bloquea botón Generar ticket al enviar form
            guardar();            
          });
    }

    function eliminar(e, id) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        <?php if (empty($es_superadmin)): ?>
        alert('Solo el usuario superadmin puede anular pagos.');
        return;
        <?php endif; ?>

        setTimeout(function() {
            swal({
                title: "¿Anular este pago?",
                text: "Esta acción anulará el ticket n° " + id + ". Ingresa tu contraseña de administrador para confirmar:",
                type: "input",
                inputType: "password",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Anular pago",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false,
                allowOutsideClick: false,
                animation: "slide-from-top",
                inputPlaceholder: "Escribe tu contraseña aquí"
            }, function (inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Debes ingresar tu contraseña.");
                    return false;
                }

                $.ajax({
                    url: "<?=base_url(strtolower($this->router->class.'/anular_ticket/')); ?>" + id,
                    type: "POST",
                    dataType: "JSON",
                    data: { password: inputValue },
                    success: function(data) {
                        if (data.status === false) {
                            swal.showInputError(data.message || "Contraseña incorrecta.");
                            return false;
                        }
                        $('#datatable').dataTable().fnDraw('page');
                        swal.close();
                        notificacion("success", "Ticket n° " + id + " anulado.", "toast-bottom-right");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        swal.showInputError("Ocurrió un error al procesar la solicitud.");
                    }
                });
            });
        }, 50);
    }

    function limpiarTransacciones(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        setTimeout(function() {
            swal({
                title: "¿Limpiar todas las transacciones?",
                text: "Esta acción eliminará de forma permanente todas las transacciones registradas. Ingresa tu contraseña de administrador para confirmar:",
                type: "input",
                inputType: "password",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Limpiar todo",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false,
                allowOutsideClick: false,
                animation: "slide-from-top",
                inputPlaceholder: "Escribe tu contraseña aquí"
            }, function (inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Debes ingresar tu contraseña.");
                    return false;
                }

                $.ajax({
                    url: "<?=base_url(strtolower($this->router->class.'/limpiar_transacciones')); ?>",
                    type: "POST",
                    dataType: "JSON",
                    data: { password: inputValue },
                    success: function(data) {
                        if (data.status === false) {
                            swal.showInputError(data.message || "Contraseña incorrecta.");
                            return false;
                        }
                        $('#datatable').dataTable().fnClearTable();
                        swal.close();
                        notificacion("success", "Todas las transacciones han sido eliminadas.", "toast-bottom-right");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        swal.showInputError("Ocurrió un error al procesar la solicitud.");
                    }
                });
            });
        }, 50);
    }

    // Cambiar el select de Tipo de cliente
    function mostrar_detalle_cliente_adherente_planes(sel){
        //alert(sel.value);
        $('#datatableFacturaDetalle').DataTable().destroy();
        enviar_cliente_id(sel.value);
        $("#btn-guardar").prop("disabled", false);
        //
        $("#generar_factura").prop("checked", true);
        generarFactura(true);
       
    }
	function mostrar_ultimo_pago(sel){
        if (sel.value === undefined ) return
		$.ajax({
            url: "<?=base_url(strtolower($this->router->class.'/mostrar_ultimo_pago/')); ?>" + sel.value,
            type:  'GET',
            dataType: "JSON",
            beforeSend: function () {
                $("#mostrar_ultimo_pago").html("Procesando datos del último pago, espere por favor...");
            },
            success:  function (response) {
                if (response != null){
                    $('span#ultimo_pago').html("Gs. " + response.pago_cliente_monto_total);
                    $('span#ultimo_pago_desde').html(response.pago_cliente_desde_hasta);
                    $('span#ultimo_pago_hasta').html(response.pago_cliente_dateinsert);
                }else{
                    $('span#ultimo_pago').html("");
                    $('span#ultimo_pago_desde').html("");
                    $('span#ultimo_pago_hasta').html("");
                }
            }
        });
    }
    
    function mostrar_pago_forma(sel){
        // Se setea los campos por cada cambio
        $('#pago_forma_efectivo_monto, #pago_forma_tarjeta_monto').val('');
        //console.log(sel.value);
        if (sel.value == 4){
            $(".div_pago_monto_efectivo_tarjeta").css('display', 'block');
            $("label.div_pago_efectivo").text('Efectivo');
            $("label.div_pago_tarjeta").text('Tarjeta Débito');
        }else if (sel.value == 5){
            $(".div_pago_monto_efectivo_tarjeta").css('display', 'block');
            $("label.div_pago_efectivo").text('Efectivo');
            $("label.div_pago_tarjeta").text('Tarjeta Crédito');
        }else if (sel.value == 6){
            $(".div_pago_monto_efectivo_tarjeta").css('display', 'block');
            $("label.div_pago_efectivo").text('Tarjeta Débito');
            $("label.div_pago_tarjeta").text('Tarjeta Crédito');
        }else if ((sel.value == 1) || (sel.value == 2) || (sel.value == 7)){
            $(".div_pago_monto_efectivo_tarjeta").css('display', 'none');
        }
    }
    // Datatable DETALLES - dataTables_info
    function enviar_cliente_id(cliente_id){
        $(document).ready(function(){  
            var table = $('#datatableFacturaDetalle').DataTable({
            dom: "<'row'<'col-sm-12't>><'clear'>" +
            "<'row'<'col-sm-4 col-xs-6'i><'col-sm-4 col-xs-6'p><'col-sm-4 col-xs-12'f>>",
            language: {
                "decimal": ",",
                "thousands": ".",
                "info": "Página: _PAGE_ / Adherentes: _MAX_",
            },
            paging: true,
            pagingType: "simple",                
            lengthChange: false,
            info: true,
            pageLength: 20,
            //autoWidth: true,
            responsive: true,
            processing:true,            
            searchHighlight: true,
            serverSide:true,
            ajax:{
                url:"<?=base_url(strtolower($this->router->class.'/datatableFacturaDetalle')); ?>/" + cliente_id,  
                type:"POST"
            },
            columnDefs: [
                { className : "text-center", targets: [0, 2, 3, 4, 5]},
                { targets:['_all'], "orderable":false, },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 3, targets: 1 },
                { responsivePriority: 4, targets: -2 },
                { responsivePriority: 2, targets: -1 }
            ],
            columns: [
                { width: "10px" },
                { width: "230px" },
                { width: "60px" },
                { width: "60px" },
                { width: "80px" },
                { width: "80px" },
            ],
            select: {
                style:    'info',
                selector: 'td:first-child',
                blurable: true
            },
            "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    // converting to interger to find total
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    // computing column Total of the complete result 
                    var monTotal = api
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer by showing the total with the reference of the column index 
                    $(api.column(3).footer()).html('Subtotal');
                    $(api.column(4).footer()).html(monTotal);
                }
            });
       });
       $('#datatableFacturaDetalle_wrapper div.dataTables_paginate').css('margin-top', '-30px');
       $('#datatableFacturaDetalle_wrapper div.dataTables_paginate').css('text-align', 'center');
       $('#datatableFacturaDetalle_wrapper .dataTables_info').removeClass('dataTables_info col-sm-3');
       $('#datatableFacturaDetalle').removeClass('dataTable');
       
    }

    function imprimir_ticket(ticket_id){
        var url = "<?=base_url(strtolower($this->router->class.'/ticket')); ?>/" + ticket_id;
        window.open(url, 'ticket_' + ticket_id, 'width=420,height=700,scrollbars=yes');
    }  
    
    function generarFactura(value)
	{        
        let cliente = $("#cliente_id_padre").val();

        $("#cliente_ruc").prop("disabled", !value);
        $("#cliente_nombre").prop("disabled", !value);
        $("#factura_concepto").prop("disabled", !value);
        
        $('[name="cliente_ruc"]').val("");
        $('[name="cliente_nombre"]').val("");
        $('[name="factura_concepto"]').val("");

        $("form").parsley().reset();
        if (!value) {
            $('[name="cliente_ruc"]').removeAttr('required');
            $('[name="cliente_nombre"]').removeAttr('required');
            $('[name="factura_concepto"]').removeAttr('required');            
            return;
        }else{
            $('[name="cliente_ruc"]').attr('required', 'required');
            $('[name="cliente_nombre"]').attr('required', 'required');
            $('[name="factura_concepto"]').attr('required', 'required');
        }

        $.ajax({
            url: "<?=base_url('clientes/getById/'); ?>" + cliente,
            type: "GET",
            dataType: "JSON",
            beforeSend: function () {
                //console.log("Procesando..")
            },
            success:  function (response) {
                let ruc = response.cliente_ruc ?? response.cliente_ci;
                let ruc_dv = response.cliente_ruc_dv ?? '??';
                $('[name="cliente_nombre"]').val(response.cliente_nombre.toUpperCase().concat(" ").concat(response.cliente_apellido.toUpperCase()));
                $('[name="cliente_ruc"]').val(ruc.concat('-').concat(ruc_dv));
                
            }
        });
        
    }

</script>

<!-- Modal: elegir período del reporte -->
<div id="modal-generar-reporte" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" style="margin-top: 80px;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background: #2b3e50; color: #ffffff; padding: 15px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.8;">×</button>
                <h4 class="modal-title" style="margin: 0; font-weight: bold;"><i class="fa fa-bar-chart"></i> Generar reporte</h4>
            </div>
            <div class="modal-body" style="padding: 25px 20px; background: #fdfdfd;">
                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="color: #555; font-weight: bold;">Período</label>
                    <select id="reporte-tipo" class="form-control" style="height: 42px; font-size: 15px;">
                        <option value="semana">Por semana (semana actual)</option>
                        <option value="mes">Por mes (mes actual)</option>
                        <option value="rango">De una fecha a otra</option>
                    </select>
                </div>
                <div id="reporte-rango-fechas" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="color: #555; font-weight: bold;">Desde</label>
                                <input type="date" id="reporte-fecha-desde" class="form-control" value="<?=date('Y-m-d')?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="color: #555; font-weight: bold;">Hasta</label>
                                <input type="date" id="reporte-fecha-hasta" class="form-control" value="<?=date('Y-m-d')?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #ebebeb; padding: 15px 20px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btn-ver-reporte" class="btn btn-primary">
                    <i class="fa fa-search"></i> Ver reporte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: resultado del reporte -->
<div id="modal-resultado-reporte" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" style="margin-top: 50px; width: 90%; max-width: 1000px;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background: #2b3e50; color: #ffffff; padding: 15px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.8;">×</button>
                <h4 class="modal-title" style="margin: 0; font-weight: bold;">
                    <i class="fa fa-file-text-o"></i> Reporte de ventas
                    <small id="reporte-periodo-label" style="color: #aab2bd; margin-left: 8px;"></small>
                </h4>
            </div>
            <div class="modal-body" style="padding: 20px; background: #f5f7f8;">
                <div class="row">
                    <!-- Izquierda: desglose -->
                    <div class="col-md-8">
                        <div style="background: #fff; border-radius: 6px; border: 1px solid #e0e0e0; padding: 15px; min-height: 320px;">
                            <h5 style="margin: 0 0 15px 0; font-weight: 700; color: #2b3e50; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                Desglose de productos vendidos
                            </h5>
                            <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
                                <table class="table table-striped table-hover m-0" style="font-size: 13px;">
                                    <thead style="background: #3b3e47; color: #fff;">
                                        <tr>
                                            <th style="color: #fff;">Producto</th>
                                            <th style="color: #fff; text-align: center;">Cant.</th>
                                            <th style="color: #fff; text-align: right;">P. unit.</th>
                                            <th style="color: #fff; text-align: right;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reporte-desglose-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Derecha: totales -->
                    <div class="col-md-4">
                        <div style="background: #2b3e50; border-radius: 6px; padding: 25px 20px; color: #fff; text-align: center; margin-bottom: 15px;">
                            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #aab2bd; font-weight: 600;">Total de ventas</div>
                            <div id="reporte-total-ventas" style="font-size: 36px; font-weight: bold; color: #2ecc71; font-family: monospace; margin-top: 8px;">$0.00</div>
                        </div>
                        <div style="background: #fff; border-radius: 6px; border: 1px solid #e0e0e0; padding: 20px; text-align: center; margin-bottom: 15px;">
                            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #888; font-weight: 600;">Total de productos</div>
                            <div id="reporte-total-productos" style="font-size: 32px; font-weight: bold; color: #2b3e50; margin-top: 8px;">0</div>
                            <div style="color: #999; font-size: 12px; margin-top: 4px;">unidades vendidas</div>
                        </div>
                        <div style="background: #fff; border-radius: 6px; border: 1px solid #e0e0e0; padding: 20px; text-align: center;">
                            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #888; font-weight: 600;">Tickets</div>
                            <div id="reporte-total-tickets" style="font-size: 28px; font-weight: bold; color: #2b3e50; margin-top: 8px;">0</div>
                            <div style="color: #999; font-size: 12px; margin-top: 4px;">transacciones pagadas</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #ebebeb; padding: 15px 20px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
