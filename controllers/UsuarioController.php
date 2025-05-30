<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función de validación (podría estar en un helper o ser un método privado de la clase)
if (!function_exists('validateInput')) { // Definir solo si no existe
    function validateInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}


class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        require_once('../models/UsuarioModel.php');
        $this->usuarioModel = new Usuario();
        // Incluir helpers si verificarAcceso está allí y no es global vía crud.php
        // require_once('../helpers.php');
    }

    function iniciar()
    {
        if (isset($_POST['Username']) && isset($_POST['Password'])) {
            $username = validateInput($_POST['Username']);
            $claveIngresada = validateInput($_POST['Password']);

            if (empty($username)) {
                header("location: ../index.php?error=El nombre de usuario es requerido");
                exit();
            } elseif (empty($claveIngresada)) {
                header("location: ../index.php?error=La contraseña es requerida");
                exit();
            } else {
                $usuariosEncontrados = $this->usuarioModel->Login($username);
                if ($usuariosEncontrados && count($usuariosEncontrados) > 0) {
                    $user = $usuariosEncontrados[0];
                    if (password_verify($claveIngresada, $user['password'])) {
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['rol'] = $user['rol'];
                        $_SESSION['usuario_id'] = $user['id_u']; // Guardar el ID del usuario en sesión
                        header("location: crud.php?c=Menu&a=index");
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
            header("location: ../index.php");
            exit();
        }
    }

    function registrarse()
    {
        if (isset($_POST['Username']) && isset($_POST['Password']) && isset($_POST['RPassword'])) {
            $username = validateInput($_POST['Username']);
            $password = validateInput($_POST['Password']);
            $Rpassword = validateInput($_POST['RPassword']);
            $datosUsuarioQuery = 'Username=' . urlencode($username);

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
                $usuarioExistente = $this->usuarioModel->Login($username);
                if (!empty($usuarioExistente)) {
                    header("location: ../views/registrarse.php?error=El nombre de usuario ya está en uso&$datosUsuarioQuery");
                    exit();
                } else {
                    $passwordHasheada = password_hash($password, PASSWORD_DEFAULT);
                    $confirmar = $this->usuarioModel->Registrar($username, $passwordHasheada);
                    if ($confirmar) {
                        header("location: ../index.php?success=Usuario creado con éxito. Por favor, inicie sesión.");
                        exit();
                    } else {
                        header("location: ../views/registrarse.php?error=Ocurrió un error durante el registro. Intente de nuevo.&$datosUsuarioQuery");
                        exit();
                    }
                }
            }
        } else {
            header('location: ../views/registrarse.php?error=Todos los campos son requeridos');
            exit();
        }
    }

    // *** NUEVO MÉTODO: Mostrar el formulario/lista para asignar roles ***
    public function mostrarFormularioRoles() {
        verificarAcceso(['jefe']); // Solo el 'jefe' puede acceder
        $data["titulo"] = "Asignar Roles a Usuarios";
        $data["usuarios"] = $this->usuarioModel->get_todos_los_usuarios();
        // Definir los roles disponibles para el dropdown/select en la vista
        $data["roles_disponibles"] = ['empleado', 'jefe']; // Ajusta según tus roles definidos

        require_once "../views/admin/asignar_roles.php";
    }

    // *** NUEVO MÉTODO: Procesar la actualización de un rol ***
    public function actualizarRol() {
        verificarAcceso(['jefe']);

        if (isset($_POST['usuario_id']) && isset($_POST['nuevo_rol'])) {
            $usuario_id = filter_var($_POST['usuario_id'], FILTER_VALIDATE_INT);
            $nuevo_rol = validateInput($_POST['nuevo_rol']);

            if (!$usuario_id) {
                $_SESSION['mensaje_error_rol'] = "ID de usuario no válido.";
            } else {
                // Validar que $nuevo_rol sea uno de los permitidos (ya se hace en el modelo, pero doble check)
                $rolesPermitidosEnSistema = ['empleado', 'jefe']; // Sincronizar con el modelo
                if (!in_array($nuevo_rol, $rolesPermitidosEnSistema)) {
                    $_SESSION['mensaje_error_rol'] = "Rol seleccionado ('".htmlspecialchars($nuevo_rol)."') no es válido.";
                }
                // Evitar que el admin se cambie el rol a sí mismo si es el único 'jefe' (lógica más compleja, omitida por simplicidad aquí)
                // O simplemente, no permitir que un jefe se cambie su propio rol desde esta interfaz.
                elseif ($usuario_id == ($_SESSION['usuario_id'] ?? 0)) {
                     $_SESSION['mensaje_error_rol'] = "No puedes cambiar tu propio rol desde esta interfaz.";
                } else {
                    $exito = $this->usuarioModel->actualizarRolUsuario($usuario_id, $nuevo_rol);
                    if ($exito) {
                        $_SESSION['mensaje_exito_rol'] = "Rol actualizado correctamente para el usuario.";
                    } else {
                        $_SESSION['mensaje_error_rol'] = "No se pudo actualizar el rol o el rol ya era el mismo. Asegúrese de que el rol sea válido.";
                    }
                }
            }
        } else {
            $_SESSION['mensaje_error_rol'] = "Datos incompletos para actualizar el rol.";
        }

        header("Location: crud.php?c=usuario&a=mostrarFormularioRoles");
        exit();
    }
}
?>