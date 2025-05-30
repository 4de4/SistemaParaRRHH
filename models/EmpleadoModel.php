<?php
class EmpleadoModel {
    private $db;
    private $change;

    public function __construct(){
        require_once('../config/database.php'); // Asegúrate que esta ruta sea correcta
        $this->db = Conectar::conexion();
        if ($this->db) {
            $this->change = get_class($this->db);
        } else {
            error_log("Fallo al conectar a la BD en EmpleadoModel constructor.");
        }
    }

    public function get_empleado() {
        $listaEmpleados = array();
        // MODIFICADO: JOIN con departamento usando empleado.id_departamento
        $sql = "SELECT e.*, c.*, d.nombre AS nombre_departamento, d.ubicacion AS ubicacion_departamento
                FROM empleado e
                INNER JOIN contrato c ON e.id_e = c.id_c
                LEFT JOIN departamento d ON e.id_departamento = d.id_d
                ORDER BY e.apellido, e.nombre";

        if (!$this->db) return $listaEmpleados;

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->execute();
                $listaEmpleados = $pst->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error PDO en get_empleado: " . $e->getMessage());
            }
        } elseif ($this->change == 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->execute();
                $res = $pst->get_result();
                if ($res) {
                    $listaEmpleados = $res->fetch_all(MYSQLI_ASSOC);
                }
                $pst->close();
            } else {
                error_log("Error MySQLi en get_empleado: " . $this->db->error);
            }
        }
        return $listaEmpleados;
    }

    public function get_empleado_por_id($id_e) {
        $empleadoData = null;
        $sql = "SELECT e.*, c.*, d.id_d AS id_departamento, d.nombre AS nombre_departamento, d.ubicacion AS ubicacion_departamento
                FROM empleado e
                INNER JOIN contrato c ON e.id_e = c.id_c
                LEFT JOIN departamento d ON e.id_departamento = d.id_d
                WHERE e.id_e = ?";

        if (!$this->db) return $empleadoData;

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $id_e, PDO::PARAM_INT);
                $pst->execute();
                $empleadoData = $pst->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error PDO en get_empleado_por_id: " . $e->getMessage());
            }
        } elseif ($this->change == 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('i', $id_e);
                $pst->execute();
                $res = $pst->get_result();
                if ($res) {
                    $empleadoData = $res->fetch_assoc();
                }
                $pst->close();
            } else {
                error_log("Error MySQLi en get_empleado_por_id: " . $this->db->error);
            }
        }
        return $empleadoData;
    }

    // FIRMA CORRECTA CON 8 ARGUMENTOS (espera id_departamento)
    public function set_Empleado($nombre, $apellido, $pathFotoBD, $fecha_nacimiento, $id_departamento, $fecha_inicio_contrato, $fecha_fin_contrato, $salario_base) {
        $idEmpleadoInsertado = false;
        if (!$this->db) return $idEmpleadoInsertado;

        if ($this->change == 'PDO') $this->db->beginTransaction();
        elseif ($this->change == 'mysqli') $this->db->begin_transaction();

        try {
            $sql_empleado = "INSERT INTO empleado (nombre, apellido, fecha_nacimiento, foto, id_departamento) VALUES (?, ?, ?, ?, ?)";
            if ($this->change == 'PDO') {
                $pst_e = $this->db->prepare($sql_empleado);
                $id_depto_param = ($id_departamento === '' || $id_departamento === null) ? null : (int)$id_departamento;
                $pst_e->bindParam(1, $nombre);
                $pst_e->bindParam(2, $apellido);
                $pst_e->bindParam(3, $fecha_nacimiento);
                $pst_e->bindParam(4, $pathFotoBD);
                if ($id_depto_param === null) {
                    $pst_e->bindParam(5, $id_depto_param, PDO::PARAM_NULL);
                } else {
                    $pst_e->bindParam(5, $id_depto_param, PDO::PARAM_INT);
                }
                $resultado_emp = $pst_e->execute();
                if ($resultado_emp) $idEmpleadoInsertado = $this->db->lastInsertId();
            } elseif ($this->change == 'mysqli') {
                $pst_e = $this->db->prepare($sql_empleado);
                $id_depto_param = ($id_departamento === '' || $id_departamento === null) ? null : (int)$id_departamento;
                $pst_e->bind_param('ssssi', $nombre, $apellido, $fecha_nacimiento, $pathFotoBD, $id_depto_param);
                $resultado_emp = $pst_e->execute();
                if ($resultado_emp) $idEmpleadoInsertado = $this->db->insert_id;
                $pst_e->close();
            }

            if (!$idEmpleadoInsertado) throw new Exception("Error al insertar empleado.");

            $sql_contrato = "INSERT INTO contrato (id_c, fecha_inicio, fecha_fin, salario_base) VALUES (?, ?, ?, ?)";
            if ($this->change == 'PDO') {
                $pst_c = $this->db->prepare($sql_contrato);
                $pst_c->bindParam(1, $idEmpleadoInsertado, PDO::PARAM_INT);
                $pst_c->bindParam(2, $fecha_inicio_contrato);
                $pst_c->bindParam(3, $fecha_fin_contrato);
                $pst_c->bindParam(4, $salario_base);
                if (!$pst_c->execute()) throw new Exception("Error al insertar contrato.");
            } elseif ($this->change == 'mysqli') {
                $pst_c = $this->db->prepare($sql_contrato);
                $pst_c->bind_param('issd', $idEmpleadoInsertado, $fecha_inicio_contrato, $fecha_fin_contrato, $salario_base);
                if (!$pst_c->execute()) throw new Exception("Error al insertar contrato.");
                $pst_c->close();
            }

            if ($this->change == 'PDO') $this->db->commit();
            elseif ($this->change == 'mysqli') $this->db->commit();
            return $idEmpleadoInsertado;

        } catch (Exception $e) {
            if ($this->change == 'PDO') $this->db->rollBack();
            elseif ($this->change == 'mysqli') $this->db->rollback();
            error_log("Error en set_Empleado: " . $e->getMessage());
            return false;
        }
    }

    // FIRMA CORRECTA CON 9 ARGUMENTOS (espera id_departamento)
    public function update_Empleado($id_e, $nombre, $apellido, $pathFotoBD, $fecha_nacimiento, $id_departamento, $fecha_inicio_contrato, $fecha_fin_contrato, $salario_base) {
        if (!$this->db) return false;

        if ($this->change == 'PDO') $this->db->beginTransaction();
        elseif ($this->change == 'mysqli') $this->db->begin_transaction();

        try {
            $sql_empleado = "UPDATE empleado SET nombre = ?, apellido = ?, fecha_nacimiento = ?, id_departamento = ?";
            if ($pathFotoBD !== null && $pathFotoBD !== '') { // Solo añadir foto si se proporciona una nueva y no es string vacío
                $sql_empleado .= ", foto = ?";
            }
            $sql_empleado .= " WHERE id_e = ?";

            if ($this->change == 'PDO') {
                $pst_e = $this->db->prepare($sql_empleado);
                $paramIndex = 1;
                $pst_e->bindParam($paramIndex++, $nombre);
                $pst_e->bindParam($paramIndex++, $apellido);
                $pst_e->bindParam($paramIndex++, $fecha_nacimiento);
                $id_depto_param = ($id_departamento === '' || $id_departamento === null) ? null : (int)$id_departamento;
                if ($id_depto_param === null) {
                    $pst_e->bindParam($paramIndex++, $id_depto_param, PDO::PARAM_NULL);
                } else {
                    $pst_e->bindParam($paramIndex++, $id_depto_param, PDO::PARAM_INT);
                }
                if ($pathFotoBD !== null && $pathFotoBD !== '') {
                    $pst_e->bindParam($paramIndex++, $pathFotoBD);
                }
                $pst_e->bindParam($paramIndex++, $id_e, PDO::PARAM_INT);
                if (!$pst_e->execute()) throw new Exception("Error al actualizar empleado.");
            } elseif ($this->change == 'mysqli') {
                $types = 'sssi'; // nombre, apellido, fecha_nac, id_depto
                $params = [$nombre, $apellido, $fecha_nacimiento];
                $id_depto_param = ($id_departamento === '' || $id_departamento === null) ? null : (int)$id_departamento;
                $params[] = $id_depto_param;

                if ($pathFotoBD !== null && $pathFotoBD !== '') {
                    $types .= 's'; // foto
                    $params[] = $pathFotoBD;
                }
                $types .= 'i'; // id_e para WHERE
                $params[] = $id_e;

                $pst_e = $this->db->prepare($sql_empleado);
                $pst_e->bind_param($types, ...$params);
                if (!$pst_e->execute()) throw new Exception("Error al actualizar empleado.");
                $pst_e->close();
            }

            $sql_contrato = "UPDATE contrato SET fecha_inicio = ?, fecha_fin = ?, salario_base = ? WHERE id_c = ?";
            if ($this->change == 'PDO') {
                $pst_c = $this->db->prepare($sql_contrato);
                $pst_c->bindParam(1, $fecha_inicio_contrato);
                $pst_c->bindParam(2, $fecha_fin_contrato);
                $pst_c->bindParam(3, $salario_base);
                $pst_c->bindParam(4, $id_e, PDO::PARAM_INT);
                if (!$pst_c->execute()) throw new Exception("Error al actualizar contrato.");
            } elseif ($this->change == 'mysqli') {
                $pst_c = $this->db->prepare($sql_contrato);
                $pst_c->bind_param('ssdi', $fecha_inicio_contrato, $fecha_fin_contrato, $salario_base, $id_e);
                if (!$pst_c->execute()) throw new Exception("Error al actualizar contrato.");
                $pst_c->close();
            }

            if ($this->change == 'PDO') $this->db->commit();
            elseif ($this->change == 'mysqli') $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->change == 'PDO') $this->db->rollBack();
            elseif ($this->change == 'mysqli') $this->db->rollback();
            error_log("Error en update_Empleado: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id_e) {
        $empleadoAEliminar = $this->get_empleado_por_id($id_e);
        $pathFotoServidor = null;
        if ($empleadoAEliminar && !empty($empleadoAEliminar['foto'])) {
             $pathFotoServidor = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/' . basename($empleadoAEliminar['foto']);
        }
        if (!$this->db) return false;

        // La eliminación del contrato se maneja por ON DELETE CASCADE.
        // El empleado se elimina.
        $sql_emp = "DELETE FROM empleado WHERE id_e = ?";
        $eliminado = false;

        if ($this->change == 'PDO') $this->db->beginTransaction();
        elseif ($this->change == 'mysqli') $this->db->begin_transaction();

        try {
            // Ya no se elimina de departamento explícitamente aquí.

            if ($this->change == 'PDO') {
                $pst_e = $this->db->prepare($sql_emp);
                $pst_e->bindParam(1, $id_e, PDO::PARAM_INT);
                $pst_e->execute();
                $eliminado = ($pst_e->rowCount() > 0);
            } elseif ($this->change == 'mysqli') {
                $pst_e = $this->db->prepare($sql_emp);
                $pst_e->bind_param('i', $id_e);
                $pst_e->execute();
                $eliminado = ($this->db->affected_rows > 0);
                $pst_e->close();
            }

            if ($eliminado) {
                if ($this->change == 'PDO') $this->db->commit();
                elseif ($this->change == 'mysqli') $this->db->commit();
                if ($pathFotoServidor && file_exists($pathFotoServidor)) {
                    unlink($pathFotoServidor);
                }
            } else {
                if ($this->change == 'PDO') $this->db->rollBack();
                elseif ($this->change == 'mysqli') $this->db->rollback();
            }
            return $eliminado;

        } catch (Exception $e) {
            if ($this->change == 'PDO') $this->db->rollBack();
            elseif ($this->change == 'mysqli') $this->db->rollback();
            error_log("Error en eliminar Empleado: " . $e->getMessage());
            return false;
        }
    }
}
?>