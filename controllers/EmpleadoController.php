<?php
class EmpleadoController {		
    public function __construct(){
        require_once "../models/EmpleadoModel.php";
    }		
    public function index(){			
        $empleado = new Empleado_Model();
        //creando la variable titulo en el arreglo data:
        $data["titulo"] = "Empleados";
        $data["empleado"] = $empleado->get_empleado();
        require_once "../views/empleados/empleados.php";
    }

}