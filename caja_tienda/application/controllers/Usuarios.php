<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('usuarios_m');
        $this->load->model('menu_m');
        $this->load->model('auxiliar_m');
        date_default_timezone_set("America/Asuncion");

        // Solo el usuario "superadmin" puede gestionar usuarios y contraseñas
        if (!$this->es_usuario_superadmin()) {
            redirect('welcome');
            exit;
        }
    }

    private function es_usuario_superadmin()
    {
        $usuario_user = strtolower(trim((string) $this->session->userdata('usuario_user')));
        return ($usuario_user === 'superadmin');
    }

    public function index()
    {
        $this->db->select('t1.usuario_id, t1.usuario_nombre, t1.usuario_apellido, t1.usuario_user, t1.usuario_estado, t1.usuario_dateinsert, GROUP_CONCAT(t3.grupo_nombre SEPARATOR ", ") AS usuario_grupos, MAX(t2.grupo_id) AS grupo_id');
        $this->db->from('usuarios t1');
        $this->db->join('grupo_usuarios t2', 't1.usuario_id = t2.usuario_id', 'left');
        $this->db->join('grupo t3', 't3.grupo_id = t2.grupo_id', 'left');
        $this->db->where('t1.usuario_estado <>', 'borrado');
        $this->db->group_by('t1.usuario_id, t1.usuario_nombre, t1.usuario_apellido, t1.usuario_user, t1.usuario_estado, t1.usuario_dateinsert');
        $this->db->order_by('t1.usuario_id', 'ASC');
        $data['usuarios'] = $this->db->get()->result();

        $this->db->from('grupo');
        $this->db->order_by('grupo_id', 'ASC');
        $data['grupos'] = $this->db->get()->result();

        $data['breadcrumb'] = $this->auxiliar_m->breadcrumb($this->router->class);

        $this->load->view('html/Head');
        $this->load->view('html/Nav', array(
            'model_menu' => $this->load->model('menu_m'),
            'menus' => $this->menu_m->menu()
        ));
        $this->load->view('html/Breadcrumb_v', $data);
        $this->load->view('admin/Usuarios_v', $data);
        $this->load->view('html/Footer');
    }

    public function guardar()
    {
        $usuario_user = trim($this->input->post('usuario_user'));
        $usuario_pass = $this->input->post('usuario_pass');
        $grupo_id = intval($this->input->post('grupo_id'));

        if ($usuario_user === '' || $usuario_pass === '') {
            echo "Usuario y contraseña son obligatorios.";
            return;
        }

        $data = array(
            'usuario_nombre'     => $this->input->post('usuario_nombre'),
            'usuario_apellido'   => $this->input->post('usuario_apellido'),
            'usuario_user'       => $usuario_user,
            'usuario_pass'       => md5($usuario_pass),
            'usuario_estado'     => $this->input->post('usuario_estado') ? $this->input->post('usuario_estado') : 'activo',
            'usuario_dateinsert' => date('Y-m-d H:i:s'),
        );

        $this->db->insert('usuarios', $data);
        $usuario_id = $this->db->insert_id();

        if ($usuario_id && $grupo_id > 0) {
            $this->db->insert('grupo_usuarios', array(
                'usuario_id' => $usuario_id,
                'grupo_id'   => $grupo_id
            ));
        }

        redirect('usuarios');
    }

    public function actualizar()
    {
        $id = intval($this->input->post('usuario_id'));
        $usuario_user = trim($this->input->post('usuario_user'));
        $grupo_id = intval($this->input->post('grupo_id'));

        if (!$id) {
            echo "Usuario no válido.";
            return;
        }

        $actual = $this->db->get_where('usuarios', array('usuario_id' => $id))->row();
        if (!$actual) {
            echo "Usuario no encontrado.";
            return;
        }

        // No permitir cambiar el login del usuario superadmin
        if (strtolower($actual->usuario_user) === 'superadmin') {
            $usuario_user = 'superadmin';
        }

        $data = array(
            'usuario_nombre'   => $this->input->post('usuario_nombre'),
            'usuario_apellido' => $this->input->post('usuario_apellido'),
            'usuario_user'     => $usuario_user,
            'usuario_estado'   => $this->input->post('usuario_estado') ? $this->input->post('usuario_estado') : 'activo',
            'usuario_dateupdate' => date('Y-m-d H:i:s'),
        );

        $nueva_pass = $this->input->post('usuario_pass');
        if ($nueva_pass !== null && $nueva_pass !== '') {
            $data['usuario_pass'] = md5($nueva_pass);
        }

        $this->db->where('usuario_id', $id);
        $this->db->update('usuarios', $data);

        // Actualizar grupo (excepto bloquear quitar superadmin de su grupo)
        if ($grupo_id > 0) {
            if (strtolower($actual->usuario_user) === 'superadmin') {
                $grupo_id = 1;
            }
            $this->db->where('usuario_id', $id);
            $this->db->delete('grupo_usuarios');
            $this->db->insert('grupo_usuarios', array(
                'usuario_id' => $id,
                'grupo_id'   => $grupo_id
            ));
        }

        redirect('usuarios');
    }

    public function eliminar($id)
    {
        $id = intval($id);
        $actual = $this->db->get_where('usuarios', array('usuario_id' => $id))->row();

        if (!$actual) {
            redirect('usuarios');
            return;
        }

        // No permitir eliminar al usuario superadmin
        if (strtolower($actual->usuario_user) === 'superadmin') {
            redirect('usuarios');
            return;
        }

        $this->db->where('usuario_id', $id);
        $this->db->update('usuarios', array(
            'usuario_estado' => 'borrado',
            'usuario_dateupdate' => date('Y-m-d H:i:s')
        ));

        redirect('usuarios');
    }
}
