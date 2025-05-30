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
        $pst=$this->db->prepare("SELECT * FROM empleado");
            $pst->execute();
            $resultados=$pst->fetchAll(\PDO::FETCH_ASSOC);

            foreach($resultados as $resultado)
            {
                $this->empleado[]=$resultado;
            }
            
        }elseif ($this->change=='mysqli') {
            $pst=$this->db->prepare("SELECT * FROM empleado");
            $pst->execute();
            $res=$pst->get_result();
            $resultados=$res->fetch_all(MYSQLI_ASSOC);
            foreach($resultados as $resultado){
                $this->empleado[]=$resultado;
            }
        }
          
            return $this->empleado;
	}
    //METODO INSERTAR
    public function set_Empleado($nombre,$apellido,$foto,$fecha_nacimiento){			
		$dif_destino='/SistemaParaRRHH/views/fotos/';
        $foto=$dif_destino.basename($_FILES['foto']['name']);

        if($this->change=='PDO'){

            $pst_e=$this->db->prepare("INSERT INTO empleado(nombre, apellido, fecha_nacimiento, foto)VALUES(?,?,?,?)");
            $pst_e->bindParam(1,$nombre);
            $pst_e->bindParam(2,$apellido);
            $pst_e->bindParam(3,$fecha_nacimiento);
            $pst_e->bindParam(4,$foto);
            $resultado_emp=$pst_e->execute();

            if ($resultado_emp) {
                return true;
            }else{
                return false;
            }
            // Cierra la conexión
            $this->db = null;
            
        }elseif($this->change=='mysqli'){

            $pst_e=$this->db->prepare("INSERT INTO empleado(nombre, apellido, fecha_nacimiento, foto)VALUES(?,?,?,?)");
            $pst_e->bind_Param('ssss',$nombre,$apellido,$fecha_nacimiento,$foto);
            $resultado_emp=$pst_e->execute();

            if ($resultado_emp) {
                return true;
            }else{
                return false;
            }
            // Cierra la conexión
            $this->db = null;
        }
    }

    //METODO MODIFICAR
    public function update_Empleado($id,$nombre,$apellido,$foto,$fecha_nacimiento,$fecha_inicio,$fecha_fin,$salario_base,$nombre_d,$ubicacion){			
		$dif_destino='/SistemaParaRRHH/views/fotos/';
        $foto=$dif_destino.basename($_FILES['foto']['name']);

        if($this->change=='PDO'){
                
            $pst_e=$this->db->prepare("update empleado set nombre=?,apellido=?,fecha_nacimiento=?,foto=? where id=?");
            $pst_e->bindParam(1,$nombre);
            $pst_e->bindParam(2,$apellido);
            $pst_e->bindParam(3,$fecha_nacimiento);
            $pst_e->bindParam(4,$foto);
            $pst_e->bindParam(5,$id);
            $resultado_emp=$pst_e->execute();

            if ($resultado_emp) {
                $pst_c=$this->db->prepare("update contrato set fecha_inicio=?,fecha_fin=?,salario_base=? where id=?");
                $pst_c->bindParam(1,$fecha_inicio);
                $pst_c->bindParam(2,$fecha_fin);
                $pst_c->bindParam(3,$salario_base);
                $pst_c->bindParam(4,$id);
                $resultado_con=$pst_c->execute();
                
                if($resultado_con){
                    $pst_d=$this->db->prepare("update departamento set nombre_d=?,ubicacion=? where id=?");
                    $pst_d->bindParam(1,$nombre_d);
                    $pst_d->bindParam(2,$ubicacion);
                    $pst_d->bindParam(3,$id);
                    $resultado_dep=$pst_d->execute();

                    if($resultado_con && $resultado_dep){
                        return true;
                    }
                }
                
            }else{
                return false;
            }
            
        }elseif($this->change=='mysqli'){
            $pst_e=$this->db->prepare("update empleado set nombre=?,apellido=?,fecha_nacimiento=?,foto=? where id=?");
            $pst_e->bind_Param('ssssi',$nombre,$apellido,$fecha_nacimiento,$foto,$id);
            $resultado_emp=$pst_e->execute();

            if ($resultado_emp){
                $pst_c=$this->db->prepare("update contrato set fecha_inicio=?,fecha_fin=?,salario_base=? where id=?");
                $pst_c->bind_Param('ssii',$fecha_inicio,$fecha_fin,$salario_base,$id); 
                $resultado_con=$pst_c->execute();

                if($resultado_con){
                    $pst_d=$this->db->prepare("update departamento set nombre_d=?,ubicacion=? where id=?");
                    $pst_d->bind_Param('ssi',$nombre_d,$ubicacion,$id); 
                    $resultado_dep=$pst_d->execute();

                    if($resultado_con && $resultado_dep){
                        return true;
                    }
                }
            }else{
                return false;
            }
        }
        $this->db = null;
    }

    //METODO ELIMINAR
    public function eliminar($id){

        if($this->change=='PDO'){
            $pst_c=$this->db->prepare("delete from contrato where id_c= ?");
            $pst_c->bindParam(1,$id);
            $resultado_con=$pst_c->execute();

            if ($resultado_con){
                $pst_d=$this->db->prepare("delete from departamento where id_d= ?");
                $pst_d->bindParam(1,$id);
                $resultado_dep=$pst_d->execute();
                
                if($resultado_dep){
                    $pst_e=$this->db->prepare("delete from empleado where id_e= ?");
                    $pst_e->bindParam(1,$id);
                    $resultado_emp=$pst_e->execute();

                    if($resultado_con && $resultado_emp){
                        return true;
                    }
                }
            }else{
                // Si algo falla, retorna false
                return false;
            }
            // Cierra la conexión
            $this->db = null;
            
        }elseif($this->change=='mysqli'){

            $pst_c=$this->db->prepare("delete from contrato where id_c= ?");
            $pst_c->bind_Param('i',$id);
            $resultado_con=$pst_c->execute();

            if ($resultado_con) {

                $pst_d=$this->db->prepare("delete from departamento where id_d= ?");
                $pst_d->bind_Param('i',$id); 
                $resultado_dep=$pst_d->execute();

                if($resultado_dep){
                    $pst_e=$this->db->prepare("delete from empleado where id_e= ?");
                    $pst_e->bind_Param('i',$id); 
                    $resultado_emp=$pst_e->execute();

                    if($resultado_con && $resultado_emp){
                        return true;
                    }
                }
            }else{
                // Si algo falla, retorna false
                return false;
            }
        }
        $this->db = null;
    }
}
