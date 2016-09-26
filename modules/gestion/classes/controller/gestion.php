<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Gestion extends Controller {

    public function before() {
        $this->gestion = Model::factory('ModelGestion');
    }

    public function action_index() {
        
    }

    public function action_nombre_laboratorio() {

        $result = $this->gestion->nombre_laboratorio();
        $datosb = array('datos' => $result);
        echo json_encode($datosb);
    }

    public function action_nombre_productos() {

        $start = $this->request->post('start');
        $limit = $this->request->post('limit');
        if (isset($start) && isset($limit)) {
            $start = $this->request->post('start');
            $limit = $this->request->post('limit');
        } else {
            $start = 0;
            $limit = 20;
        }
        $id_laboratorio = $this->request->post('id_laboratorio');
        $data = $this->gestion->nombre_productos($id_laboratorio);

        $paging = array(
            'success' => true,
            'total' => count($data),
            'data' => array_splice($data, $start, $limit)
        );
        echo json_encode($paging);
    }

    public function action_obj_formula() {
        $id_formula = $this->request->post('id_formula');
        $result = $this->gestion->obj_formula($id_formula);
        $datosb = array('success' => true, 'data' => $result);
        echo json_encode($datosb);
    }

    public function action_nombre_formula() {
        session_start();
        $id_producto = $this->request->post('id_producto');
        if (isset($id_producto)) {
            $id_producto = $this->request->post('id_producto');
            $_SESSION['id_producto'] = $id_producto;
        } else {
            $id_producto = $_SESSION['id_producto'];
        }
        $producto_terminado = $this->gestion->producto_terminado($id_producto);
        $id_producto_terminado = $producto_terminado[0]->id_prod_terminado;
        $formulas = $this->gestion->nombre_formula($id_producto_terminado);

        $_SESSION['id_producto_terminado'] = $id_producto_terminado;
        $datosb = array('datos' => $formulas);
        echo json_encode($datosb);
    }

    public function action_insertar_formula() {
        session_start();
        $id_producto_terminado = $_SESSION['id_producto_terminado'];
        $formula_name = $this->request->post('formula_name');
        $predeterminada = $this->request->post('predeterminada');
        $cant_productos = $this->request->post('cant_productos');
        $insertar_formula = $this->gestion->insertar_formula($id_producto_terminado, $formula_name, $predeterminada,$cant_productos);
        if ($insertar_formula == TRUE) {
            $insert_ok = array('success' => TRUE, 'msg' => 'F&oacute;rmula insertada correctamente.');
            echo json_encode($insert_ok);
        } else {
            $insert_ok = array('success' => FALSE, 'msg' => 'Ya existe esa f&oacute;rmula.');
            echo json_encode($insert_ok);
        }
    }

    public function action_eliminar_formula() {
        session_start();
        $id_producto_terminado = $_SESSION['id_producto_terminado'];
        $formula_name = $this->request->post('formula_name');
        $eliminar = $this->gestion->elimina_formula($id_producto_terminado, $formula_name);

        if ($eliminar == TRUE) {
            $delete_ok = array('success' => TRUE, 'msg' => 'F&oacute;rmula eliminada correctamente.');
            echo json_encode($delete_ok);
        }
    }

    public function action_editar_formula() {
        session_start();
        $id_producto_terminado = $_SESSION['id_producto_terminado'];
        $id_formula = $this->request->post('id_formula');
        $nombre_formula = $this->request->post('tipo_formula');
        $predeterminada = $this->request->post('predeterminada');
        $cant_productos= $this->request->post('cant_productos');

        $editar_formula = $this->gestion->modificar_formula($id_formula, $id_producto_terminado, $nombre_formula, $predeterminada,$cant_productos);
        if ($editar_formula == TRUE) {
            $editar_formula_ok = array('success' => TRUE, 'msg' => 'F&oacute;rmula modificada correctamente.');
            echo json_encode($editar_formula_ok);
        }
    }

    public function action_nombre_materias_primas() {

        $result = $this->gestion->nombre_materias_primas();
        $datosb = array('datos' => $result);
        echo json_encode($datosb);
    }

    public function action_materias_primas_formula() {
        session_start();
        $start = $this->request->post('start');
        $limit = $this->request->post('limit');
        $id_formula = $this->request->post('id_formula');
        if (isset($start) && isset($limit)) {
            $start = $this->request->post('start');
            $limit = $this->request->post('limit');
        } else {
            $start = 0;
            $limit = 15;
        }
        if (isset($id_formula)) {
            $id_formula = $this->request->post('id_formula');
            $_SESSION['id_formula'] = $id_formula;
        } else {
            $id_formula = $_SESSION['id_formula'];
        }
        $data = $this->gestion->materias_primas_formula($id_formula);
        $paging = array(
            'success' => true,
            'total' => count($data),
            'data' => array_splice($data, $start, $limit),
            'id_formula' => $id_formula
        );
        echo json_encode($paging);
    }

    public function action_insertar_materia_prima() {
        session_start();

        $id_formula = $_SESSION['id_formula'];
        $nombre_producto = $this->request->post('materia_prima');
        $indice_consumo = $this->request->post('indice_consumo');
        
        $insertar_mp = $this->gestion->insertar_materia_prima($nombre_producto, $id_formula,$indice_consumo);

        if ($insertar_mp == TRUE) {
            $insertar_mp = array('success' => TRUE, 'msg' => 'Materia Prima insertada correctamente.');
            echo json_encode($insertar_mp);
        } else {
            $insertar_mp = array('success' => FALSE, 'msg' => 'Ya existe esa MP en la f&oacute;rmula seleccionada.');
            echo json_encode($insertar_mp);
        }
    }

    public function action_eliminar_materia_prima() {
        session_start();

        $id_formula = $_SESSION['id_formula'];
        $nombre_producto = $this->request->post('materia_prima');

        $eliminar_mp = $this->gestion->eliminar_materia_prima($nombre_producto, $id_formula);

        if ($eliminar_mp == TRUE) {
            $eliminar_mp = array('success' => TRUE, 'msg' => 'Materia Prima eliminada correctamente.');
            echo json_encode($eliminar_mp);
        }
    }

    public function action_editar_datos_mp() {
        session_start();

        $id_formula = $_SESSION['id_formula'];
        $record = $this->request->post('records');
        $records = json_decode(stripslashes($record));

        $editar_mp = $this->gestion->editar_datos_mp($records);
        if ($editar_mp == TRUE)
            echo json_encode(array('success' => true));
    }

}
