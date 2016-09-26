<?php

defined('SYSPATH') or die('No direct script access.');

class Model_ModelPlanProduccion extends Model_Database {

    public function __construct() {

        parent::__construct();
    }

    public function nombre_formaFarmaceutica() {

        $this->db = DB::load('gestion', TRUE);
        $nombre_formaFarmaceutica = $this->db->select('lp.*')
                        ->from("nomencladores.nom_lineaproducto AS lp")
                        ->get()->result_array();
        return $nombre_formaFarmaceutica;
    }

    public function productos_lab($id_laboratorio, $formaFarmaceutica) {
        $this->db = DB::load('gestion', TRUE);

        if ($formaFarmaceutica != NULL) {
            $productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->join('datos.producto_terminado AS pdt', 'pdt.id_producto = pd.id_producto', 'inner')
                            ->join('datos.historial_producto AS hp', 'hp.id_producto = pd.id_producto', 'inner')
                            ->where("hp.id_lab = $id_laboratorio")
                            ->where("pd.id_linea = $formaFarmaceutica")
                            ->get()->result_array();
        } else {
            $productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->join('datos.producto_terminado AS pdt', 'pdt.id_producto = pd.id_producto', 'inner')
                            ->join('datos.historial_producto AS hp', 'hp.id_producto = pd.id_producto', 'inner')
                            ->where("hp.id_lab = $id_laboratorio")
                            ->get()->result_array();
        }

        return $productos;
    }

