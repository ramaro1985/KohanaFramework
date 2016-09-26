<?php

defined('SYSPATH') or die('No direct script access.');

class Model_ModelGestion extends Model_Database {

    public function __construct() {

        parent::__construct();
    }

    public function nombre_laboratorio() {

        $this->db = DB::load('gestion', TRUE);
        $laboratorios = $this->db->select('lb.*')
                        ->from("n_laboratorio AS lb")
                        ->get()->result_array();

        return $laboratorios;
    }

    public function nombre_productos($id_laboratorio) {
        $this->db = DB::load('gestion', TRUE);

        $productos = $this->db->select('pd.*')
                        ->from('productos AS pd')
                        ->join('productoTerminado AS pdt', 'pdt.id_producto = pd.id_producto', 'inner')
                        ->join('historial_producto AS hp', 'hp.id_producto = pd.id_producto', 'inner')
                        ->where("hp.id_lab = $id_laboratorio")
                        ->get()->result_array();
        return $productos;
    }

    public function producto_terminado($id_producto) {
        $this->db = DB::load('gestion', TRUE);
        $procuto_terminado = $this->db->select('pdt.*')
                        ->from('productoTerminado AS pdt')
                        ->where("pdt.id_producto", $id_producto)
                        ->limit(1)
                        ->get()->result();

        return $procuto_terminado;
    }

    public function nombre_formula($id_producto_terminado) {
        $this->db = DB::load('gestion', TRUE);
        $formulas = $this->db->select('form.*')
                        ->from('n_formulafarmaceutica AS form')
                        ->where("form.id_prod_terminado", $id_producto_terminado)
                        ->get()->result_array();
        return $formulas;
    }

    public function obj_formula($id_formula) {
        $this->db = DB::load('gestion', TRUE);
        $formulas = $this->db->select('form.*')
                        ->from('n_formulafarmaceutica AS form')
                        ->where("form.id_formula", $id_formula)
                        ->get()->result();
        return $formulas_obj = $formulas[0];
    }

    public function modificar_formula($id_formula, $id_producto_terminado, $nombre_formula, $predeterminada, $cant_productos) {

        $this->db = DB::load('gestion', TRUE);
        if ($predeterminada == 'on') {
            $nada = NULL;
            $data = array(
                "predeterminada" => '',
            );
            $this->db->where('id_prod_terminado', $id_producto_terminado);
            $this->db->update('n_formulafarmaceutica', $data);
            $data1 = array(
                "predeterminada" => 'on',
                "tipo_formula" => $nombre_formula,
                "cant_productos" => $cant_productos
            );
            $this->db->where('id_formula', $id_formula);
            $this->db->update('n_formulafarmaceutica', $data1);
            return TRUE;
        } else {
            $data2 = array(
                "tipo_formula" => $nombre_formula,
                "predeterminada" => '',
                "cant_productos" => $cant_productos
            );
            $this->db->where('id_formula', $id_formula);
            $this->db->update('n_formulafarmaceutica', $data2);
            return TRUE;
        }
    }

