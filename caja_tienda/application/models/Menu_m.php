<?php
//if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Menu_m extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    var $t1 = 'menu';
    var $t2 = 'grupo_permisos';
    var $t3 = 'grupo_usuarios';

    public function perfiles_usuarios()
    {
        $this->db->from($this->t3);
        $this->db->where('usuario_id', $this->session->userdata('usuario_id'));
        $consulta  = $this->db->get();
        $resultado = $consulta->result();
        return $resultado;
    }

    /**
     * Menú de navegación según permisos del usuario en sesión.
     */
    public function menu($nivel_usuario_id = 0)
    {
        $usuario_id = (int) $this->session->userdata('usuario_id');

        $this->db->select('t4.menu_id, t4.menu_nivel, t4.menu_nombre, t4.menu_id_padre, t4.menu_icono, t4.menu_controlador');
        $this->db->from($this->t2.' t1');
        $this->db->join($this->t3.' t2', 't1.grupo_id = t2.grupo_id AND t2.usuario_id = '.$usuario_id);
        $this->db->join($this->t1.' t3', 't3.menu_id = t1.menu_id');
        $this->db->join($this->t1.' t4', 't4.menu_id = t3.menu_id_padre');
        $this->db->group_by('t4.menu_id, t4.menu_nivel, t4.menu_nombre, t4.menu_id_padre, t4.menu_icono, t4.menu_controlador');
        $this->db->order_by('t4.menu_id', 'asc');
        
        $q = $this->db->get();
        
        $final = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $this->db->select('t1.menu_id, t1.menu_nivel, t1.menu_nombre, t1.menu_id_padre, t1.menu_icono, t1.menu_controlador');
                $this->db->from($this->t1.' t1');
                $this->db->join($this->t2.' t2', 't1.menu_id = t2.menu_id');
                $this->db->where('t1.menu_nivel', 2);
                $this->db->where('t1.menu_id_padre', $row->menu_id);
                $this->db->where('t2.grupo_id IN (SELECT grupo_id FROM grupo_usuarios WHERE usuario_id = '.$usuario_id.')', null, false);
                $this->db->group_by('t1.menu_id, t1.menu_nivel, t1.menu_nombre, t1.menu_id_padre, t1.menu_icono, t1.menu_controlador');
                $this->db->order_by('t1.menu_id', 'asc');
                $qChildren = $this->db->get();

                if ($qChildren->num_rows() > 0) {
                    $children = $qChildren->result();
                    $usuario_user = strtolower(trim((string) $this->session->userdata('usuario_user')));

                    // Menú de configuración solo visible para superadmin
                    if ($usuario_user !== 'superadmin') {
                        $children = array_values(array_filter($children, function ($child) {
                            return strtolower($child->menu_controlador) !== 'menu';
                        }));
                    }

                    if (count($children) > 0) {
                        $row->children = $children;
                        array_push($final, $row);
                    }
                } else {
                    array_push($final, $row);
                }
            }
        }
        return $final;
    }
    
    /**
     * Listado de módulos para select optgroup (pantalla Usuarios / permisos).
     */
    public function listar_modulos()
    {
        $this->db->from($this->t1);
        $this->db->where('menu_nivel', 1);
        $q = $this->db->get();
        
        $final = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $this->db->from($this->t1);
                $this->db->where('menu_nivel', 2);
                $this->db->where('menu_id_padre', $row->menu_id);
                $q = $this->db->get();
                
                if ($q->num_rows() > 0) {
                    $row->children = $q->result();
                }
                array_push($final, $row);
            }
        }
        return $final;
    }

}
