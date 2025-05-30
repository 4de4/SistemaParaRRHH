<?php
// Es buena práctica iniciar sesión al principio si se va a usar en múltiples métodos.
// Si ya lo haces en helpers.php o crud.php y se incluye antes, puedes omitirlo aquí.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función de validación (podría estar en un helper o ser un método privado)
function validateInput($data)
{
    $data = trim($data);
    $data = stripslashes($data); // Corregido: stripslashes en lugar de stripcslashes
    $data = htmlspecialchars($data);
    return $data;
}

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        require_once('../models/UsuarioModel.php');
        $this->usuarioModel = new Usuario(); // Instanciar el modelo
    }

    function iniciar()
    {
        if (isset($_POST['Username']) && isset($_POST['Password'])) {

            $username = validateInput($_POST['Username']);
            $claveIngresada = validateInput($_POST['Password']); // No hashear la clave ingresada aquí

            if (empty($username)) {
                header("location: ../index.php?error=El nombre de usuario es requerido");
                exit();
            } elseif (empty($claveIngresada)) {
                header("location: ../index.php?error=La contraseña es requerida");
                exit();
            } else {
                $usuariosEncontrados = $this->usuarioModel->Login($username);

                if ($usuariosEncontrados && count($usuariosEncontrados) > 0) {
                    $user = $usuariosEncontrados[0]; // Tomamos el primer usuario (debería ser único)

                    // Verificar la contraseña hasheada
                    if (password_verify($claveIngresada, $user['password'])) {
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['rol'] = $user['rol'];
                        $_SESSION['usuario_id'] = $user['id_u'];

                        // Redirigir al menú principal o dashboard después del login
                        header("location: crud.php?c=Menu&a=index"); // Ajusta si es otra ruta
                        exit();
                    } else {
                        header("location: ../index.php?error=El nombre de usuario o la contraseña son incorrectos");
                        exit();
                    }
                } else {
                    header("location: ../index.php?error=El nombre de usuario o la contraseña son incorrectos");
                    exit();
                }
            }
        } else {
            header("location: ../index.php"); // Redirigir si no hay datos POST
            exit();
        }
    }

    function registrarse()
    {
        if (isset($_POST['Username']) && isset($_POST['Password']) && isset($_POST['RPassword'])) {

            $username = validateInput($_POST['Username']);
            $password = validateInput($_POST['Password']); // Contraseña en texto plano
            $Rpassword = validateInput($_POST['RPassword']);

            $datosUsuarioQuery = 'Username=' . urlencode($username); // Para pasar en URL si hay error

            if (empty($username)) {
                header("location: ../views/registrarse.php?error=El usuario es requerido&$datosUsuarioQuery");
                exit();
            } elseif (empty($password)) {
                header("location: ../views/registrarse.php?error=La clave es requerida&$datosUsuarioQuery");
                exit();
            } elseif (empty($Rpassword)) {
                header("location: ../views/registrarse.php?error=Repetir la clave es requerida&$datosUsuarioQuery");
                exit();
            } elseif ($password !== $Rpassword) {
                header("location: ../views/registrarse.php?error=Las claves no coinciden&$datosUsuarioQuery");
                exit();
            } else {
                // Verificar si el usuario ya existe
                $usuarioExistente = $this->usuarioModel->Login($username);

                if (!empty($usuarioExistente)) { // Si el array no está vacío, el usuario existe
                    header("location: ../views/registrarse.php?error=El nombre de usuario ya está en uso&$datosUsuarioQuery");
                    exit();
                } else {
                    // Hashear la contraseña ANTES de guardarla
                    $passwordHasheada = password_hash($password, PASSWORD_DEFAULT);

                    // Intentar registrar el usuario
                    $confirmar = $this->usuarioModel->Registrar($username, $passwordHasheada);

                    if ($confirmar) {
                        // Usuario creado con éxito, redirigir al login con mensaje de éxito
                        header("location: ../index.php?success=Usuario creado con éxito. Por favor, inicie sesión.");
                        exit();
                    } else {
                        header("location: ../views/registrarse.php?error=Ocurrió un error durante el registro. Intente de nuevo.&$datosUsuarioQuery");
                        exit();
                    }
                }
            }
        } else {
            // Si no se enviaron todos los datos, redirigir de nuevo al formulario de registro
            header('location: ../views/registrarse.php?error=Todos los campos son requeridos');
            exit();
        }
    }
}
?>