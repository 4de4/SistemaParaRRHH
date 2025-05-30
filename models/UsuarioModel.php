<?php
class Usuario {
    private $db;
    private $change;

    public function __construct() {
        require_once('../config/database.php');
        $this->db = Conectar::conexion();
        if ($this->db) {
            $this->change = get_class($this->db);
        } else {
            error_log("Error al conectar a la base de datos en UsuarioModel::__construct");
            // Considerar lanzar una excepción si la conexión es crítica aquí
        }
    }

    public function Login($username) {
        $resultadosLogin = array();
        if (!$this->db) {
            error_log("Error de BD en UsuarioModel::Login - Conexión no establecida.");
            return $resultadosLogin;
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
        return $resultadosLogin;
    }

    public function Registrar($username, $passwordHasheada) {
        if (!$this->db) {
            error_log("Error de BD en UsuarioModel::Registrar - Conexión no establecida.");
            return false;
        }
        $sql = "INSERT INTO usuario (username, password, rol) VALUES (?, ?, ?)";
        $rol_por_defecto = 'usuario';

        if ($this->change === 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $username);
                $pst->bindParam(2, $passwordHasheada);
                $pst->bindParam(3, $rol_por_defecto);
                return $pst->execute();
            } catch (PDOException $e) {
                error_log("Error PDO en Registrar: " . $e->getMessage());
                return false;
            }
        } elseif ($this->change === 'mysqli') { // Corregido: 'mysqli'
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('sss', $username, $passwordHasheada, $rol_por_defecto);
                $resultado = $pst->execute();
                $pst->close();
                return $resultado;
            } else {
                error_log("Error MySQLi al preparar la consulta en Registrar: " . $this->db->error);
                return false;
            }
        }
        return false;
    }

    // *** NUEVO MÉTODO: Obtener todos los usuarios (para la lista de asignación de roles) ***
    public function get_todos_los_usuarios() {
        $listaUsuarios = array();
        if (!$this->db) {
            error_log("Error de BD en UsuarioModel::get_todos_los_usuarios - Conexión no establecida.");
            return $listaUsuarios;
        }

        $idUsuarioActual = $_SESSION['usuario_id'] ?? 0; // Para no listarse a sí mismo
        $sql = "SELECT id_u, username, rol FROM usuario WHERE id_u != ? ORDER BY username";

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $idUsuarioActual, PDO::PARAM_INT);
                $pst->execute();
                $listaUsuarios = $pst->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error PDO en get_todos_los_usuarios: " . $e->getMessage());
            }
        } elseif ($this->change == 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('i', $idUsuarioActual);
                $pst->execute();
                $res = $pst->get_result();
                if ($res) {
                    $listaUsuarios = $res->fetch_all(MYSQLI_ASSOC);
                }
                $pst->close();
            } else {
                error_log("Error MySQLi en get_todos_los_usuarios: " . $this->db->error);
            }
        }
        return $listaUsuarios;
    }

    // *** NUEVO MÉTODO: Actualizar el rol de un usuario ***
    public function actualizarRolUsuario($id_u, $nuevo_rol) {
        if (!$this->db) {
            error_log("Error de BD en UsuarioModel::actualizarRolUsuario - Conexión no establecida.");
            return false;
        }
        
        // Validar que $nuevo_rol sea uno de los permitidos ('jefe', 'empleado')
        $rolesPermitidos = ['jefe', 'empleado']; // Ajusta según tus roles
        if (!in_array($nuevo_rol, $rolesPermitidos)) {
            error_log("Intento de asignar rol no permitido: " . $nuevo_rol . " al usuario ID: " . $id_u);
            return false; // Rol no válido
        }

        $sql = "UPDATE usuario SET rol = ? WHERE id_u = ?";
        $actualizado = false;

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $nuevo_rol);
                $pst->bindParam(2, $id_u, PDO::PARAM_INT);
                $pst->execute();
                $actualizado = ($pst->rowCount() > 0);
            } catch (PDOException $e) {
                error_log("Error PDO en actualizarRolUsuario: " . $e->getMessage());
            }
        } elseif ($this->change == 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('si', $nuevo_rol, $id_u); // 's' para rol (string), 'i' para id_u (integer)
                $pst->execute();
                $actualizado = ($this->db->affected_rows > 0);
                $pst->close();
            } else {
                error_log("Error MySQLi en actualizarRolUsuario: " . $this->db->error);
            }
        }
        return $actualizado;
    }
}
?>