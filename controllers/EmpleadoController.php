<?php
class EmpleadoController {		
    private $db;
    private $empleado;
    private $change;
    
    public function __construct(){
        require_once "../models/EmpleadoModel.php";
        $this->empleado=new EmpleadoModel();
        $this->db = Conectar::conexion();
        $this->change = get_class($this->db);
    }		
    public function index(){			
        $empleado = new EmpleadoModel();
        //creando la variable titulo en el arreglo data:
        $data["titulo"] = "Empleados";
        $data["empleado"] = $empleado->get_empleado();
        if ($this->change=='PDO') {
            $cafe["comprobar"] = "Estas trabajando con PDO"; 	
        } elseif ($this->change=='mysqli') {
            $cafe["comprobar"] = "Estas trabajando con mysqli";     		
        }else{
            $cafe["comprobar"] = "Error";
        }
        require_once "../views/empleados/empleados.php";
    }

}