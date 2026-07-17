<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('menu_m');
        $this->load->model('auxiliar_m');
        $this->load->model('pagos_m');
        $this->load->model('clientes_m');
        $this->load->model('usuarios_m');
        $this->load->model('factura_m');
        $this->load->helper('url');
        date_default_timezone_set("America/Asuncion");
    }

    public function index() {
        $data['breadcrumb'] = $this->auxiliar_m->breadcrumb($this->router->class);

        $this->load->view('html/Head');
        $this->load->view('html/Nav', array(
            'model_menu' => $this->load->model('menu_m'), 
            'menus' => $this->menu_m->menu()
        ));
        $this->load->view('html/Breadcrumb_v', $data);
        $this->load->view('ventas/index');
        $this->load->view('html/Footer');
    }

    public function buscar_por_codigo() {
        $busqueda = $this->input->post('codigo_barras');

        if (!$busqueda) {
            echo json_encode(array('status' => 'error', 'message' => 'No se proporcionó término de búsqueda'));
            return;
        }

        $this->db->where('codigo_barras', $busqueda);
        $this->db->where('activo', 1);
        $query = $this->db->get('productos');

        if ($query->num_rows() > 0) {
            $producto = $query->row();
            if ($producto->stock <= 0) {
                echo json_encode(array('status' => 'error', 'message' => 'El producto no cuenta con stock disponible'));
            } else {
                echo json_encode(array(
                    'status' => 'success',
                    'action' => 'add_direct',
                    'producto' => $producto
                ));
            }
            return;
        }

        $this->db->group_start();
        $this->db->like('nombre', $busqueda);
        $this->db->or_like('codigo_barras', $busqueda, 'after');
        $this->db->group_end();
        
        $this->db->where('activo', 1);
        $this->db->limit(10);
        $query_nombre = $this->db->get('productos');

        if ($query_nombre->num_rows() > 0) {
            echo json_encode(array(
                'status' => 'success',
                'action' => 'show_suggestions',
                'productos' => $query_nombre->result()
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No se encontraron productos'));
        }
    }

    public function procesar_venta() {
        header('Content-Type: application/json');

        $articulos = $this->input->post('articulos');
    
        if (empty($articulos) || !is_array($articulos)) {
            echo json_encode(array('status' => 'error', 'message' => 'El carrito está vacío o no es válido.'));
            return;
        }

        $usuario_id = $this->session->userdata('usuario_id');
        if (!$usuario_id) {
            echo json_encode(array('status' => 'error', 'message' => 'Sesión no válida. Vuelve a iniciar sesión.'));
            return;
        }

        $clientePosId = $this->obtener_o_crear_cliente_pos();
        if (!$clientePosId) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'No se pudo crear el cliente CONSUMIDOR FINAL. ' . $this->db->error()['message']
            ));
            return;
        }

        $this->db->trans_start();

        $total_venta = 0;
        $lineas = array();

        foreach ($articulos as $art) {
            $id_producto = intval($art['id']);
            $cantidad_vendida = intval($art['cantidad']);

            $this->db->select('id, stock, nombre, precio_venta, codigo_barras');
            $this->db->where('id', $id_producto);
            $query = $this->db->get('productos');

            if ($query->num_rows() === 0) {
                $this->db->trans_rollback();
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Producto no encontrado (ID: ' . $id_producto . ').'
                ));
                return;
            }

            $prod = $query->row();
            $nuevo_stock = $prod->stock - $cantidad_vendida;

            if ($nuevo_stock < 0) {
                $this->db->trans_rollback();
                echo json_encode(array(
                    'status' => 'error', 
                    'message' => 'Stock insuficiente para el producto: ' . $prod->nombre
                ));
                return;
            }

            $this->db->where('id', $id_producto);
            $this->db->update('productos', array('stock' => $nuevo_stock));

            $precio = floatval($prod->precio_venta);
            $subtotal = $precio * $cantidad_vendida;
            $total_venta += $subtotal;

            $lineas[] = array(
                'producto_id'      => $prod->id,
                'producto_nombre'  => $prod->nombre,
                'codigo_barras'    => $prod->codigo_barras,
                'cantidad'         => $cantidad_vendida,
                'precio_unitario'  => $precio,
                'subtotal'         => $subtotal,
            );
        }

        $efectivo = $this->input->post('efectivo');

        $dataCabecera = array(
            'cliente_id'                => $clientePosId,
            'pago_cliente_fecha'        => date('Y-m-d H:i:s'),
            'pago_cliente_fecha_hasta'  => date('Y-m-d H:i:s'),
            'pago_forma_id'             => $this->input->post('pago_forma_id') ? $this->input->post('pago_forma_id') : 1,
            'pago_forma_efectivo_monto' => ($efectivo !== null && $efectivo !== '') ? $efectivo : $total_venta,
            'pago_forma_tarjeta_monto'  => $this->input->post('tarjeta') ? $this->input->post('tarjeta') : 0,
            'pago_cliente_cuotas'       => 1,
            'pago_cliente_monto_plan'   => $total_venta,
            'pago_cliente_monto_total'  => $total_venta,
            'pago_cliente_estado'       => 'Pagado',
            'usuario_id'                => $usuario_id,
        );

        $pago_id = $this->pagos_m->agregar_cabecera($dataCabecera);

        if (!$pago_id) {
            $this->db->trans_rollback();
            $dbError = $this->db->error();
            echo json_encode(array(
                'status' => 'error',
                'message' => 'No se pudo registrar el pago. ' . (isset($dbError['message']) ? $dbError['message'] : '')
            ));
            return;
        }

        foreach ($lineas as $linea) {
            $linea['pago_cliente_id'] = $pago_id;
            $this->db->insert('venta_detalle', $linea);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(array('status' => 'error', 'message' => 'Error al procesar la venta.'));
            return;
        }

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Venta procesada, stock actualizado y pago registrado.',
            'pago_cliente_id' => $pago_id,
            'cliente_id' => $clientePosId
        ));
    }

    private function obtener_o_crear_cliente_pos() {
        $this->db->select('cliente_id');
        $this->db->from('clientes');
        $this->db->where('cliente_nombre', 'CONSUMIDOR');
        $this->db->where('cliente_apellido', 'FINAL');
        $this->db->where('cliente_estado <>', 'borrado');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return (int) $query->row()->cliente_id;
        }

        $data = array(
            'cliente_nombre'     => 'CONSUMIDOR',
            'cliente_apellido'   => 'FINAL',
            'cliente_tipo_id'    => 1,
            'cliente_ci'         => 0,
            'cliente_cel'        => 0,
            'cliente_direccion'  => 'Mostrador / POS',
            'cliente_dateinsert' => date('Y-m-d H:i:s'),
            'cliente_estado'     => 'activo',
            'usuario_id'         => $this->session->userdata('usuario_id'),
        );

        $this->db->insert('clientes', $data);
        $insertId = $this->db->insert_id();

        return $insertId ? (int) $insertId : null;
    }
}
