<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *  Class Pagos
 *
 *  @package application/controllers/pagos
 *  @author  seto  
 */
class Pagos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clientes_m');
        $this->load->model('pagos_m');
        $this->load->model('auxiliar_m');
        $this->load->model('usuarios_m');
        $this->load->model('factura_m');
        $this->load->model('parametros_m');
        date_default_timezone_set("America/Asuncion");
    }

    public function index()
    {
        $this->load->model('menu_m');
        
        $data['breadcrumb']  = $this->auxiliar_m->breadcrumb($this->router->class);
        $data['tbl_cliente_tipo']  = $this->auxiliar_m->traer_tabla(NULL, 'clientes_tipo');
        $data['es_superadmin'] = (strtolower(trim((string) $this->session->userdata('usuario_user'))) === 'superadmin');

        $this->load->view('html/Head');
        $this->load->view('html/Nav', array(
            'model_menu'=> $this->load->model('menu_m'),
            'menus'     => $this->menu_m->menu()
        ));
        $this->load->view('html/Breadcrumb_v', $data);
        $this->load->view('section/Pagos_v', $data);        
        $this->load->view('html/Footer');
    }

    /**
     * Datatable principal
     */
    function datatable_datos()
    {
        $fetch_data = $this->pagos_m->make_datatables();
        
        $data = array();
        foreach ($fetch_data as $row) {
            $sub_array   = array();
            $sub_array[] = $row->pago_cliente_id;
            $sub_array[] = $row->cliente;
            $sub_array[] = $row->pago_cliente_fecha;
            $sub_array[] = $row->cobrador;
            $sub_array[] = $row->pago_cliente_estado;
            $sub_array[] = $row->pago_cliente_monto_total;
            $sub_array[] = '';
            $data[]      = $sub_array;
        }
        $output = array(
            "draw"              => intval($this->input->post("draw")),
            "recordsTotal"      => $this->pagos_m->get_all_data(),
            "recordsFiltered"   => $this->pagos_m->get_filtered_data(),
            "data"              => $data
        );
        
        $headers = 'Content-type:application/json';
        header($headers);
        echo json_encode($output, JSON_PRETTY_PRINT);
    } 

    /**
     * Datatable para crear ticket o registro
     */
    function datatableFacturaDetalle($cliente_id)
    {
        $fetch_data = $this->pagos_m->make_datatableFacturaDetalle($cliente_id);

        $data = array();
        foreach ($fetch_data as $row) {
            $i = 1;
            $sub_array   = array();
            $sub_array[] = '<input readonly="" tabindex="-1" name="cliente_id[]" value="'.$row->cliente_id.'" type="text" class="text-center form-control input-format-1" style="width:76px">';
            $sub_array[] = $row->cliente;
            $sub_array[] = $row->edad;
            $sub_array[] = $row->plan;
            $sub_array[] = $row->plan_monto;
            $sub_array[] = '<input id="" tabindex="'.$i++.'" name="pago_cliente_detalle_monto_adicional[]" value="0" type="text" class="text-center form-control" style="width:100%;border: none;border-bottom: 1px solid #c2c2c2;border-radius: 0;padding: 0;margin: 0;">';
            $data[]      = $sub_array;            
        }
        $output = array(
            "draw"              => intval($this->input->post("draw")),
            "recordsTotal"      => $this->pagos_m->get_all_data_datatableFacturaDetalle($cliente_id),
            "recordsFiltered"   => $this->pagos_m->get_filtered_data_datatableFacturaDetalle($cliente_id),
            "data"              => $data
        );
        
        $headers = 'Content-type:application/json';
        header($headers);
        echo json_encode($output, JSON_PRETTY_PRINT);        
    } 

    /**
     * Añadir, Editar / Actualizar, Eliminar
     */
    public function agregar()
    {
        $datetime1 = new DateTime($this->input->post('pago_cliente_fecha'));
        $datetime2 = new DateTime($this->input->post('pago_cliente_fecha_hasta'));
        $interval = $datetime1->diff($datetime2);
        $meses_cuotas = ((( $interval->y * 12 ) + $interval->m) + 1);

        $userObj = $this->usuarios_m->obtener_por_id($this->session->userdata('usuario_id'));
        $userSucursal = $userObj ? $userObj->sucursal_id : null;

        if (!$userSucursal || $userSucursal <= 0) {
            echo json_encode(array(
                "status"    => TRUE,
                "code"      => 404,
                "tipo"      => "error",
                "message"   => "El usuario no tiene definido una sucursal."
            ));
            return;
        }
        
        if ($this->factura_m->getBySucursal($userSucursal) == null) {
            echo json_encode(array(
                "status"    => TRUE,
                "code"      => 404,
                "tipo"      => "error",
                "message"   => "No se registró ninguna factura."
            ));
            return;
        }

        $facturaObj = $this->factura_m->getBySucursal($userSucursal);
        $facturaNroActual = explode('-', $facturaObj->nro_actual)[2];
        $facturaNroHasta = explode('-', $facturaObj->nro_hasta)[2];
        
        if (!$this->currentNumberIsLessThanOrEqual($facturaNroActual, $facturaNroHasta)){
            echo json_encode(array(
                "status"    => TRUE,
                "code"      => 409,
                "tipo"      => "warning",
                "message"   => "El número de factura generado no puede ser mayor al número final de factura. Comuniquese con su Administrador."
            ));
            return;
        }

        // Insertar la cabecera
        $data = array(
            'cliente_id'                => $this->input->post('cliente_id')['0'],
            'pago_cliente_fecha'        => $this->input->post('pago_cliente_fecha')." ".date("H:i:s"),
            'pago_cliente_fecha_hasta'  => $this->input->post('pago_cliente_fecha_hasta')." ".date("H:i:s"),
            'pago_forma_id'             => $this->input->post('pago_forma_id'),
            'pago_forma_efectivo_monto' => $this->input->post('pago_forma_efectivo_monto'),
            'pago_forma_tarjeta_monto'  => $this->input->post('pago_forma_tarjeta_monto'),
            'pago_cliente_cuotas'       => $meses_cuotas,
            'pago_cliente_monto_plan'   => $this->input->post('pago_cliente_detalle_monto_adicional')['0'],
            'pago_cliente_estado'       => 'Pendiente',
            'usuario_id'                => $this->session->userdata('usuario_id'),
            'sucursal_id'               => $userSucursal,
        );

        $generarFactura = ($this->input->post('generar_factura') != null) ? true : false;
        $exito = $this->pagos_m->agregar_cabecera($data);

        // Insertar detalle
        $countsize = count($this->input->post('cliente_id'));
        if ($exito){
            for ($i = 0; $i < $countsize; $i++) {
                $cliente_id = $this->input->post('cliente_id')[$i];
                $plan_id = $this->pagos_m->obtener_planes_cliente($cliente_id, 'plan_id');
                $monto_plan_detail = $this->pagos_m->obtener_planes_cliente_costo($plan_id, $cliente_id);
                $monto_adicional_detail = $this->input->post('pago_cliente_detalle_monto_adicional')[$i];
                
                $dataDetalle = array(
                    'pago_cliente_id'                      => $exito,
                    'planes_clientes_id'                   => $this->pagos_m->obtener_planes_cliente($cliente_id, 'planes_clientes_id'),
                    'pago_cliente_detalle_monto_plan'      => $monto_plan_detail,
                    'pago_cliente_detalle_subtotal'        => $monto_plan_detail * $meses_cuotas,
                    'pago_cliente_detalle_monto_adicional' => $monto_adicional_detail,
                    'pago_cliente_detalle_iva'             => ($monto_plan_detail + $monto_adicional_detail) * $this->parametros_m->getbyCod("iva10")->param_valor
                );
                $this->pagos_m->agregar_detalle($dataDetalle);
            }

            if ($exito){
                $monto_total = $this->pagos_m->total_monto_plan($exito);
                $monto_iva = $this->pagos_m->total_monto_iva($exito);
                $monto_adicional = $this->pagos_m->total_monto_adicional($exito);
                
                $dataCabeceraUpdate = array(
                    'pago_cliente_estado'      => 'Pagado',
                    'pago_cliente_monto_plan'  => $monto_total,
                    'pago_cliente_monto_iva'   => $monto_iva,
                    'pago_cliente_monto_total' => (($monto_total * $meses_cuotas) + $monto_adicional + $monto_iva), 
                    'factura_id'               => $facturaObj->factura_id,
                    'factura_nro'              => $this->generateNextNumberFactura($facturaObj->nro_actual),
                    'factura_ruc'              => $this->input->post('cliente_ruc'),
                    'factura_razon_social'     => $generarFactura ? $this->input->post('cliente_nombre') : 'SIN NOMBRE',
                    'factura_concepto'         => $this->input->post('factura_concepto'),
                );
                
                $exito = $this->pagos_m->actualizar_montos_cabecera(array('pago_cliente_id' => $exito), $dataCabeceraUpdate);
                
                if ($exito){
                    $dataFacturaUpdate = array(
                        'nro_actual' => $this->generateNextNumberFactura($facturaObj->nro_actual),
                    );
                    $this->factura_m->updateNroActual(array('factura_id' => $facturaObj->factura_id), $dataFacturaUpdate);
                }
            }
        }

        echo json_encode(array(
            "status"    => TRUE,
            "code"      => 200,
            "tipo"      => "success",
            "message"   => "Pago generado exitosamente."
        ));
    }

    public function getId()
    {
        $data = $this->usuarios_m->obtener_por_id($this->session->userdata('usuario_id'))->sucursal_id;
        echo json_encode($data);
    }

    public function generateNextNumberFactura($currentNroFactura){ 
        $nroFactura = ltrim(explode('-', $currentNroFactura)[2], "0");
        $number = $nroFactura + 1;
        $length = strlen(explode('-', $currentNroFactura)[2]);
        $nextNumber = substr(str_repeat(0, $length).$number, - $length);
        $nextNumber = explode('-', $currentNroFactura)[0]."-".explode('-', $currentNroFactura)[1]."-".$nextNumber;
        return $nextNumber;
    }

    public function currentNumberIsLessThanOrEqual($currentNroFactura, $nroFacturaHasta){
        $nroFactura = ltrim($currentNroFactura, "0") + 1;
        $nroFacturaHasta = ltrim($nroFacturaHasta, "0");
        return ($nroFactura <= $nroFacturaHasta);
    }
    
    public function ver_detalle($pago_cliente_id)
    {
        $data = $this->pagos_m->obtener_por_id($pago_cliente_id);
        echo json_encode($data);
    }

    public function datatableVerDetalle($pago_cliente_id)
    {
        $data['data'] = $this->pagos_m->datatableVerDetalle($pago_cliente_id);
        echo json_encode($data);
    }

    public function anular_ticket($id)
    {
        if (strtolower(trim((string) $this->session->userdata('usuario_user'))) !== 'superadmin') {
            echo json_encode(array(
                "status" => FALSE,
                "message" => "Solo el usuario superadmin puede anular pagos."
            ));
            return;
        }

        $password = $this->input->post('password');
        if (!$password) {
            echo json_encode(array(
                "status" => FALSE,
                "message" => "Se requiere la contraseña para anular esta transacción."
            ));
            return;
        }

        $usuario_id = $this->session->userdata('usuario_id');
        $usuario = $this->usuarios_m->obtener_por_id($usuario_id);

        if (!$usuario || $usuario->usuario_pass !== md5($password)) {
            echo json_encode(array(
                "status" => FALSE,
                "message" => "Contraseña incorrecta."
            ));
            return;
        }

        $this->pagos_m->anular_ticket(array('pago_cliente_id' => $id), array('pago_cliente_estado' => 'Anulado'));
        echo json_encode(array("status" => TRUE));
    }

    public function limpiar_transacciones()
    {
        if (strtolower(trim((string) $this->session->userdata('usuario_user'))) !== 'superadmin') {
            echo json_encode(array(
                "status" => FALSE,
                "message" => "Solo el usuario superadmin puede limpiar las transacciones."
            ));
            return;
        }

        $password = $this->input->post('password');
        if (!$password) {
            echo json_encode(array(
                "status" => FALSE,
                "message" => "Se requiere la contraseña para limpiar las transacciones."
            ));
            return;
        }

        $usuario_id = $this->session->userdata('usuario_id');
        $usuario = $this->usuarios_m->obtener_por_id($usuario_id);

        if (!$usuario || $usuario->usuario_pass !== md5($password)) {
            echo json_encode(array(
                "status" => FALSE,
                "message" => "Contraseña incorrecta."
            ));
            return;
        }

        // Limpiar todas las transacciones de forma segura
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->empty_table('venta_detalle');
        $this->db->empty_table('pago_cliente_detalle');
        $this->db->empty_table('pago_cliente');
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");

        echo json_encode(array("status" => TRUE));
    }

    /**
     * Imprimir una sola transacción (ticket POS).
     */
    public function ticket($pago_cliente_id)
    {
        $pago_cliente_id = intval($pago_cliente_id);

        $this->db->select("
            t1.pago_cliente_id,
            t1.pago_cliente_fecha,
            t1.pago_cliente_monto_total,
            t1.pago_cliente_estado,
            t1.pago_forma_efectivo_monto,
            COALESCE(NULLIF(TRIM(t3.usuario_user), ''), CONCAT(IFNULL(t3.usuario_nombre,''),' ',IFNULL(t3.usuario_apellido,''))) AS cobrador,
            CONCAT(IFNULL(t2.cliente_nombre,''),' ',IFNULL(t2.cliente_apellido,'')) AS cliente,
            t4.pago_forma_descripcion
        ", false);
        $this->db->from('pago_cliente t1');
        $this->db->join('clientes t2', 't1.cliente_id = t2.cliente_id', 'left');
        $this->db->join('usuarios t3', 't1.usuario_id = t3.usuario_id', 'left');
        $this->db->join('pago_forma t4', 't1.pago_forma_id = t4.pago_forma_id', 'left');
        $this->db->where('t1.pago_cliente_id', $pago_cliente_id);
        $pago = $this->db->get()->row();

        if (!$pago) {
            show_404();
            return;
        }

        $this->db->select('producto_nombre, codigo_barras, cantidad, precio_unitario, subtotal');
        $this->db->from('venta_detalle');
        $this->db->where('pago_cliente_id', $pago_cliente_id);
        $this->db->order_by('venta_detalle_id', 'asc');
        $detalle = $this->db->get()->result();

        $data = array(
            'pago' => $pago,
            'detalle' => $detalle
        );

        $this->load->view('section/Ticket_print_v', $data);
    }

    public function cliente_titular($cliente_id){
        foreach ($this->pagos_m->listar_clientes_titulares($cliente_id) as $row) {
            echo '<option value="'.$row->cliente_id.'" '.$row->selected.'>'.$row->cliente_nombre.' '.$row->cliente_apellido.' - '.$row->cliente_ci.'</option>';
        }
    }

    public function mostrar_forma_pago(){
        foreach ($this->pagos_m->mostrar_forma_pago() as $row) {
            echo '<option value="'.$row->pago_forma_id.'" '.$row->selected.'>'.$row->pago_forma_id.'. '.$row->pago_forma_descripcion.'</option>';
        }
    }
    
    public function planes($fecha_nacimiento, $cliente_id){
        $fecha_nacimiento = date("Y-m-d", strtotime($fecha_nacimiento));
        $cumpleanos = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($cumpleanos)->y;       
        
        foreach ($this->clientes_m->listar_planes($edad, $cliente_id) as $row) {
            echo '<option value="'.$row->plan_id.'" '.$row->selected.'>'.$row->plan_categoria_id.'. '.$row->plan_categoria_nombre.' '.$row->planes_costo.' - '.$row->plan_rango_edad_nombre.'</option>';
        }
    }
    
    public function planes_clientes_fecha_ingreso($cliente_id, $plan_id){
        $data = $this->clientes_m->planes_clientes_existe($cliente_id, $plan_id);
        echo json_encode($data);
    }
    
    public function mostrar_ultimo_pago($cliente_id){
        $data = $this->pagos_m->mostrar_ultimo_pago($cliente_id);
        echo json_encode($data);
    }

    public function getTimbrado()
    {
        $data = $this->pagos_m->getTimbradoActivo();
        echo json_encode($data);
    }

    /**
     * Reporte de ventas POS por semana, mes o rango de fechas.
     */
    public function generar_reporte()
    {
        header('Content-Type: application/json');

        $tipo = $this->input->post('tipo');
        $hoy = date('Y-m-d');

        if ($tipo === 'semana') {
            // Semana actual (lunes a domingo)
            $diaSemana = date('N'); // 1=lunes ... 7=domingo
            $fecha_desde = date('Y-m-d', strtotime('-' . ($diaSemana - 1) . ' days'));
            $fecha_hasta = date('Y-m-d', strtotime('+' . (7 - $diaSemana) . ' days'));
        } elseif ($tipo === 'mes') {
            $fecha_desde = date('Y-m-01');
            $fecha_hasta = date('Y-m-t');
        } elseif ($tipo === 'rango') {
            $fecha_desde = $this->input->post('fecha_desde');
            $fecha_hasta = $this->input->post('fecha_hasta');
            if (!$fecha_desde || !$fecha_hasta) {
                echo json_encode(array('status' => 'error', 'message' => 'Indicá fecha desde y hasta.'));
                return;
            }
            if ($fecha_desde > $fecha_hasta) {
                echo json_encode(array('status' => 'error', 'message' => 'La fecha desde no puede ser mayor a la fecha hasta.'));
                return;
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Tipo de reporte no válido.'));
            return;
        }

        $desde_dt = $fecha_desde . ' 00:00:00';
        $hasta_dt = $fecha_hasta . ' 23:59:59';

        // Totales de ventas (tickets pagados en el rango)
        $this->db->select('COALESCE(SUM(pago_cliente_monto_total), 0) AS total_ventas, COUNT(*) AS total_tickets');
        $this->db->from('pago_cliente');
        $this->db->where('pago_cliente_estado', 'Pagado');
        $this->db->where('pago_cliente_fecha >=', $desde_dt);
        $this->db->where('pago_cliente_fecha <=', $hasta_dt);
        $totales = $this->db->get()->row();

        // Desglose de productos vendidos
        $this->db->select('vd.producto_nombre, vd.codigo_barras, SUM(vd.cantidad) AS cantidad, AVG(vd.precio_unitario) AS precio_unitario, SUM(vd.subtotal) AS subtotal');
        $this->db->from('venta_detalle vd');
        $this->db->join('pago_cliente pc', 'pc.pago_cliente_id = vd.pago_cliente_id');
        $this->db->where('pc.pago_cliente_estado', 'Pagado');
        $this->db->where('pc.pago_cliente_fecha >=', $desde_dt);
        $this->db->where('pc.pago_cliente_fecha <=', $hasta_dt);
        $this->db->group_by('vd.producto_id, vd.producto_nombre, vd.codigo_barras');
        $this->db->order_by('subtotal', 'DESC');
        $desglose = $this->db->get()->result();

        $total_productos = 0;
        foreach ($desglose as $item) {
            $total_productos += intval($item->cantidad);
        }

        echo json_encode(array(
            'status' => 'success',
            'periodo' => array(
                'tipo' => $tipo,
                'desde' => $fecha_desde,
                'hasta' => $fecha_hasta,
                'etiqueta' => date('d/m/Y', strtotime($fecha_desde)) . ' — ' . date('d/m/Y', strtotime($fecha_hasta))
            ),
            'total_ventas' => number_format(floatval($totales->total_ventas), 2, '.', ''),
            'total_tickets' => intval($totales->total_tickets),
            'total_productos' => $total_productos,
            'desglose' => $desglose
        ));
    }

    public function procesar_pago_pos()
    {
        $articulos = $this->input->post('articulos');

        if (empty($articulos) || !is_array($articulos)) {
            echo json_encode(array('status' => 'error', 'message' => 'El carrito está vacío.'));
            return;
        }

        $userObj = $this->usuarios_m->obtener_por_id($this->session->userdata('usuario_id'));
        $userSucursal = $userObj ? $userObj->sucursal_id : 1;
        $facturaObj = $this->factura_m->getBySucursal($userSucursal);

        if (!$facturaObj) {
            echo json_encode(array('status' => 'error', 'message' => 'No hay factura activa para la sucursal.'));
            return;
        }

        $total_venta = 0;
        foreach ($articulos as $art) {
            $total_venta += (floatval($art['precio']) * intval($art['cantidad']));
        }

        // Cabecera adaptada para el POS (Soporta efectivo o transferencia según mandes el form)
        $dataCabecera = array(
            'cliente_id'                => 1, // Cliente genérico o mostrador
            'pago_cliente_fecha'        => date('Y-m-d H:i:s'),
            'pago_cliente_fecha_hasta'  => date('Y-m-d H:i:s'),
            'pago_forma_id'             => $this->input->post('pago_forma_id') ? $this->input->post('pago_forma_id') : 1,
            'pago_forma_efectivo_monto' => $this->input->post('efectivo') ? $this->input->post('efectivo') : $total_venta,
            'pago_forma_tarjeta_monto'  => $this->input->post('tarjeta') ? $this->input->post('tarjeta') : 0,
            'pago_cliente_cuotas'       => 1,
            'pago_cliente_monto_plan'   => $total_venta,
            'pago_cliente_monto_total'  => $total_venta,
            'pago_cliente_estado'       => 'Pagado',
            'usuario_id'                => $this->session->userdata('usuario_id'),
            'sucursal_id'               => $userSucursal,
            'factura_id'                => $facturaObj->factura_id,
            'factura_nro'               => $this->generateNextNumberFactura($facturaObj->nro_actual),
            'factura_ruc'               => 'X',
            'factura_razon_social'      => 'CONSUMIDOR FINAL',
            'factura_concepto'          => 'Venta de productos POS'
        );

        $exito_cabecera = $this->pagos_m->agregar_cabecera($dataCabecera);

        if ($exito_cabecera) {
            // Actualizar número actual de factura
            $dataFacturaUpdate = array(
                'nro_actual' => $this->generateNextNumberFactura($facturaObj->nro_actual),
            );
            $this->factura_m->updateNroActual(array('factura_id' => $facturaObj->factura_id), $dataFacturaUpdate);

            echo json_encode(array(
                'status' => 'success', 
                'message' => 'Pago y transferencia registrados correctamente en el módulo de pagos.'
            ));
        } else {
            echo json_encode(array(
                'status' => 'error', 
                'message' => 'No se pudo registrar la cabecera del pago.'
            ));
        }
    }
}