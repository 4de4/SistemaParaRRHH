<?php
    class Usuario{
        private $guardar;
        private $db;
        
        public function __construct(){
            $this->guardar=array();
            $this->db=new PDO('mysql:host=localhost; dbname=rr_hh', "root", "");
        }	

        public function Login($username){
            $sql = "select * from usuario where username = '$username'";
            $f = $this->db->query($sql);
            $this->guardar=$f->fetch(PDO::FETCH_ASSOC);
            $this->db=null;
            return $this->guardar;
        }

        public function Registrar($username,$password){
            $sql = "insert into usuario (username, password) values ('$username','$password')";
            $resultado = $this->db->query($sql);
            if($resultado){
                return true;
            }else{
                return false;
            }
            $this->db=null;

        }
    }
?>