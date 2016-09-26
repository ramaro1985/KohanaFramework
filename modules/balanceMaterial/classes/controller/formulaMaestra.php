<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_FormulaMaestra extends Controller_Secured {

    public function before() {
        $this->gestion = Model::factory('ModelFormulaMaestra');
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
        $id_formula_insertada = $this->gestion->insertar_formula($id_producto_terminado, $formula_name, $predeterminada, $cant_productos);

        if ($id_formula_insertada != NULL) {
            $insert_ok = array('success' => TRUE, 'msg' => 'F&oacute;rmula insertada correctamente. Inserte las materias primas');
//--------Este es el Id_de la formula insertada--------//
            $_SESSION['id_formula_insertada'] = $id_formula_insertada;

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
        
        $editar_formula = $this->gestion->modificar_formula($id_formula, $id_producto_terminado, $nombre_formula, $predeterminada);
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

    public function action_materias_primas_formula_seleccionada_a_partir_de() {
        session_start();

        $id_formula_a_partir_de = $this->request->post('id_formula_a_partir_de');
        $id_formula_insertada = $_SESSION['id_formula_insertada'];
        if (isset($id_formula_a_partir_de)) {
            $materias_primas_formula_a_partir_de = $this->gestion->materias_primas_formula($id_formula_a_partir_de);
            $this->action_insertar_materia_prima($materias_primas_formula_a_partir_de, $id_formula_insertada);
        }
        $materias_primas_formula_insertada = $this->gestion->materias_primas_formula($id_formula_insertada);
        echo json_encode($materias_primas_formula_insertada);
    }

    public function action_insertar_materia_prima($materias_primas_formula_a_partir_de, $id_formula_insertada) {
        session_start();
        $insertar_mp = $this->gestion->insertar_materia_prima($materias_primas_formula_a_partir_de, $id_formula_insertada);
    }

    public function action_insertar_materia_prima_pivote() {
        session_start();

        $id_formula_insertada = $_SESSION['id_formula_insertada'];
        $nombre_producto = $this->request->post('materia_prima');
        $indice_consumo;

        $obj_materia_prima = $this->gestion->obj_materia_prima($nombre_producto);

        $materias_primas = array("0" => array("id_materia_prima" => $obj_materia_prima->id_materiaprima, "indice_consumo" => $indice_consumo));

        $insertar_mp = $this->gestion->insertar_materia_prima($materias_primas, $id_formula_insertada);
    }

    public function action_eliminar_materia_prima() {
        session_start();

        $id_formula_insertada = $_SESSION['id_formula_insertada'];
        $nombre_producto = $this->request->post('materia_prima');
        $eliminar_mp = $this->gestion->eliminar_materia_prima($nombre_producto, $id_formula_insertada);
    }

    public function action_editar_datos_mp_formula() {
        session_start();
        $id_formula_insertada = $_SESSION['id_formula_insertada'];

        $record = $this->request->post('records');
        $records = json_decode(stripslashes($record));
        $editar_mp = $this->gestion->editar_datos_mp($records, $id_formula_insertada);
        if ($editar_mp == TRUE)
            echo json_encode(array('success' => true));
    }

    public function action_editar_datos_materia_prima() {

        $record = $this->request->post('records');
        $records = json_decode(stripslashes($record));
        $editar_mp = $this->gestion->editar_datos_mp($records, $id_formula_insertada = '');
        if ($editar_mp == TRUE)
            echo json_encode(array('success' => true));
    }

}
