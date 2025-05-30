<?php
    class Usuario{
        private $guardar;
        private $db;
        private $change;
        
        public function __construct(){
            require_once('../config/database.php');
            $this->guardar=array();
            $this->db = Conectar::conexion();
            $this->change = get_class($this->db);
        }	

        public function Login($username){
            if ($this->change==='PDO') {
                $pst=$this->db->prepare("select * from usuario where username = ?");
                $pst->bindParam(1, $username);  
                $pst->execute();
                $resultado=$pst->fetchAll(\PDO::FETCH_ASSOC);
                foreach($resultado as $resuelto){
                    $this->guardar[]=$resuelto;
                }
                $this->db=null;
                return $this->guardar;
            }elseif ($this->change==='mysqli'){
                $pst=$this->db->prepare("select * from usuario where username = ?");
                $pst->bind_param('s',$username);
                $pst->execute();
                $res=$pst->get_result();
                $resultado=$res->fetch_all(MYSQLI_ASSOC);
                foreach($resultado as $resuelto){
                $this->guardar[]=$resuelto;
                }
                $this->db=null;
                return $this->guardar;
            }
            return [];
        }

        public function Registrar($username,$password){
            if ($this->change==='PDO'){
                $pst=$this->db->prepare("insert into usuario (username, password)values(?,?)");
                $pst->bindParam(1,$username);
                $pst->bindParam(2,$password); 
                $resultado=$pst->execute();

                if($resultado){
                    return true;
                }else{
                    return false;
                }
            }elseif($this->change=='mysqli'){
                $pst=$this->db->prepare("insert into usuario (username, password)values(?,?)");
                $pst->bind_Param('ss',$username,$password); 
                $resultado=$pst->execute();

                if($resultado){
                    return true;
                }else{
                    return false;
                }
            }
            $this->db=null;
        }
    }