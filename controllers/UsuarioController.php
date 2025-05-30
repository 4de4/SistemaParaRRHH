<?php
/*iniciarSesion.php*/
session_start();
class UsuarioController
{
    public function __construct()
    {
        require_once('../models/UsuarioModel.php');
    }

    function iniciar()
    {
        if (isset($_POST['Username']) && isset($_POST['Password'])) {
            function validate($data)
            {
                $data = trim($data);
                $data = stripcslashes($data);
                $data = htmlspecialchars($data);

                return $data;
            }

            $usuario = validate($_POST['Username']);
            $clave = validate($_POST['Password']);
            if (empty($usuario)) {
                header("location: ../index.php?error=El usuario es requerido");
                exit();
            } elseif (empty($clave)) {
                header("location: ../index.php?error=La clave es requerida");
                exit();
            } else {
                /*$clave = password_hash($clave, PASSWORD_BCRYPT);

            $sql = "SELECT * FROM usuario WHERE user = '$usuario' AND clave = '$clave'";
            $query = $con->query($sql);*/

                $guardar = new Usuario();
                $row = $guardar->Login($usuario);
                if ($row && count($row) > 0) {
                    $user = $row[0];
                    if ($user['password'] === $clave) {
                        $_SESSION['id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['rol'] = $user['rol'];

                        header("location: ../views/crud.php");
                        exit();
                    } else {
                        header("location: ../index.php?error=La clave es incorrecta");
                        exit();
                    }
                } else {
                    header("location: ../index?error=El usuario o clave son inexistentes");
                    exit();
                }
            }
        } else {
            header("location: ../index.php");
            exit();
        }
    }

    function registrarse(){
        if(isset($_POST['Username']) && isset($_POST['Password']) && isset($_POST['RPassword'])){
        function validar($data){
            $data = trim($data);
            $data = stripcslashes($data);
            $data = htmlspecialchars($data);

            return $data;
        }

        $username = validar($_POST['Username']);
        $password = validar($_POST['Password']);
        $Rpassword = validar($_POST['RPassword']);

        $datosUsuario = 'Username=' . $username;

        if(empty($username)){
            header("location: ../views/registrarse.php?error=El usuario es requerido&$datosUsuario");
            exit();
        }elseif(empty($password)){
            header("location: ../views/registrarse.php?error=La clave es requerida&$datosUsuario");
            exit();
        }elseif(empty($Rpassword)){
            header("location: ../views/registrarse.php?error=Repetir la clave es requerida&$datosUsuario");
            exit();
        }elseif($password !== $Rpassword){
            header("location: ../views/registrarse.php?error=Las claves no coinciden");
            exit();
        }else{
            /*$clave = password_hash($clave, PASSWORD_BCRYPT);*/

            $prueba = new Usuario();
            $a = $prueba->Login($username);

            if($a['username']===$username){
               header("location: ../views/registrarse.php?error=El usuario ya existe!");
               exit(); 
            }else{
                $guardar = new Usuario();
                $confirmar = $guardar->Registrar($username,$password);

                if($confirmar){
                    header("location: ../index.php?error=Usuario creado con exito!");
                    exit();
                }else{
                    header("location: ../views/registrarse.php?success=Ocurrio un error...:(");
                    exit();
                }
            }
        }
    }else{
        header('location: ../views/registrarse.php');
        exit();
    }
    }
}
