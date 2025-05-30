<?php
class DepartamentoModel {
    private $db;
    private $change; // Para saber si es PDO o MySQLi

    public function __construct() {
        require_once('../config/database.php'); // Asumiendo que está en config/
        $this->db = Conectar::conexion();
        if ($this->db) {
            $this->change = get_class($this->db);
        } else {
            error_log("Fallo al conectar a la BD en DepartamentoModel constructor.");
            // Podrías lanzar una excepción para detener la ejecución.
        }
    }

    public function get_departamentos() {
        $listaDepartamentos = array();
        $sql = "SELECT id_d, nombre, ubicacion FROM departamento ORDER BY nombre";

        if (!$this->db) return $listaDepartamentos; // Si no hay conexión

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->execute();
                $listaDepartamentos = $pst->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error PDO en get_departamentos: " . $e->getMessage());
            }
        } elseif ($this->change == 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->execute();
                $res = $pst->get_result();
                if ($res) {
                    $listaDepartamentos = $res->fetch_all(MYSQLI_ASSOC);
                }
                $pst->close();
            } else {
                 error_log("Error MySQLi en get_departamentos: " . $this->db->error);
            }
        }
        return $listaDepartamentos;
    }

    public function get_departamento_por_id($id_d) {
        $departamentoData = null;
        $sql = "SELECT id_d, nombre, ubicacion FROM departamento WHERE id_d = ?";

        if (!$this->db) return $departamentoData;

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $id_d, PDO::PARAM_INT);
                $pst->execute();
                $departamentoData = $pst->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error PDO en get_departamento_por_id: " . $e->getMessage());
            }
        } elseif ($this->change == 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('i', $id_d);
                $pst->execute();
                $res = $pst->get_result();
                if ($res) {
                    $departamentoData = $res->fetch_assoc();
                }
                $pst->close();
            } else {
                error_log("Error MySQLi en get_departamento_por_id: " . $this->db->error);
            }
        }
        return $departamentoData;
    }

    public function insertar_departamento($nombre, $ubicacion) {
        $sql = "INSERT INTO departamento (nombre, ubicacion) VALUES (?, ?)";
        $idInsertado = false;

        if (!$this->db) return $idInsertado;

        if ($this->change == 'PDO') {
            try {
                $this->db->beginTransaction();
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $nombre);
                $pst->bindParam(2, $ubicacion);
                if ($pst->execute()) {
                    $idInsertado = $this->db->lastInsertId();
                    $this->db->commit();
                } else {
                    $this->db->rollBack();
                }
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Error PDO en insertar_departamento: " . $e->getMessage());
                // Verificar si es error de duplicado (UNIQUE constraint en 'nombre')
                if ($e->getCode() == '23000') { // Código SQLSTATE para Integrity constraint violation
                    // Podrías retornar un código específico o un mensaje para el controlador
                    return 'duplicado';
                }
            }
        } elseif ($this->change == 'mysqli') {
            $this->db->begin_transaction();
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('ss', $nombre, $ubicacion);
                if ($pst->execute()) {
                    $idInsertado = $this->db->insert_id;
                    $this->db->commit();
                } else {
                    $this->db->rollback();
                    // Verificar si es error de duplicado
                    if ($this->db->errno == 1062) { // Código de error MySQL para duplicado
                        $idInsertado = 'duplicado';
                    } else {
                        error_log("Error MySQLi al ejecutar insertar_departamento: " . $pst->error);
                    }
                }
                $pst->close();
            } else {
                $this->db->rollback();
                error_log("Error MySQLi al preparar insertar_departamento: " . $this->db->error);
            }
        }
        return $idInsertado; // Retorna el ID, 'duplicado', o false
    }

    public function actualizar_departamento($id_d, $nombre, $ubicacion) {
        $sql = "UPDATE departamento SET nombre = ?, ubicacion = ? WHERE id_d = ?";
        $actualizado = false;

        if (!$this->db) return $actualizado;

        if ($this->change == 'PDO') {
            try {
                $this->db->beginTransaction();
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $nombre);
                $pst->bindParam(2, $ubicacion);
                $pst->bindParam(3, $id_d, PDO::PARAM_INT);
                if ($pst->execute()) {
                    $actualizado = ($pst->rowCount() > 0); // Verifica si alguna fila fue afectada
                    $this->db->commit();
                } else {
                    $this->db->rollBack();
                }
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Error PDO en actualizar_departamento: " . $e->getMessage());
                if ($e->getCode() == '23000') return 'duplicado';
            }
        } elseif ($this->change == 'mysqli') {
            $this->db->begin_transaction();
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('ssi', $nombre, $ubicacion, $id_d);
                if ($pst->execute()) {
                    $actualizado = ($this->db->affected_rows > 0);
                    $this->db->commit();
                } else {
                    $this->db->rollback();
                     if ($this->db->errno == 1062) {
                        $actualizado = 'duplicado';
                    } else {
                        error_log("Error MySQLi al ejecutar actualizar_departamento: " . $pst->error);
                    }
                }
                $pst->close();
            } else {
                $this->db->rollback();
                error_log("Error MySQLi al preparar actualizar_departamento: " . $this->db->error);
            }
        }
        return $actualizado; // Retorna true, 'duplicado', o false
    }

    public function eliminar_departamento($id_d) {
        // Antes de eliminar, verificar si hay empleados asignados a este departamento.
        // Si existen, no se debería permitir eliminar (o se deberían reasignar).
        // Por ahora, implementaremos la eliminación directa.
        // La FK en empleado tiene ON DELETE SET NULL, así que los empleados quedarían sin departamento.
        $sql_check = "SELECT COUNT(*) as count FROM empleado WHERE id_departamento = ?";
        $sql_delete = "DELETE FROM departamento WHERE id_d = ?";
        $eliminado = false;

        if (!$this->db) return $eliminado;

        if ($this->change == 'PDO') {
            try {
                // Verificar empleados asignados (opcional, pero buena práctica)
                $pst_check = $this->db->prepare($sql_check);
                $pst_check->bindParam(1, $id_d, PDO::PARAM_INT);
                $pst_check->execute();
                $result_check = $pst_check->fetch(PDO::FETCH_ASSOC);
                
                // Si prefieres no eliminar si hay empleados:
                // if ($result_check && $result_check['count'] > 0) {
                //     return 'en_uso'; // Código especial para indicar que está en uso
                // }

                $this->db->beginTransaction();
                $pst = $this->db->prepare($sql_delete);
                $pst->bindParam(1, $id_d, PDO::PARAM_INT);
                if ($pst->execute()) {
                    $eliminado = ($pst->rowCount() > 0);
                    $this->db->commit();
                } else {
                    $this->db->rollBack();
                }
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Error PDO en eliminar_departamento: " . $e->getMessage());
                // Podría haber un error de FK si ON DELETE SET NULL no está o falla
            }
        } elseif ($this->change == 'mysqli') {
             // Verificar empleados asignados (opcional)
            $pst_check = $this->db->prepare($sql_check);
            if ($pst_check) {
                $pst_check->bind_param('i', $id_d);
                $pst_check->execute();
                $res_check_obj = $pst_check->get_result();
                $res_check = $res_check_obj->fetch_assoc();
                $pst_check->close();
                // if ($res_check && $res_check['count'] > 0) {
                //     return 'en_uso';
                // }
            }


            $this->db->begin_transaction();
            $pst = $this->db->prepare($sql_delete);
            if ($pst) {
                $pst->bind_param('i', $id_d);
                if ($pst->execute()) {
                    $eliminado = ($this->db->affected_rows > 0);
                    $this->db->commit();
                } else {
                    $this->db->rollback();
                    error_log("Error MySQLi al ejecutar eliminar_departamento: " . $pst->error);
                }
                $pst->close();
            } else {
                $this->db->rollback();
                error_log("Error MySQLi al preparar eliminar_departamento: " . $this->db->error);
            }
        }
        return $eliminado; // Retorna true, false, o 'en_uso'
    }
}
?>