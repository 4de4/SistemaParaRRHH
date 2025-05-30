<?php
class ContratoModel {		
    //definir atributos:
    private $db;
    private $contrato;
    private $change;

    public function __construct(){
	//usando atributo db para trabajar con el método conexión 
    //que esta en la carpeta config/database.php
    $this->db = Conectar::conexion();
	$this->contrato = array();
    //change esta almacenando con que conexion se esta trabajando(PDO o mysqli)
    $this->change = get_class($this->db);
    }		

    public function get_contrato()
	{

        if (isset($_GET['id'])) {
            $id_e = $_GET['id']; // Obtienes el ID desde el URL
            //echo "El ID del empleado es: " . $id_e;
        } else {
            echo "No se proporcionó el ID del empleado en el URL.";
        }

        if ($this->change=='PDO') {
        $pst=$this->db->prepare("SELECT e.*, c.* FROM empleado e INNER JOIN contrato c ON
             e.id_e = c.id_c WHERE e.id_e = $id_e");
            $pst->execute();
            $resultados=$pst->fetchAll(\PDO::FETCH_ASSOC);

            foreach($resultados as $resultado)
            {
                $this->contrato[]=$resultado;
            }
            
        }elseif ($this->change=='mysqli') {
            $pst=$this->db->prepare("SELECT e.*, c.* FROM empleado e INNER JOIN contrato c ON
             e.id_e = c.id_c WHERE e.id_e = $id_e");
            $pst->execute();

            $res=$pst->get_result();
            $resultados=$res->fetch_All(MYSQLI_ASSOC);

            foreach($resultados as $resultado){
                $this->contrato[]=$resultado;
            }
        }
            return $this->contrato;
	}

}