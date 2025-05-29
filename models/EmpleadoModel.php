<?php
class EmpleadoModel {		
    //definir atributos:
    private $db;
    private $empleado;
    private $change;

    public function __construct(){
	//usando atributo db para trabajar con el método conexión 
    //que esta en la carpeta config/database.php
    $this->db = Conectar::conexion();
	$this->empleado = array();
    //change esta almacenando con que conexion se esta trabajando(PDO o mysqli)
    $this->change = get_class($this->db);
    }		

    public function get_empleado()
	{
        if ($this->change=='PDO') {
        $pst=$this->db->prepare("SELECT e.*, c.*, d.* FROM empleado e INNER JOIN contrato c ON
             e.id_e = c.id_c INNER JOIN departamento d ON e.id_e = d.id_d");
            $pst->execute();
            $resultados=$pst->fetchAll(\PDO::FETCH_ASSOC);

            foreach($resultados as $resultado)
            {
                $this->empleado[]=$resultado;
            }
            
        }elseif ($this->change=='mysqli') {
            $pst=$this->db->prepare("SELECT e.*, c.*, d.* FROM empleado e INNER JOIN contrato c ON
             e.id_e = c.id_c INNER JOIN departamento d ON e.id_e = d.id_d");
            $pst->execute();
            $res=$pst->get_result();
            $resultados=$res->fetch_All(MYSQLI_ASSOC);
            foreach($resultados as $resultado){
                $this->empleado[]=$resultado;
            }
        }
            /*$sql = "SELECT e.*, c.*, d.* FROM empleado e INNER JOIN contrato c ON
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
            }*/
            return $this->empleado;
	}

    //METODO INSERTAR
    public function set_Empleado($nombre,$apellido,$foto,$fecha_nacimiento,$fecha_inicio,$fecha_fin,$salario_base,$nombre_d,$ubicacion){			
		$dif_destino='/SistemaParaRRHH/views/fotos';
        $foto=$dif_destino.basename($_FILES['foto']['name']);

        if($this->change=='PDO'){

            $pst_e=$this->db->prepare("INSERT INTO empleado(nombre, apellido, fecha_nacimiento, foto)VALUES(?,?,?,?)");
            $pst_e->bindParam(1,$nombre);
            $pst_e->bindParam(2,$apellido);
            $pst_e->bindParam(3,$fec_nac);
            $pst_e->bindParam(4,$foto);
            $resultado_emp=$pst_e->execute();

            if ($resultado_emp) {
                // Obtiene el último ID insertado
                $idEmpleado = $this->db->insert_id;

                $pst_c=$this->db->prepare("INSERT INTO contrato(id_c, fecha_inicio, fecha_fin, salario_base)VALUES(?,?,?,?)");
                $pst_c->bindParam(1,$idEmpleado);
                $pst_c->bindParam(2,$fecha_inicio);
                $pst_c->bindParam(3,$fecha_fin);
                $pst_c->bindParam(4,$salario_base);
                $resultado_con=$pst_c->execute();
                
                $pst_d=$this->db->prepare("INSERT INTO departamento(id_d, nombre, ubicacion)VALUES(?,?,?)");
                $pst_d->bindParam(1,$idEmpleado);
                $pst_d->bindParam(2,$nombre_d);
                $pst_d->bindParam(3,$ubicacion);
                $resultado_dep=$pst_d->execute();
                if($resultado_con && $resultado_dep){
                return true;
                }
            }
             // Si algo falla, retorna false
            return false;
            // Cierra la conexión
            $this->db = null;
            
        }elseif($this->change=='mysqli'){

            $pst=$this->db->prepare("insert into empleado(nombre,apellido,fecha_nacimiento,foto)values(?,?,?,?)");
            $pst->bind_Param('ssdi',$nombre,$apellido,$descripcion,$precio,$stock);  
            $resultado=$pst->execute(); 

            $pst_e=$this->db->prepare("INSERT INTO empleado(nombre, apellido, fecha_nacimiento, foto)VALUES(?,?,?,?)");
            $pst_e->bind_Param('ssis',$nombre,$apellido,$fec_nac,$foto);
            $resultado_emp=$pst_e->execute();

            if ($resultado_emp) {
                // Obtiene el último ID insertado
                $idEmpleado = $this->db->insert_id;

                $pst_c=$this->db->prepare("INSERT INTO contrato(id_c, fecha_inicio, fecha_fin, salario_base)VALUES(?,?,?,?)");
                $pst_c->bind_Param('iiid',$idEmpleado,$fecha_inicio,$fecha_fin,$salario_base); 
                $resultado_con=$pst_c->execute();
                
                $pst_d=$this->db->prepare("INSERT INTO departamento(id_d, nombre, ubicacion)VALUES(?,?,?)");
                $pst_d->bind_Param('iss',$idEmpleado,$nombre_d,$ubicacion); 
                $resultado_dep=$pst_d->execute();
                if($resultado_con && $resultado_dep){
                return true;
                }
            }
             // Si algo falla, retorna false
            return false;
            // Cierra la conexión
            $this->db = null;
        }
    
        /*
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
            if($resultado_con && $resultado_dep){
                return true;
            }
        }
         // Si algo falla, retorna false
        return false;
        // Cierra la conexión
        $this->db = null;
        */
    }

}


