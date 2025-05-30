<?php
// Asegúrate que session_start() se llama ANTES de cualquier uso de $_SESSION
// Si helpers.php lo tiene al inicio, y se incluye antes, está bien.
// O colócalo aquí si es más seguro:
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/define.php";
require_once "../core/routes.php";
require_once "../config/database.php";
require_once "../helpers.php"; // Asumiendo que está en la raíz (junto a index.php)

// Lista de controladores y acciones públicas (que no requieren login)
$acciones_publicas = [
    // Controlador 'usuario' (en minúsculas para comparación)
    'usuario' => [
        'iniciar',      // Acción para procesar el login
        'registrarse'   // Acción para procesar el formulario de registro
    ],
    // Podrías añadir otras si las tuvieras, ej: 'password' => ['solicitarReseteo', 'resetear']
];

$controlador_solicitado = isset($_GET['c']) ? strtolower($_GET['c']) : strtolower(CONTROLADOR_PRINCIPAL);
$accion_solicitada = isset($_GET['a']) ? strtolower($_GET['a']) : strtolower(ACCION_PRINCIPAL);

$es_accion_publica = false;
if (isset($acciones_publicas[$controlador_solicitado]) &&
    in_array($accion_solicitada, $acciones_publicas[$controlador_solicitado])) {
    $es_accion_publica = true;
}

// Si la acción NO es pública, entonces verificamos el acceso
if (!$es_accion_publica) {
    verificarAcceso(); // Proteger solo las rutas/acciones que no son públicas
}

// --- Resto del código de enrutamiento en crud.php ---
if(isset($_GET['c'])){
    $controlador = cargarControlador($_GET['c']); // El nombre original, no el minúsculas
    if(isset($_GET['a'])){
        $id = null; // Inicializar ID
        if(isset($_GET['id_u'])){ $id = $_GET['id_u']; }
        elseif(isset($_GET['id_e'])){ $id = $_GET['id_e']; } // Para empleados
        elseif(isset($_GET['id'])){ $id = $_GET['id']; } // Genérico
        cargarAccion($controlador, $_GET['a'], $id);
    } else {
        cargarAccion($controlador, ACCION_PRINCIPAL);
    }
} else {
    $controlador = cargarControlador(CONTROLADOR_PRINCIPAL);
    $accionTmp = ACCION_PRINCIPAL;
    $controlador->$accionTmp();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/fico.ico" type="imge/x-icon">
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-attachment: fixed;
            background-image: url("img/fondoazulgrande.jpg");
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        table{
            width: 90%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 30px;
            background-color: rgba(153, 191, 211, 0.8);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    <title>crud...</title>
</head>
<body>
    <?php
        require_once "../config/define.php";
        require_once "../core/routes.php";
        require_once "../config/database.php";
        
        //La función isset en PHP es una herramienta útil para verificar si una 
            //variable existe y no es NULL.
            // c significa el controlador y a significa la accion que realiza el usuario
            // c y a trabajan juntos.
        if(isset($_GET['c'])){//si existe el parametro "c"(controlador) 
                // El método cargarControlador esta en el archivo routes.php
                $controlador = cargarControlador($_GET['c']);//se crea el controlador
                if(isset($_GET['a'])){//si existe a(accion) se verifica si tiene id
                    if(isset($_GET['id_u'])){
                        cargarAccion($controlador, $_GET['a'], $_GET['id_u']); //para modificar o eliminar
                    }else{//existe "id" cargara la accion
                        cargarAccion($controlador, $_GET['a']); //para nuevo
                    }
                }else{
                    cargarAccion($controlador, ACCION_PRINCIPAL);
                }		
        }else{//sino se tiene c en la url se ejecuta el index		
            $controlador = cargarControlador(CONTROLADOR_PRINCIPAL);
            $accionTmp = ACCION_PRINCIPAL;
            $controlador->$accionTmp();
        }

    ?>
</body>
</html>

