<?php

// require_once("configuracion.php"); // <--- ELIMINA O COMENTA ESTA LÍNEA

// Es importante asegurarse de que la sesión esté iniciada ANTES de acceder a $_SESSION.
// Si no estás seguro de que ya se inició en otro lado (ej. crud.php o helpers.php),
// puedes añadirlo aquí de forma segura:
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// verifica si la variable db_driver fue declarada (si el usuario eligió pdo o mysqli)
// y si no se le asigna por defecto el valor de PDO
$driver = isset($_SESSION['db_driver']) ? $_SESSION['db_driver'] : 'PDO';

class Conectar {
    public static function conexion() {
        global $driver; // Es mejor pasar $driver como parámetro o que sea una propiedad de la clase
                        // pero por ahora mantenemos tu uso de global.
        $host="localhost";
        $dbname="rr_hh";
        $user="root";
        $pass=""; // Asegúrate que esta es tu contraseña de root, si tienes una.

        if($driver=="PDO"){
            try{
                $conexion=new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$user,$pass); // Añadido charset
                $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Opcional, pero útil
                return $conexion;
            }catch(PDOException $e){
                // En producción, no mostrar $e->getMessage() directamente. Loguear el error.
                error_log("Error de conexión PDO: " . $e->getMessage());
                die("Error al conectar con la base de datos. Intente más tarde.");
            }
        }elseif($driver == "mysqli"){
            $conexion = new mysqli($host,$user,$pass,$dbname);
            if ($conexion->connect_error){
                error_log("Error de conexión MySQLi: " . $conexion->connect_error);
                die("Error al conectar con la base de datos. Intente más tarde.");
            }
            $conexion->set_charset("utf8mb4"); // Añadido charset
            return $conexion;
        }else{
            error_log("Tipo de conexión no válido especificado: " . $driver);
            die("Tipo de conexión no válido. Contacte al administrador.");
        }
    }
}

// echo "Probando la conexión...\n";
// $conn = Conectar::conexion();
// if ($conn) {
//     echo "Conexión exitosa con " . (is_object($conn) ? get_class($conn) : 'desconocido') . " usando driver: " . $driver;
// } else {
//     echo "Fallo la conexión.";
// }
// $conn = null; // Cerrar para prueba
?>