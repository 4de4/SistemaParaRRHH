<?php
class Usuario {
    // private $guardar; // No parece usarse como propiedad de instancia de forma persistente
    private $db;
    private $change; // Para saber si es PDO o MySQLi

    public function __construct() {
        // Es mejor que la conexión se pase o se obtenga de un singleton/factory
        // en lugar de requerir 'database.php' directamente en cada modelo,
        // pero por ahora mantenemos tu estructura.
        require_once('../config/database.php');
        // $this->guardar = array(); // Inicializar aquí si se va a acumular en la instancia
        $this->db = Conectar::conexion();
        if ($this->db) { // Verificar que la conexión se estableció
            $this->change = get_class($this->db);
        } else {
            // Manejar error de conexión si es necesario, aunque Conectar::conexion() ya usa die()
            // Podrías lanzar una excepción o registrar un error.
            error_log("Error al conectar a la base de datos en UsuarioModel::__construct");
            // $this->change podría quedar sin definir, lo que causaría problemas después.
            // Es mejor asegurar que $this->db siempre sea un objeto válido o lanzar excepción.
        }
    }

    public function Login($username) {
        $resultadosLogin = array(); // Usar una variable local para los resultados de esta función

        if (!$this->db) {
            error_log("Error de BD en UsuarioModel::Login - Conexión no establecida.");
            return $resultadosLogin; // Retornar array vacío
        }

        $sql = "SELECT id_u, username, password, rol FROM usuario WHERE username = ?";

        if ($this->change === 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $username);
                $pst->execute();
                $resultadosLogin = $pst->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error PDO en Login: " . $e->getMessage());
                // En un entorno de producción, no mostrarías $e->getMessage() al usuario.
                // Aquí podrías retornar un array vacío o lanzar una excepción personalizada.
            }
        } elseif ($this->change === 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('s', $username);
                $pst->execute();
                $res = $pst->get_result();
                if ($res) {
                    $resultadosLogin = $res->fetch_all(MYSQLI_ASSOC);
                }
                $pst->close();
            } else {
                error_log("Error MySQLi al preparar la consulta en Login: " . $this->db->error);
            }
        }
        // $this->db = null; // No cierres la conexión aquí si otros métodos la van a usar.
                           // La conexión debería manejarse de forma centralizada o cerrarse al final del script.
        return $resultadosLogin;
    }

    public function Registrar($username, $passwordHasheada) {
        if (!$this->db) {
            error_log("Error de BD en UsuarioModel::Registrar - Conexión no establecida.");
            return false;
        }

        $sql = "INSERT INTO usuario (username, password, rol) VALUES (?, ?, ?)";
        // Por defecto, el rol será 'usuario' según tu BD, pero podrías querer especificarlo.
        // Si la BD ya tiene DEFAULT 'usuario' para rol, puedes omitirlo de la inserción
        // o pasar el rol como parámetro si quieres más flexibilidad.
        $rol_por_defecto = 'usuario';


        if ($this->change === 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $username);
                $pst->bindParam(2, $passwordHasheada);
                $pst->bindParam(3, $rol_por_defecto); // Asignar rol por defecto
                return $pst->execute(); // Devuelve true en éxito, false en error
            } catch (PDOException $e) {
                // Manejo de error, por ejemplo, si el username ya existe (aunque el controlador ya lo verifica)
                error_log("Error PDO en Registrar: " . $e->getMessage());
                // Podrías verificar $e->getCode() para errores específicos como duplicados (23000)
                return false;
            }
        } elseif ($this->change === 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('sss', $username, $passwordHasheada, $rol_por_defecto);
                $resultado = $pst->execute();
                $pst->close();
                return $resultado; // Devuelve true en éxito, false en error
            } else {
                error_log("Error MySQLi al preparar la consulta en Registrar: " . $this->db->error);
                return false;
            }
        }
        // $this->db = null; // No cierres la conexión aquí.
        return false; // Si $this->change no es ni PDO ni mysqli
    }
}
?>