<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// require_once "../helpers.php"; // Si verificarAcceso no está global

class ConfiguracionController {

    public function __construct() {
        // No se necesita modelo para esta simple configuración de sesión
        verificarAcceso(['jefe']); // Solo el 'jefe' o 'admin' puede cambiar esto
    }

    /**
     * Muestra el formulario de configuración de conexión.
     */
    public function index() {
        $data["titulo"] = "Configuración del Sistema";
        // No se necesitan más datos para la vista en este caso,
        // ya que la vista lee directamente de $_SESSION['db_driver']
        require_once "../views/configuracion/config_conexion.php";
    }

    /**
     * Guarda la preferencia del driver de conexión en la sesión.
     */
    public function guardarConexion() {
        if (isset($_POST['conexion_driver'])) {
            $driverSeleccionado = $_POST['conexion_driver'];
            if ($driverSeleccionado === 'PDO' || $driverSeleccionado === 'mysqli') {
                $_SESSION['db_driver'] = $driverSeleccionado;
                $_SESSION['mensaje_config'] = "Configuración de conexión guardada: " . strtoupper($driverSeleccionado);
            } else {
                $_SESSION['mensaje_config_error'] = "Opción de driver no válida."; // Podrías mostrar este error
            }
        } else {
            $_SESSION['mensaje_config_error'] = "No se seleccionó ningún driver.";
        }

        // Redirigir de nuevo a la página de configuración para ver el cambio y el mensaje
        header("Location: crud.php?c=configuracion&a=index");
        exit();
    }

    // Podrías añadir otras acciones de configuración aquí en el futuro
}
?>