    public function nombre_productos($id_laboratorio, $formaFarmaceutica, $id_producto) {
        $this->db = DB::load('gestion', TRUE);
        if ($id_producto != NULL) {
            $um = $this->db->select('um.*')
                            ->from('nomencladores.nom_unidadmedida AS um')
                            ->join('datos.productos AS pd', 'pd.id_um = um.id_um', 'inner')
                            ->where("pd.id_producto = $id_producto")
                            ->limit(1)
                            ->get()->result();
            $obj_um = $um [0];

             $plan_consumo = $this->db->select('planconsm_lab.*')
                            ->from('datos.planconsumo_lab AS planconsm_lab')
                            ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                            ->where("planconsm_lab.id_lab", $id_laboratorio)
                            ->where("pd_planconsm.id_producto",$id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_plan_consumo_producto = $plan_consumo[0];

            $h_productos = $this->db->select('hp.*')
                            ->from('datos.historial_producto AS hp')
                            ->where("hp.id_lab = $id_laboratorio")
                            ->where("hp.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_h_productos = $h_productos[0];

            $obj_productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->where("pd.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
            $productos = $obj_productos[0];

            $data[0] = array(
                "id_producto" => $id_producto,
                "codigo" => $productos->codigo,
                "nombre" => $productos->nombre,
                "descripcion" => $productos->descripcion,
                "um" => $obj_um->unidad_medida,
                "plan_prod_anual" => $obj_plan_consumo_producto->plan_produccion,
                "cant_lab" => $obj_h_productos->cant_lab,
                "cant_almacen" => $obj_h_productos->cant_almacen,
                "existencia_total" => $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab,
            );
            
        } 
        else 
        {
            $productos = $this->productos_lab($id_laboratorio, $formaFarmaceutica);
            for ($i = 0; $i < count($productos); $i++) {

                $um = $this->db->select('um.*')
                                ->from('nomencladores.nom_unidadmedida AS um')
                                ->where("um.id_um", $productos[$i]['id_um'])
                                ->limit(1)
                                ->get()->result();
                $obj_um = $um [0];
                
                $plan_consumo = $this->db->select('planconsm_lab.*')
                            ->from('datos.planconsumo_lab AS planconsm_lab')
                            ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                            ->where("planconsm_lab.id_lab", $id_laboratorio)
                            ->where("pd_planconsm.id_producto", $productos[$i]['id_producto'])
                            ->limit(1)
                            ->get()->result();
               $obj_plan_consumo_producto = $plan_consumo[0];
               
              $h_productos = $this->db->select('hp.*')
                                ->from('datos.historial_producto AS hp')
                                ->where("hp.id_lab = $id_laboratorio")
                                ->where("hp.id_producto", $productos[$i]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_h_productos = $h_productos[0];

                $data[$i] = array(
                    "id_producto" => $productos[$i]['id_producto'],
                    "codigo" => $productos[$i]['codigo'],
                    "nombre" => $productos[$i]['nombre'],
                    "descripcion" => $productos[$i]['descripcion'],
                    "um" => $obj_um->unidad_medida,
                    "plan_prod_anual" => $obj_plan_consumo_producto->plan_produccion,
                    "cant_lab" => $obj_h_productos->cant_lab,
                    "cant_almacen" => $obj_h_productos->cant_almacen,
                    "existencia_total" => $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab,
                );
            }
            
        }
        return $data;
    }

    public function materias_primas_lab($id_laboratorio, $formaFarmaceutica) 
    {
        $this->db = DB::load('gestion', TRUE);
        if ($formaFarmaceutica != NULL) {
            $productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->join('datos.materia_prima AS mp', 'mp.id_producto = pd.id_producto', 'inner')
                            ->join('datos.historial_producto AS hp', 'hp.id_producto = pd.id_producto', 'inner')
                            ->where("hp.id_lab = $id_laboratorio")
                            ->where("pd.id_linea = $formaFarmaceutica")
                            ->get()->result_array();
            return $productos;
        } else {
            $productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->join('datos.materia_prima AS mp', 'mp.id_producto = pd.id_producto', 'inner')
                            ->join('datos.historial_producto AS hp', 'hp.id_producto = pd.id_producto', 'inner')
                            ->where("hp.id_lab = $id_laboratorio")
                            ->get()->result_array();
            return $productos;
        }
    }

    public function nombre_materias_primas($id_laboratorio, $id_producto, $formaFarmaceutica) {
        $this->db = DB::load('gestion', TRUE);
        $on = 'on';
        if ($id_producto != NULL) {
            $um = $this->db->select('um.*')
                            ->from('nomencladores.nom_unidadmedida AS um')
                            ->join('datos.productos AS pd', 'pd.id_um = um.id_um', 'inner')
                            ->where("pd.id_producto = $id_producto")
                            ->limit(1)
                            ->get()->result();
            $obj_um = $um [0];

            $h_productos = $this->db->select('hp.*')
                            ->from('datos.historial_producto AS hp')
                            ->where("hp.id_lab = $id_laboratorio")
                            ->where("hp.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_h_productos = $h_productos[0];

            $obj_productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->where("pd.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
            $productos = $obj_productos[0];

            $data[0] = array(
                "id_producto" => $id_producto,
                "codigo" => $productos->codigo,
                "nombre" => $productos->nombre,
                "descripcion" => $productos->descripcion,
                "um" => $obj_um->unidad_medida,
                "cant_lab" => $obj_h_productos->cant_lab,
                "cant_almacen" => $obj_h_productos->cant_almacen,
                "existencia_total" => $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab,
            );
            return $data;
        } else {
            $productos = $this->materias_primas_lab($id_laboratorio, $formaFarmaceutica);
            for ($i = 0; $i < count($productos); $i++) {

                $um = $this->db->select('um.*')
                                ->from('nomencladores.nom_unidadmedida AS um')
                                ->where("um.id_um", $productos[$i]['id_um'])
                                ->limit(1)
                                ->get()->result();
                $obj_um = $um [0];

                $h_productos = $this->db->select('hp.*')
                                ->from('datos.historial_producto AS hp')
                                ->where("hp.id_lab = $id_laboratorio")
                                ->where("hp.id_producto", $productos[$i]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_h_productos = $h_productos[0];

                $data[$i] = array(
                    "id_producto" => $productos[$i]['id_producto'],
                    "codigo" => $productos[$i]['codigo'],
                    "nombre" => $productos[$i]['nombre'],
                    "descripcion" => $productos[$i]['descripcion'],
                    "um" => $obj_um->unidad_medida,
                    "cant_lab" => $obj_h_productos->cant_lab,
                    "cant_almacen" => $obj_h_productos->cant_almacen,
                    "existencia_total" => $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab,
                );
            }
            return $data;
        }
    }

    public function materias_primas_producto($id_laboratorio, $id_producto) {

        $this->db = DB::load('gestion', TRUE);
        $on = 'on';

        $producto_terminado = $this->db->select('pdt.*')
                        ->from('datos.producto_terminado AS pdt')
                        ->join('datos.productos AS pd', 'pd.id_producto = pdt.id_producto', 'inner')
                        ->where("pdt.id_producto ", $id_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_producto_terminado = $producto_terminado[0];
        $id_producto_terminado = $obj_producto_terminado->id_prod_terminado;

        $formula_farma = $this->db->select('formula_far.*')
                        ->from('nomencladores.nom_formulafarmaceutica AS formula_far')
                        ->where("formula_far.id_prod_terminado", $id_producto_terminado)
                        ->where("formula_far.predeterminada", $on)
                        ->limit(1)
                        ->get()->result();
        $obj_formula_farma = $formula_farma[0];
        $id_formula = $obj_formula_farma->id_formula;

        $plan_consumo = $this->db->select('planconsm_lab.*')
                        ->from('datos.planconsumo_lab AS planconsm_lab')
                        ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                        ->where("planconsm_lab.id_lab", $id_laboratorio)
                        ->where("pd_planconsm.id_producto", $id_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_plan_consumo = $plan_consumo[0];

        $materias_primas_formula = $this->db->select('frm_mp.*')
                        ->from('datos.formulafarma_materiaprima AS frm_mp')
                        ->join('datos.materia_prima AS mp', 'mp.id_materiaprima = frm_mp.id_materiaprima', 'inner')
                        ->where("frm_mp.id_formula", $id_formula)
                        ->get()->result_array();

        for ($i = 0; $i < count($materias_primas_formula); $i++) {

            $materias_primas = $this->db->select('mp.*')
                            ->from('datos.materia_prima AS mp')
                            ->join('datos.formulafarma_materiaprima AS frm_mp', 'frm_mp.id_materiaprima = mp.id_materiaprima', 'inner')
                            ->where("frm_mp.id_materiaprima", $materias_primas_formula[$i]['id_materiaprima'])
                            ->limit(1)
                            ->get()->result();

            $obj_materias_primas = $materias_primas[0];

            $indice_consumo = $materias_primas_formula[$i]['indice_consumo'];
            $cantidad_productos_formula = $obj_formula_farma->cant_productos;
            $plan_consumo_producto = $obj_plan_consumo->plan_produccion;

            $cantidad_necesaria = (int)(( $plan_consumo_producto * $indice_consumo) / $cantidad_productos_formula);

            $um = $this->db->select('um.*')
                            ->from('nomencladores.nom_unidadmedida AS um')
                            ->join('datos.productos AS pd', 'pd.id_um = um.id_um', 'inner')
                            ->where("pd.id_producto ", $obj_materias_primas->id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_um = $um [0];

            $h_productos = $this->db->select('hp.*')
                            ->from('datos.historial_producto AS hp')
                            ->where("hp.id_lab ", $id_laboratorio)
                            ->where("hp.id_producto", $obj_materias_primas->id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_h_productos = $h_productos[0];

            $obj_productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->where("pd.id_producto", $obj_materias_primas->id_producto)
                            ->limit(1)
                            ->get()->result();
            $productos = $obj_productos[0];

            $data[$i] = array(
                "id_producto" => $obj_materias_primas->id_producto,
                "codigo" => $productos->codigo,
                "nombre" => $productos->nombre,
                "descripcion" => $productos->descripcion,
                "um" => $obj_um->unidad_medida,
                "cant_necesaria" => $cantidad_necesaria,
                "cant_lab" => $obj_h_productos->cant_lab,
                "cant_almacen" => $obj_h_productos->cant_almacen,
                "existencia_total" => $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab,
                "total_fina" => (int)(($obj_h_productos->cant_almacen + $obj_h_productos->cant_lab) - $cantidad_necesaria),
            );
        }
        return $data;
    }

    public function cantidad_mp_nec($id_laboratorio, $id_producto) {

        $this->db = DB::load('gestion', TRUE);
        $predeterminada = 'on';
        $materia_prima = $this->db->select('mp.*')
                        ->from('datos.materia_prima AS mp')
                        ->join('datos.productos AS pd', 'pd.id_producto = mp.id_producto', 'inner')
                        ->where("pd.id_producto", $id_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_materia_prima = $materia_prima[0];
        $id_materia_prima = $obj_materia_prima->id_materiaprima;

        $productos_terminados = $this->db->select('pdt.*')
                        ->from('datos.producto_terminado AS pdt ')
                        ->join('datos.productos AS pd', 'pd.id_producto = pdt.id_producto', 'inner')
                        ->join('datos.historial_producto AS hp', 'hp.id_producto = pd.id_producto', 'inner')
                        ->where("hp.id_lab = $id_laboratorio")
                        ->get()->result_array();

        for ($j = 0; $j < count($productos_terminados); $j++) {

            $formula_farma = $this->db->select('formula_far.*')
                            ->from('nomencladores.nom_formulafarmaceutica AS formula_far')
                            ->where("formula_far.id_prod_terminado", $productos_terminados[$j]['id_prod_terminado'])
                            ->where("formula_far.predeterminada", $predeterminada)
                            ->limit(1)
                            ->get()->result();
            $obj_formula_farma = $formula_farma[0];
            $id_formula = $obj_formula_farma->id_formula;
            
            $plan_consumo = $this->db->select('planconsm_lab.*')
                        ->from('datos.planconsumo_lab AS planconsm_lab')
                        ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                        ->where("planconsm_lab.id_lab", $id_laboratorio)
                        ->where("pd_planconsm.id_producto", $productos_terminados[$j]['id_producto'])
                        ->limit(1)
                        ->get()->result();
            $obj_plan_consumo = $plan_consumo[0];
           
            $materias_primas_formula = $this->db->select('frm_mp.*')
                            ->from('datos.formulafarma_materiaprima AS frm_mp')
                            ->join('datos.materia_prima AS mp', 'mp.id_materiaprima = frm_mp.id_materiaprima', 'inner')
                            ->where("frm_mp.id_formula", $id_formula)
                            ->where("frm_mp.id_materiaprima", $id_materia_prima)
                            ->limit(1)
                            ->get()->result();
            if (!empty($materias_primas_formula)) {
                $obj_materias_primas = $materias_primas_formula[0];

                $indice_consumo = $obj_materias_primas->indice_consumo;
                $cantidad_productos_formula = $obj_formula_farma->cant_productos;
                $plan_consumo_producto = $obj_plan_consumo->plan_produccion;
                $cantidad_elab = (int)(( $plan_consumo_producto * $indice_consumo) / $cantidad_productos_formula);
                $total+= ( $plan_consumo_producto * $indice_consumo) / $cantidad_productos_formula;

                $productos = $this->db->select('pd.*')
                                ->from('datos.productos AS pd')
                                ->where("pd.id_producto", $productos_terminados[$j]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_productos = $productos[0];
                $hist_productos = $this->db->select('hp.*')
                                ->from('datos.historial_producto AS hp')
                                ->where("hp.id_producto", $productos_terminados[$j]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_productos_hist = $hist_productos[0];
                $um = $this->db->select('um.*')
                                ->from('nomencladores.nom_unidadmedida AS um')
                                ->join('datos.productos AS pd', 'pd.id_um = um.id_um', 'inner')
                                ->where("pd.id_producto ", $productos_terminados[$j]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_um = $um [0];
                $data[$j] = array(
                    "id_producto" => $obj_productos->id_producto,
                    "codigo" => $obj_productos->codigo,
                    "nombre" => $obj_productos->nombre,
                    "descripcion" => $obj_productos->descripcion,
                    "um" => $obj_um->unidad_medida,
                    "cant_elab" => $cantidad_elab,
                    "plan_prod_anual" => $plan_consumo_producto,
                    "cant_lab" => $obj_productos_hist->cant_lab,
                    "cant_almacen" => $obj_productos_hist->cant_almacen,
                    "existencia_total" => $obj_productos_hist->cant_almacen + $obj_productos_hist->cant_lab,
                );
            }
        }
        return $datos = array(0 => $data, 1 => $total);
    }
    
     public function materias_primas_producto_plan_consumo($id_laboratorio, $id_producto) {

        $this->db = DB::load('gestion', TRUE);
        $on = 'on';

        $producto_terminado = $this->db->select('pdt.*')
                        ->from('datos.producto_terminado AS pdt')
                        ->join('datos.productos AS pd', 'pd.id_producto = pdt.id_producto', 'inner')
                        ->where("pdt.id_producto ", $id_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_producto_terminado = $producto_terminado[0];
        $id_producto_terminado = $obj_producto_terminado->id_prod_terminado;

        $formula_farma = $this->db->select('formula_far.*')
                        ->from('nomencladores.nom_formulafarmaceutica AS formula_far')
                        ->where("formula_far.id_prod_terminado", $id_producto_terminado)
                        ->where("formula_far.predeterminada", $on)
                        ->limit(1)
                        ->get()->result();
        $obj_formula_farma = $formula_farma[0];
        $id_formula = $obj_formula_farma->id_formula;

        $plan_consumo = $this->db->select('planconsm_lab.*')
                        ->from('datos.planconsumo_lab AS planconsm_lab')
                        ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                        ->where("planconsm_lab.id_lab", $id_laboratorio)
                        ->where("pd_planconsm.id_producto", $id_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_plan_consumo = $plan_consumo[0];
        
        $h_productos1 = $this->db->select('hp.*')
                            ->from('datos.historial_producto AS hp')
                            ->where("hp.id_lab ", $id_laboratorio)
                            ->where("hp.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
         $obj_h_productos1 = $h_productos1[0];

        $materias_primas_formula = $this->db->select('frm_mp.*')
                        ->from('datos.formulafarma_materiaprima AS frm_mp')
                        ->join('datos.materia_prima AS mp', 'mp.id_materiaprima = frm_mp.id_materiaprima', 'inner')
                        ->where("frm_mp.id_formula", $id_formula)
                        ->get()->result_array();

        for ($i = 0; $i < count($materias_primas_formula); $i++) 
        {

            $materias_primas = $this->db->select('mp.*')
                            ->from('datos.materia_prima AS mp')
                            ->join('datos.formulafarma_materiaprima AS frm_mp', 'frm_mp.id_materiaprima = mp.id_materiaprima', 'inner')
                            ->where("frm_mp.id_materiaprima", $materias_primas_formula[$i]['id_materiaprima'])
                            ->limit(1)
                            ->get()->result();

            $obj_materias_primas = $materias_primas[0];

            $um = $this->db->select('um.*')
                            ->from('nomencladores.nom_unidadmedida AS um')
                            ->join('datos.productos AS pd', 'pd.id_um = um.id_um', 'inner')
                            ->where("pd.id_producto ", $obj_materias_primas->id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_um = $um [0];

            $h_productos = $this->db->select('hp.*')
                            ->from('datos.historial_producto AS hp')
                            ->where("hp.id_lab ", $id_laboratorio)
                            ->where("hp.id_producto", $obj_materias_primas->id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_h_productos = $h_productos[0];

            $obj_productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->where("pd.id_producto", $obj_materias_primas->id_producto)
                            ->limit(1)
                            ->get()->result();
            $productos = $obj_productos[0];
            
            $existencia_total = $obj_h_productos1->cant_almacen + $obj_h_productos1->cant_lab;
            $plan_real = $obj_plan_consumo->plan_produccion - $existencia_total;
            
            $indice_consumo = $materias_primas_formula[$i]['indice_consumo'];
            $cantidad_productos_formula = $obj_formula_farma->cant_productos;
            
            $cantidad_utilizar = (int)(( $plan_real * $indice_consumo) / $cantidad_productos_formula);
            
            $totalfinal =  (int)($existencia_total - $cantidad_utilizar);
            $data[$i] = array(
                "id_producto" => $obj_materias_primas->id_producto,
                "codigo" => $productos->codigo,
                "nombre" => $productos->nombre,
                "descripcion" => $productos->descripcion,
                "um" => $obj_um->unidad_medida,
                "cant_utilizar" => $cantidad_utilizar,
                "cant_lab" => $obj_h_productos->cant_lab,
                "cant_almacen" => $obj_h_productos->cant_almacen,
                "existencia_total" => $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab,
                "total_final" =>  $totalfinal
            );
        }
        return $data;
    }

    public function cantidad_mp_nec_plan_consumo($id_laboratorio, $id_producto) 
    {

        $this->db = DB::load('gestion', TRUE);
        $predeterminada = 'on';
        $materia_prima = $this->db->select('mp.*')
                        ->from('datos.materia_prima AS mp')
                        ->join('datos.productos AS pd', 'pd.id_producto = mp.id_producto', 'inner')
                        ->where("pd.id_producto", $id_producto)
                        ->limit(1)
                        ->get()->result();
        $obj_materia_prima = $materia_prima[0];
        $id_materia_prima = $obj_materia_prima->id_materiaprima;

        $productos_terminados = $this->db->select('pdt.*')
                        ->from('datos.producto_terminado AS pdt ')
                        ->join('datos.productos AS pd', 'pd.id_producto = pdt.id_producto', 'inner')
                        ->join('datos.historial_producto AS hp', 'hp.id_producto = pd.id_producto', 'inner')
                        ->where("hp.id_lab = $id_laboratorio")
                        ->get()->result_array();

        for ($j = 0; $j < count($productos_terminados); $j++) 
        {

            $formula_farma = $this->db->select('formula_far.*')
                            ->from('nomencladores.nom_formulafarmaceutica AS formula_far')
                            ->where("formula_far.id_prod_terminado", $productos_terminados[$j]['id_prod_terminado'])
                            ->where("formula_far.predeterminada", $predeterminada)
                            ->limit(1)
                            ->get()->result();
            $obj_formula_farma = $formula_farma[0];
            $id_formula = $obj_formula_farma->id_formula;

            $plan_consumo = $this->db->select('planconsm_lab.*')
                            ->from('datos.planconsumo_lab AS planconsm_lab')
                            ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                            ->where("planconsm_lab.id_lab", $id_laboratorio)
                            ->where("pd_planconsm.id_producto", $productos_terminados[$j]['id_producto'])
                            ->limit(1)
                            ->get()->result();
            $obj_plan_consumo_producto = $plan_consumo[0];

            $materias_primas_formula = $this->db->select('frm_mp.*')
                            ->from('datos.formulafarma_materiaprima AS frm_mp')
                            ->join('datos.materia_prima AS mp', 'mp.id_materiaprima = frm_mp.id_materiaprima', 'inner')
                            ->where("frm_mp.id_formula", $id_formula)
                            ->where("frm_mp.id_materiaprima", $id_materia_prima)
                            ->limit(1)
                            ->get()->result();
            if (!empty($materias_primas_formula)) 
            {
                $obj_materias_primas = $materias_primas_formula[0];

                $productos = $this->db->select('pd.*')
                                ->from('datos.productos AS pd')
                                ->where("pd.id_producto", $productos_terminados[$j]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_productos = $productos[0];
                $hist_productos = $this->db->select('hp.*')
                                ->from('datos.historial_producto AS hp')
                                ->where("hp.id_producto", $productos_terminados[$j]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_productos_hist = $hist_productos[0];
                
                $indice_consumo = $obj_materias_primas->indice_consumo;
                $cantidad_productos_formula = $obj_formula_farma->cant_productos;
                $existencia_total = $obj_productos_hist->cant_almacen + $obj_productos_hist->cant_lab;
                $plan_real = $obj_plan_consumo_producto->plan_produccion - $existencia_total;
                
                $cantidad_elab = (int)(( $plan_real * $indice_consumo) / $cantidad_productos_formula);
                $total += $cantidad_elab;
                
                $um = $this->db->select('um.*')
                                ->from('nomencladores.nom_unidadmedida AS um')
                                ->join('datos.productos AS pd', 'pd.id_um = um.id_um', 'inner')
                                ->where("pd.id_producto ", $productos_terminados[$j]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_um = $um [0];
                $data[$j] = array(
                    "id_producto" => $obj_productos->id_producto,
                    "codigo" => $obj_productos->codigo,
                    "nombre" => $obj_productos->nombre,
                    "descripcion" => $obj_productos->descripcion,
                    "um" => $obj_um->unidad_medida,
                    "cant_elab" => $cantidad_elab,
                    "plan_prod_real" => $plan_real,
                    "cant_lab" => $obj_productos_hist->cant_lab,
                    "cant_almacen" => $obj_productos_hist->cant_almacen,
                    "existencia_total" => $existencia_total,
                    "total" => $total);
                    
            }
        }
        return $datos = array(0 => $data, 1 => $total);
    }


   public function nombre_productos_plan_consumo($id_laboratorio, $formaFarmaceutica, $id_producto)
   {
        $this->db = DB::load('gestion', TRUE);
        if ($id_producto != NULL) 
        {
            $um = $this->db->select('um.*')
                            ->from('nomencladores.nom_unidadmedida AS um')
                            ->join('datos.productos AS pd', 'pd.id_um = um.id_um', 'inner')
                            ->where("pd.id_producto = $id_producto")
                            ->limit(1)
                            ->get()->result();
            $obj_um = $um [0];

            $plan_consumo = $this->db->select('planconsm_lab.*')
                            ->from('datos.planconsumo_lab AS planconsm_lab')
                            ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                            ->where("planconsm_lab.id_lab", $id_laboratorio)
                            ->where("pd_planconsm.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_plan_consumo_producto = $plan_consumo[0];

            $h_productos = $this->db->select('hp.*')
                            ->from('datos.historial_producto AS hp')
                            ->where("hp.id_lab = $id_laboratorio")
                            ->where("hp.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
            $obj_h_productos = $h_productos[0];

            $obj_productos = $this->db->select('pd.*')
                            ->from('datos.productos AS pd')
                            ->where("pd.id_producto", $id_producto)
                            ->limit(1)
                            ->get()->result();
            $productos = $obj_productos[0];
            $existencia_total = $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab;
            $data[0] = array(
                "id_producto" => $id_producto,
                "codigo" => $productos->codigo,
                "nombre" => $productos->nombre,
                "descripcion" => $productos->descripcion,
                "um" => $obj_um->unidad_medida,
                "plan_prod_real" => $obj_plan_consumo_producto->plan_produccion - $existencia_total,
                "cant_lab" => $obj_h_productos->cant_lab,
                "cant_almacen" => $obj_h_productos->cant_almacen,
                "existencia_total" => $existencia_total,
            );
           
        } 
        else 
        {
            $productos = $this->productos_lab($id_laboratorio, $formaFarmaceutica);
            for ($i = 0; $i < count($productos); $i++) {

                $um = $this->db->select('um.*')
                                ->from('nomencladores.nom_unidadmedida AS um')
                                ->where("um.id_um", $productos[$i]['id_um'])
                                ->limit(1)
                                ->get()->result();
                $obj_um = $um [0];

                $plan_consumo = $this->db->select('planconsm_lab.*')
                            ->from('datos.planconsumo_lab AS planconsm_lab')
                            ->join('datos.producto_planconsumo AS pd_planconsm', 'pd_planconsm.id_planconsumo = planconsm_lab.id_planconsumo', 'inner')
                            ->where("planconsm_lab.id_lab", $id_laboratorio)
                            ->where("pd_planconsm.id_producto", $productos[$i]['id_producto'])
                            ->limit(1)
                            ->get()->result();
                $obj_plan_consumo_producto = $plan_consumo[0];

                $h_productos = $this->db->select('hp.*')
                                ->from('datos.historial_producto AS hp')
                                ->where("hp.id_lab = $id_laboratorio")
                                ->where("hp.id_producto", $productos[$i]['id_producto'])
                                ->limit(1)
                                ->get()->result();
                $obj_h_productos = $h_productos[0];
                
                $existencia_total = $obj_h_productos->cant_almacen + $obj_h_productos->cant_lab;
                
                $data[$i] = array(
                    "id_producto" => $productos[$i]['id_producto'],
                    "codigo" => $productos[$i]['codigo'],
                    "nombre" => $productos[$i]['nombre'],
                    "descripcion" => $productos[$i]['descripcion'],
                    "um" => $obj_um->unidad_medida,
                    "plan_prod_real" => $obj_plan_consumo_producto->plan_produccion - $existencia_total,
                    "cant_lab" => $obj_h_productos->cant_lab,
                    "cant_almacen" => $obj_h_productos->cant_almacen,
                    "existencia_total" => $existencia_total,
                    "total_final" => $existencia_total - $cantidad_necesaria,
                );
            }
           
        }
         return $data;
  }
}
