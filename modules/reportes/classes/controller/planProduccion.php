<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_PlanProduccion extends Controller_Secured {

    public function before() {
        $this->gestion = Model::factory('ModelPlanProduccion');
    }

    public function action_index() {
        
    }

    public function action_nombre_formaFarmaceutica() 
    {
        $result = $this->gestion->nombre_formaFarmaceutica();
        $datosb = array('datos' => $result);
        echo json_encode($datosb);
    }

    public function action_productos_lab() 
    {

        $id_laboratorio = $this->request->post('id_laboratorio');
        $formaFarmaceutica = $this->request->post('formaFarmaceutica');

        $data = $this->gestion->productos_lab($id_laboratorio, $formaFarmaceutica);
        $paging = array('data' => $data);
        echo json_encode($paging);
    }

    public function action_nombre_productos() 
    {

        $id_laboratorio = $this->request->post('id_laboratorio');
        $formaFarmaceutica = $this->request->post('formaFarmaceutica');
        $id_producto = $this->request->post('id_producto');

        $data = $this->gestion->nombre_productos($id_laboratorio, $formaFarmaceutica, $id_producto);
        $paging = array('data' => $data);
        echo json_encode($paging);
    }

    public function action_materias_primas_lab() 
    {

        $id_laboratorio = $this->request->post('id_laboratorio');
        $data = $this->gestion->materias_primas_lab($id_laboratorio, $formaFarmaceutica);
        $paging = array('data' => $data);
        echo json_encode($paging);
    }

    public function action_nombre_materias_primas() 
    {

        $id_laboratorio = $this->request->post('id_laboratorio');
        $id_producto = $this->request->post('id_producto');
        $formaFarmaceutica = $this->request->post('formaFarmaceutica');

        $data = $this->gestion->nombre_materias_primas($id_laboratorio, $id_producto, $formaFarmaceutica);
        $paging = array('data' => $data);
        echo json_encode($paging);
    }

    public function action_materias_primas_producto() 
    {

        $id_laboratorio = $this->request->post('id_laboratorio');
        $id_producto = $this->request->post('id_producto');

        $data = $this->gestion->materias_primas_producto($id_laboratorio, $id_producto);
        $paging = array('data' => $data);
        echo json_encode($paging);
    }

    public function action_cantidad_mp_nec() 
    {
        session_start();
        $id_laboratorio = $this->request->post('id_laboratorio');
        $id_producto = $this->request->post('id_producto');

        $data = $this->gestion->cantidad_mp_nec($id_laboratorio, $id_producto);

        $_SESSION['total'] = $data[1];
        $paging = array('data' => $data[0]);
        echo json_encode($paging);
    }

    public function action_total() 
    {
        session_start();
        $total = $_SESSION['total'];
        
        $data = array(array('total' => $total));
        $info = $data;
        $info = array(
            'success' => true,
            'data' => $data[0]
        );
        echo json_encode($info);
    }
    
     public function action_total1() 
    {
        session_start();
        $total = $_SESSION['total1'];
        
        $data = array(array('total1' => $total));
        $info = $data;
        $info = array(
            'success' => true,
            'data' => $data[0]
        );
        echo json_encode($info);
    }
    
    public function action_materias_primas_producto_plan_consumo() 
    {

        $id_laboratorio = $this->request->post('id_laboratorio');
        $id_producto = $this->request->post('id_producto');

        $data = $this->gestion->materias_primas_producto_plan_consumo($id_laboratorio, $id_producto);
        $paging = array('data' => $data);
        echo json_encode($paging);
    }
    
    public function action_cantidad_mp_nec_plan_consumo()
    {
        session_start();
        $id_laboratorio = $this->request->post('id_laboratorio');
        $id_producto = $this->request->post('id_producto');

        $data = $this->gestion->cantidad_mp_nec_plan_consumo($id_laboratorio, $id_producto);

        $_SESSION['total1'] = $data[1];
        $paging = array('data' => $data[0]);
        echo json_encode($paging);
    }

    
    public function action_nombre_productos_plan_consumo() 
    {

        $id_laboratorio = $this->request->post('id_laboratorio');
        $formaFarmaceutica = $this->request->post('formaFarmaceutica');
        $id_producto = $this->request->post('id_producto');

        $data = $this->gestion->nombre_productos_plan_consumo($id_laboratorio, $formaFarmaceutica, $id_producto);
        $paging = array('data' => $data);
        echo json_encode($paging);
    }
    public function action_mostrar_reporte() {} 
    
}
