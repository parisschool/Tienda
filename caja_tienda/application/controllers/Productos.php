<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('menu_m');
        // Cargamos el modelo auxiliar del sistema para la navegación y breadcrumbs
        $this->load->model('auxiliar_m');
    }

    public function index() {
        // 1. Consultamos los productos
        $query = $this->db->get_where('productos', array('activo' => 1));
        $data['productos'] = $query->result();

        // 2. Generamos el breadcrumb usando la clase actual del controlador
        $data['breadcrumb'] = $this->auxiliar_m->breadcrumb($this->router->class);

        // 3. Cargamos la plantilla en el orden correcto de tu POS
        $this->load->view('html/Head');
        $this->load->view('html/Nav', array(
            'model_menu' => $this->load->model('menu_m'), 
            'menus' => $this->menu_m->menu()
        ));
        
        // Cargamos el breadcrumb oficial del sistema (crea los contenedores principales)
        $this->load->view('html/Breadcrumb_v', $data);

        // Cargamos tu vista de productos dentro de la estructura
        $this->load->view('productos/index', $data);

        $this->load->view('html/Footer');
    }

    public function guardar() {
        // Recibimos los datos del formulario sanitizados por POST
        $data = array(
            'codigo_barras' => $this->input->post('codigo_barras'),
            'nombre'        => $this->input->post('nombre'),
            'precio_venta'  => $this->input->post('precio_venta'),
            'stock'         => $this->input->post('stock'),
            'activo'        => 1 // Para que se muestre por defecto en la tabla
        );

        // Insertamos el registro directo en la tabla 'productos'
        if ($this->db->insert('productos', $data)) {
            // Redireccionamos de vuelta al mantenimiento de productos
            redirect('productos');
        } else {
            // En caso de que falle por algún error de BD
            echo "Error al guardar el producto.";
        }
    }
    // Método para guardar los cambios editados del producto
    public function actualizar() {
        $id = $this->input->post('id');
        
        $data = array(
            'codigo_barras' => $this->input->post('codigo_barras'),
            'nombre'        => $this->input->post('nombre'),
            'precio_venta'  => $this->input->post('precio_venta'),
            'stock'         => $this->input->post('stock')
        );

        $this->db->where('id', $id);
        if ($this->db->update('productos', $data)) {
            redirect('productos');
        } else {
            echo "Error al actualizar el producto.";
        }
    }

    // Método para eliminar de manera lógica (activo = 0)
    public function eliminar($id) {
        $this->db->where('id', $id);
        // Hacemos un update en lugar de un delete físico
        if ($this->db->update('productos', array('activo' => 0))) {
            redirect('productos');
        } else {
            echo "Error al intentar eliminar el producto.";
        }
    }
}