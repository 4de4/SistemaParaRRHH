<?php 
    
    $servidor='localhost';
    $usuario='root';
    $pass='';
    $bd='rr_hh';

    $con=new mysqli($servidor,$usuario,$pass,$bd);

    if($con->connect_errno){
        echo "Error al conectarse {$con->errno}";
    }else {
        echo "Conexión exitosa a la base de datos '{$bd}'\n";
    }
?>