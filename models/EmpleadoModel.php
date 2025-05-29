<?php
class Empleado_Model {		
    //definir atributos:
    private $db;
    private $empleado;		
    public function __construct(){
	//usando atributo db para trabajar con el método conexión 
        //que esta en la carpeta config/database.php
    $this->db = Conectar::conexion();
	$this->empleado = array();
    }		
    public function get_empleado()
	{
            $sql = "SELECT e.*, c.*, d.* FROM empleado e INNER JOIN contrato c ON
             e.id_e = c.id_c INNER JOIN departamento d ON e.id_e = d.id_d";
            $resultado = $this->db->query($sql);
            //La función fetch_assoc() en PHP se utiliza para obtener una fila 
            //de resultados de una consulta SQL como un arreglo asociativo, donde 
            //cada columna de la fila se convierte en una clave del arreglo, con 
            //el nombre de la columna como clave y el valor de la celda como el 
            //valor correspondiente.
            while($row = $resultado->fetch_assoc())
            {
            	$this->empleado[] = $row;
            }
            return $this->empleado;
	}
}


