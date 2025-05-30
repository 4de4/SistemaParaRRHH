<?php
class ContratoController {		
    private $db;
    private $contrato;
    private $change;
    
    public function __construct(){
        require_once "../models/ContratoModel.php";
        $this->contrato=new ContratoModel();
        $this->db = Conectar::conexion();
        $this->change = get_class($this->db);
    }	

    public function index(){

        $contrato = new ContratoModel();
        //creando la variable titulo en el arreglo data:
        $data["titulo_c"] = "Contrato";
        $data["contrato"] = $contrato->get_contrato();
        if ($this->change=='PDO') {
            $cafe["comprobar"] = "Estas trabajando con PDO"; 	
        } elseif ($this->change=='mysqli') {
            $cafe["comprobar"] = "Estas trabajando con mysqli";     		
        }else{
            $cafe["comprobar"] = "Error";
        }
        require_once "../views/empleados/contratos.php";
    }

}