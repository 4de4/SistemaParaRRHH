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

    //FUNCION NUEVO
    public function nuevo(){
        $data["titulo"] = "Empleados";
        require_once "../views/empleados/empleados_nuevo.php";
    }

    //FUNCION GUARDA()
    public function guardaAct($id){

        if(((isset($_REQUEST['nombre']))&&($_REQUEST['nombre']!=''))&&((isset($_REQUEST['apellido']))&&($_POST['apellido']!=''))&&
        ((isset($_FILES['foto']))&&($_FILES['foto']['error'] == 0))&&((isset($_REQUEST['fecha_nacimiento']))&&($_REQUEST['fecha_nacimiento']!=''))
        &&((isset($_REQUEST['fecha_inicio']))&&($_REQUEST['fecha_inicio']!=''))&&((isset($_REQUEST['fecha_fin']))&&($_REQUEST['fecha_fin']!=''))
        &&((isset($_REQUEST['salario_base']))&&($_REQUEST['salario_base']!=''))&&((isset($_REQUEST['nombre_d']))&&($_REQUEST['nombre_d']!=''))
        &&((isset($_REQUEST['ubicacion']))&&($_REQUEST['ubicacion']!='')))
        {
            $empleado= new EmpleadoModel();
            $empleado->update_Empleado($id,$_REQUEST['nombre'],$_REQUEST['apellido'],$_FILES['foto'],$_REQUEST['fecha_nacimiento'],
            $_REQUEST['fecha_inicio'],$_REQUEST['fecha_fin'],$_REQUEST['salario_base'],$_REQUEST['nombre_d'],$_REQUEST['ubicacion']);
        }
        $data["titulo"] = "Empleados";
        $this->index();
    }

    public function guardaNew(){

        if(((isset($_REQUEST['nombre']))&&($_REQUEST['nombre']!=''))&&((isset($_REQUEST['apellido']))&&($_POST['apellido']!=''))&&
        ((isset($_FILES['foto']))&&($_FILES['foto']['error'] == 0))&&((isset($_REQUEST['fecha_nacimiento']))&&($_REQUEST['fecha_nacimiento']!=''))
        &&((isset($_REQUEST['fecha_inicio']))&&($_REQUEST['fecha_inicio']!=''))&&((isset($_REQUEST['fecha_fin']))&&($_REQUEST['fecha_fin']!=''))
        &&((isset($_REQUEST['salario_base']))&&($_REQUEST['salario_base']!=''))&&((isset($_REQUEST['nombre_d']))&&($_REQUEST['nombre_d']!=''))
        &&((isset($_REQUEST['ubicacion']))&&($_REQUEST['ubicacion']!='')))
        {
            $empleado= new EmpleadoModel();
            $empleado->set_Empleado($_REQUEST['nombre'],$_REQUEST['apellido'],$_FILES['foto'],$_REQUEST['fecha_nacimiento'],
            $_REQUEST['fecha_inicio'],$_REQUEST['fecha_fin'],$_REQUEST['salario_base'],$_REQUEST['nombre_d'],$_REQUEST['ubicacion']);
        }
        $data["titulo"] = "Empleados";
        $this->index();
    }

    //METODO MODIFICAR
    public function modificar($id){

			$dato["id"] = $id;
			$data = $this->empleado->get_empleado();
			$dato["titulo"] = "Actualizar";
            
            require_once "../views/empleados/empleado_modifica.php";
		}

    //METODO ELIMINAR
    public function eliminar($id){

        // if((isset($_GET[$id_e]))&&($_GET[$id_e]!='')){
        //     $empleado=new EmpleadoModel();
        //     $empleado->eliminar($_GET[$id_e]);
        //     $data["titulo"] = "Empleados";
        //     $this->index();
        // }

        $empleado = new EmpleadoModel();
        $empleado->eliminar($id);
        $data["titulo"] = "Empleados";
        $this->index();
	}

}