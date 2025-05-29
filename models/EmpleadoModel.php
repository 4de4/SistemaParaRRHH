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

    //METODO INSERTAR
    public function set_Empleado($nombre,$apellido,$foto,$fecha_nacimiento,$fecha_inicio,$fecha_fin,$salario_base,$nombre_d,$ubicacion){			
		$dif_destino='/SistemaParaRRHH/views/fotos';
        $foto=$dif_destino.basename($_FILES['foto']['name']);

        $sql1=" INSERT INTO empleado (nombre, apellido, fecha_nacimiento, foto)
         VALUES ('$nombre', '$apellido', '$fecha_nacimiento', '$foto')";
        $resultado_emp=$this->db->query($sql1);
        
        if ($resultado_emp) {
            // Obtiene el último ID insertado
            $idEmpleado = $this->db->insert_id;

            // Inserta en la tabla contrato
            $sql2 = " INSERT INTO contrato (id_c, fecha_inicio, fecha_fin, salario_base) 
            VALUES ($idEmpleado, '$fecha_inicio','$fecha_fin',$salario_base)";
            $resultado_con = $this->db->query($sql2);

            $sql3 = " INSERT INTO departamento (id_d, nombre, ubicacion) 
            VALUES ($idEmpleado, '$nombre_d', '$ubicacion')";
            $resultado_dep = $this->db->query($sql3);

            // Retorna true solo si ambas inserciones fueron exitosas
            if ($resultado_con && $resultado_dep) {
                return true;
            }
        }

         // Si algo falla, retorna false
        return false;
        // Cierra la conexión
        $this->db = null;
    }



}