    public function insertar_formula($id_producto_terminado, $formula_name, $predeterminada, $cant_productos) {

        $this->db = DB::load('gestion', TRUE);
        $exsite_formulas = $this->db->select('form.*')
                        ->from('n_formulafarmaceutica AS form')
                        ->where("form.id_prod_terminado", $id_producto_terminado)
                        ->where("form.tipo_formula", $formula_name)
                        ->limit(1)
                        ->get()->result();
        if (empty($exsite_formulas)) {
            if ($predeterminada == 'on') {
                $data1 = array(
                    "predeterminada" => '',
                );
                $this->db->where('id_prod_terminado', $id_producto_terminado);
                $this->db->update('n_formulafarmaceutica', $data1);

                $data = array(
                    "id_prod_terminado" => $id_producto_terminado,
                    "tipo_formula" => $formula_name,
                    "predeterminada" => 'on',
                    "cant_productos" => $cant_productos
                );
                $this->db->insert('n_formulafarmaceutica', $data);
            } else {
                $data = array(
                    "id_prod_terminado" => $id_producto_terminado,
                    "tipo_formula" => $formula_name,
                    "predeterminada" => '',
                    "cant_productos" => $cant_productos
                );
                $this->db->insert('n_formulafarmaceutica', $data);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function elimina_formula($id_producto_terminado, $formula_name) {
        $this->db = DB::load('gestion', TRUE);
        $data = array(
            "id_prod_terminado" => $id_producto_terminado,
            "tipo_formula" => $formula_name,
        );
        $this->db->delete('n_formulafarmaceutica', $data);
        return TRUE;
    }

    public function nombre_materias_primas() {
        $this->db = DB::load('gestion', TRUE);

        $productos = $this->db->select('pd.*')
                        ->from('productos AS pd')
                        ->join('materiaPrima AS mp', 'mp.id_producto = pd.id_producto', 'inner')
                        ->get()->result_array();
        return $productos;
    }

    public function materias_primas_formula($id_formula) {

        $this->db = DB::load('gestion', TRUE);
        $materias_primas = $this->db->select('mp.*')
                        ->from('materiaPrima AS mp')
                        ->join('formulafarma_materiaprima AS fmp', 'fmp.id_materiaprima = mp.id_materiaprima', 'inner')
                        ->join('n_formulafarmaceutica AS fm', 'fm.id_formula = fmp.id_formula', 'inner')
                        ->where("fm.id_formula = $id_formula")
                        ->get()->result_array();

        $datos = array();
        for ($i = 0; $i < count($materias_primas); $i++) {
            $producto = $this->db->select('pd.*')
                            ->from('productos AS pd')
                            ->where("pd.id_producto", $materias_primas[$i]['id_producto'])
                            ->limit(1)
                            ->get()->result();
            $obj_producto = $producto[0];
            $formula_mp = $this->db->select('fmp.*')
                            ->from('formulafarma_materiaprima AS fmp')
                            ->where("fmp.id_materiaprima", $materias_primas[$i]['id_materiaprima'])
                            ->where("fmp.id_formula", $id_formula)
                            ->limit(1)
                            ->get()->result();
            $obj_formula_mp = $formula_mp[0];
            $datos[$i] = array(
                "id_materia_prima" => $materias_primas[$i]['id_materiaprima'],
                "id_producto" => $obj_producto->id_producto,
                "id_formula" => $obj_formula_mp->id_formula,
                "nombre_producto_mp" => $obj_producto->nombre,
                "cantidad_producto" => $obj_formula_mp->cant_producto,
                "indice_consumo" => $obj_formula_mp->indice_consumo,
            );
        }
        return $datos;
    }

    public function insertar_materia_prima($nombre_producto, $id_formula, $indice_consumo) {
        $this->db = DB::load('gestion', TRUE);
        $materia_prima = $this->db->select('mp.*')
                        ->from('materiaPrima AS mp')
                        ->join('productos AS pd', 'pd.id_producto= mp.id_producto', 'inner')
                        ->where("pd.nombre", $nombre_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_materia_prima = $materia_prima[0];

        $existe_mp = $this->db->select('fmp.*')
                        ->from('formulafarma_materiaprima AS fmp')
                        ->where("fmp.id_materiaprima", $obj_materia_prima->id_materiaprima)
                        ->where("fmp.id_formula", $id_formula)
                        ->limit(1)
                        ->get()->result();
        if (empty($existe_mp)) {
            $data = array(
                "id_materiaprima" => $obj_materia_prima->id_materiaprima,
                "id_formula" => $id_formula,
                "indice_consumo" => $indice_consumo
            );
            $this->db->insert('formulafarma_materiaprima', $data);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function eliminar_materia_prima($nombre_producto, $id_formula) {
        $this->db = DB::load('gestion', TRUE);
        $materia_prima = $this->db->select('mp.*')
                        ->from('materiaPrima AS mp')
                        ->join('productos AS pd', 'pd.id_producto= mp.id_producto', 'inner')
                        ->where("pd.nombre", $nombre_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_materia_prima = $materia_prima[0];
        $data = array(
            "id_materiaprima" => $obj_materia_prima->id_materiaprima,
            "id_formula" => $id_formula
        );
        $this->db->delete('formulafarma_materiaprima', $data);
        return TRUE;
    }

    public function editar_datos_mp($records) {
        $this->db = DB::load('gestion', TRUE);
        for ($i = 0; $i < count($records); $i++) {

            $data = array(
                "indice_consumo" => $records[$i]->indice_consumo
            );
            $this->db->where('id_materiaprima', $records[$i]->id_materia_prima);
            $this->db->where('id_formula', $records[$i]->id_formula);
            $this->db->update('formulafarma_materiaprima', $data);
        }
        return TRUE;
    }

}
