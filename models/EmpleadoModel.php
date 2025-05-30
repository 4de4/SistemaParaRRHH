<?php
class EmpleadoModel {
    private $db;
    // private $empleado; // Esta propiedad no se usa consistentemente como array de instancia, mejor usar variables locales en métodos.
    private $change;

    public function __construct(){
        $this->db = Conectar::conexion();
        if ($this->db) {
            $this->change = get_class($this->db);
        } else {
            // Manejar error de conexión, por ejemplo, lanzando una excepción o logueando.
            // Si Conectar::conexion() ya usa die(), esta parte podría no alcanzarse.
            error_log("Fallo al conectar a la BD en EmpleadoModel constructor.");
            // Podrías lanzar una excepción aquí para detener la ejecución si la BD es crucial.
            // throw new Exception("No se pudo conectar a la base de datos.");
        }
    }

    public function get_empleado() {
        $listaEmpleados = array(); // Usar variable local
        // IMPORTANTE: Esta consulta asume que id_c en contrato y id_d en departamento son IGUALES a id_e en empleado.
        // Si cambiaste la BD para que departamento sea independiente y empleado tenga id_departamento (FK),
        // esta consulta debe cambiar significativamente.
        // Por ahora, se mantiene tu lógica original para consistencia.
        $sql = "SELECT e.*, c.*, d.nombre AS nombre_d, d.ubicacion FROM empleado e 
                INNER JOIN contrato c ON e.id_e = c.id_c 
                INNER JOIN departamento d ON e.id_e = d.id_d
                ORDER BY e.apellido, e.nombre"; // Añadido ORDER BY

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->execute();
                $listaEmpleados = $pst->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error PDO en get_empleado: " . $e->getMessage());
                return []; // Retornar array vacío en caso de error
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
                return []; // Retornar array vacío en caso de error
            }
        }
        return $listaEmpleados;
    }

    // *** NUEVO MÉTODO: OBTENER UN EMPLEADO POR SU ID ***
    public function get_empleado_por_id($id) {
        $empleadoData = null;
        // Similar a get_empleado, pero con WHERE y esperando un solo resultado.
        $sql = "SELECT e.*, c.*, d.nombre AS nombre_d, d.ubicacion FROM empleado e 
                INNER JOIN contrato c ON e.id_e = c.id_c 
                INNER JOIN departamento d ON e.id_e = d.id_d 
                WHERE e.id_e = ?";

        if ($this->change == 'PDO') {
            try {
                $pst = $this->db->prepare($sql);
                $pst->bindParam(1, $id, PDO::PARAM_INT);
                $pst->execute();
                $empleadoData = $pst->fetch(PDO::FETCH_ASSOC); // fetch() para un solo resultado
            } catch (PDOException $e) {
                error_log("Error PDO en get_empleado_por_id: " . $e->getMessage());
            }
        } elseif ($this->change == 'mysqli') {
            $pst = $this->db->prepare($sql);
            if ($pst) {
                $pst->bind_param('i', $id);
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


    // MÉTODO INSERTAR (set_Empleado) - Revisado para retornar ID o false
    // El path de la foto ahora se pasa como argumento desde el controlador.
    public function set_Empleado($nombre, $apellido, $pathFotoBD, $fecha_nacimiento, $fecha_inicio_contrato, $fecha_fin_contrato, $salario_base, $nombre_departamento, $ubicacion_departamento) {
        $idEmpleadoInsertado = false; // Para retornar el ID del nuevo empleado o false

        // Lógica de base de datos (asumiendo que departamento y contrato se crean junto con el empleado y comparten ID)
        // Esto es problemático si departamento es una entidad independiente.
        // Por ahora, se mantiene tu lógica original.

        // Iniciar transacción si es posible (especialmente con múltiples inserciones)
        if ($this->change == 'PDO') $this->db->beginTransaction();
        elseif ($this->change == 'mysqli') $this->db->begin_transaction();

        try {
            // 1. Insertar en empleado
            $sql_empleado = "INSERT INTO empleado (nombre, apellido, fecha_nacimiento, foto) VALUES (?, ?, ?, ?)";
            if ($this->change == 'PDO') {
                $pst_e = $this->db->prepare($sql_empleado);
                $pst_e->bindParam(1, $nombre);
                $pst_e->bindParam(2, $apellido);
                $pst_e->bindParam(3, $fecha_nacimiento);
                $pst_e->bindParam(4, $pathFotoBD); // $pathFotoBD es el nombre/ruta guardado en BD
                $resultado_emp = $pst_e->execute();
                if ($resultado_emp) $idEmpleadoInsertado = $this->db->lastInsertId();
            } elseif ($this->change == 'mysqli') {
                $pst_e = $this->db->prepare($sql_empleado);
                $pst_e->bind_param('ssss', $nombre, $apellido, $fecha_nacimiento, $pathFotoBD);
                $resultado_emp = $pst_e->execute();
                if ($resultado_emp) $idEmpleadoInsertado = $this->db->insert_id;
                $pst_e->close();
            }

            if (!$idEmpleadoInsertado) throw new Exception("Error al insertar empleado.");

            // 2. Insertar en contrato
            $sql_contrato = "INSERT INTO contrato (id_c, fecha_inicio, fecha_fin, salario_base) VALUES (?, ?, ?, ?)";
            if ($this->change == 'PDO') {
                $pst_c = $this->db->prepare($sql_contrato);
                $pst_c->bindParam(1, $idEmpleadoInsertado);
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

            // 3. Insertar en departamento (siguiendo tu lógica original)
            $sql_departamento = "INSERT INTO departamento (id_d, nombre, ubicacion) VALUES (?, ?, ?)"; // Corregido 'nombre_d' a 'nombre' si la columna es 'nombre'
            if ($this->change == 'PDO') {
                $pst_d = $this->db->prepare($sql_departamento);
                $pst_d->bindParam(1, $idEmpleadoInsertado);
                $pst_d->bindParam(2, $nombre_departamento);
                $pst_d->bindParam(3, $ubicacion_departamento);
                if (!$pst_d->execute()) throw new Exception("Error al insertar departamento.");
            } elseif ($this->change == 'mysqli') {
                $pst_d = $this->db->prepare($sql_departamento);
                $pst_d->bind_param('iss', $idEmpleadoInsertado, $nombre_departamento, $ubicacion_departamento);
                if (!$pst_d->execute()) throw new Exception("Error al insertar departamento.");
                $pst_d->close();
            }

            // Si todo fue bien, confirmar transacción
            if ($this->change == 'PDO') $this->db->commit();
            elseif ($this->change == 'mysqli') $this->db->commit();

            return $idEmpleadoInsertado;

        } catch (Exception $e) {
            // Si algo falla, revertir transacción
            if ($this->change == 'PDO') $this->db->rollBack();
            elseif ($this->change == 'mysqli') $this->db->rollback();
            error_log("Error en set_Empleado: " . $e->getMessage());
            return false;
        }
        // $this->db = null; // No cerrar conexión aquí si se reutiliza
    }

    // *** MÉTODO MODIFICAR (update_Empleado) - CORREGIDO PARA UPDATE ***
    // $pathFotoBD es el nuevo path de la foto si se actualiza, o el anterior si no.
    // Puede ser NULL si se decide eliminar la foto.
    public function update_Empleado($id_e, $nombre, $apellido, $pathFotoBD, $fecha_nacimiento, $fecha_inicio_contrato, $fecha_fin_contrato, $salario_base, $nombre_departamento, $ubicacion_departamento) {
        // Iniciar transacción
        if ($this->change == 'PDO') $this->db->beginTransaction();
        elseif ($this->change == 'mysqli') $this->db->begin_transaction();

        try {
            // 1. Actualizar empleado
            // Si $pathFotoBD es NULL, podríamos querer no actualizar el campo foto o ponerlo a NULL.
            // Si $pathFotoBD está vacío pero no es NULL, indica que no se subió nueva foto, mantener la anterior.
            // Esta lógica debe manejarse en el controlador antes de llamar a este método.
            // Aquí asumimos que $pathFotoBD contiene el valor final para la BD.
            $sql_empleado = "UPDATE empleado SET nombre = ?, apellido = ?, fecha_nacimiento = ?";
            if ($pathFotoBD !== null) { // Solo actualizar foto si se proporciona un nuevo path (puede ser string vacío para borrar o un path)
                $sql_empleado .= ", foto = ?";
            }
            $sql_empleado .= " WHERE id_e = ?";

            if ($this->change == 'PDO') {
                $pst_e = $this->db->prepare($sql_empleado);
                $paramIndex = 1;
                $pst_e->bindParam($paramIndex++, $nombre);
                $pst_e->bindParam($paramIndex++, $apellido);
                $pst_e->bindParam($paramIndex++, $fecha_nacimiento);
                if ($pathFotoBD !== null) {
                    $pst_e->bindParam($paramIndex++, $pathFotoBD);
                }
                $pst_e->bindParam($paramIndex++, $id_e, PDO::PARAM_INT);
                if (!$pst_e->execute()) throw new Exception("Error al actualizar empleado.");
            } elseif ($this->change == 'mysqli') {
                $types = 'sss';
                $params = [$nombre, $apellido, $fecha_nacimiento];
                if ($pathFotoBD !== null) {
                    $types .= 's';
                    $params[] = $pathFotoBD;
                }
                $types .= 'i';
                $params[] = $id_e;

                $pst_e = $this->db->prepare($sql_empleado);
                $pst_e->bind_param($types, ...$params);
                if (!$pst_e->execute()) throw new Exception("Error al actualizar empleado.");
                $pst_e->close();
            }

            // 2. Actualizar contrato
            $sql_contrato = "UPDATE contrato SET fecha_inicio = ?, fecha_fin = ?, salario_base = ? WHERE id_c = ?";
            if ($this->change == 'PDO') {
                $pst_c = $this->db->prepare($sql_contrato);
                $pst_c->bindParam(1, $fecha_inicio_contrato);
                $pst_c->bindParam(2, $fecha_fin_contrato);
                $pst_c->bindParam(3, $salario_base);
                $pst_c->bindParam(4, $id_e, PDO::PARAM_INT); // id_c es igual a id_e en tu lógica
                if (!$pst_c->execute()) throw new Exception("Error al actualizar contrato.");
            } elseif ($this->change == 'mysqli') {
                $pst_c = $this->db->prepare($sql_contrato);
                $pst_c->bind_param('ssdi', $fecha_inicio_contrato, $fecha_fin_contrato, $salario_base, $id_e);
                if (!$pst_c->execute()) throw new Exception("Error al actualizar contrato.");
                $pst_c->close();
            }

            // 3. Actualizar departamento (siguiendo tu lógica original)
            // Asegúrate que la columna en la BD sea 'nombre' para el nombre del departamento.
            $sql_departamento = "UPDATE departamento SET nombre = ?, ubicacion = ? WHERE id_d = ?";
            if ($this->change == 'PDO') {
                $pst_d = $this->db->prepare($sql_departamento);
                $pst_d->bindParam(1, $nombre_departamento);
                $pst_d->bindParam(2, $ubicacion_departamento);
                $pst_d->bindParam(3, $id_e, PDO::PARAM_INT); // id_d es igual a id_e en tu lógica
                if (!$pst_d->execute()) throw new Exception("Error al actualizar departamento.");
            } elseif ($this->change == 'mysqli') {
                $pst_d = $this->db->prepare($sql_departamento);
                $pst_d->bind_param('ssi', $nombre_departamento, $ubicacion_departamento, $id_e);
                if (!$pst_d->execute()) throw new Exception("Error al actualizar departamento.");
                $pst_d->close();
            }

            // Si todo fue bien, confirmar transacción
            if ($this->change == 'PDO') $this->db->commit();
            elseif ($this->change == 'mysqli') $this->db->commit();

            return true;

        } catch (Exception $e) {
            // Si algo falla, revertir transacción
            if ($this->change == 'PDO') $this->db->rollBack();
            elseif ($this->change == 'mysqli') $this->db->rollback();
            error_log("Error en update_Empleado: " . $e->getMessage());
            return false;
        }
        // $this->db = null; // No cerrar conexión aquí
    }


    public function eliminar($id_e) {
        // Asumiendo que `id_c` en `contrato` y `id_d` en `departamento` son iguales a `id_e`
        // y que tienes ON DELETE CASCADE en la FK de contrato a empleado.
        // Si no, necesitarías eliminar de contrato y departamento primero.
        // Tu script de BD ya tiene ON DELETE CASCADE para contrato.
        // Para departamento, si se relaciona 1 a 1 con empleado y comparten ID:

        // Iniciar transacción
        if ($this->change == 'PDO') $this->db->beginTransaction();
        elseif ($this->change == 'mysqli') $this->db->begin_transaction();

        try {
            // 0. Obtener datos del empleado para borrar foto (opcional)
            $empleadoAEliminar = $this->get_empleado_por_id($id_e);
            $pathFotoAEliminar = null;
            if ($empleadoAEliminar && !empty($empleadoAEliminar['foto'])) {
                // La ruta de la foto guardada en BD es algo como '/SistemaParaRRHH/views/fotos/nombre_archivo.jpg'
                // Necesitamos la ruta física en el servidor.
                // Si $_SERVER['DOCUMENT_ROOT'] es 'C:/xampp/htdocs'
                // y $empleadoAEliminar['foto'] es '/SistemaParaRRHH/views/fotos/01.jpg'
                // la ruta física sería 'C:/xampp/htdocs/SistemaParaRRHH/views/fotos/01.jpg'
                // Pero tu `dif_destino` original en `set_Empleado` era '/SistemaParaRRHH/views/fotos/'
                // y luego hacías `basename()`. Así que en la BD solo debería estar 'nombre_archivo.jpg'.
                // La ruta física sería: $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/' . $empleadoAEliminar['foto']
                // O, si $empleadoAEliminar['foto'] ya tiene '/SistemaParaRRHH/views/fotos/', entonces:
                // $_SERVER['DOCUMENT_ROOT'] . $empleadoAEliminar['foto']
                // Por tu `set_Empleado`, parece que guardas la ruta completa desde la raíz del proyecto web.
                // $pathFotoServidor = $_SERVER['DOCUMENT_ROOT'] . $empleadoAEliminar['foto'];

                // CORRECCIÓN BASADA EN TU set_Empleado:
                // $dif_destino='/SistemaParaRRHH/views/fotos/';
                // $foto=$dif_destino.basename($_FILES['foto']['name']);
                // Esto significa que en la BD guardas algo como: /SistemaParaRRHH/views/fotos/nombre_archivo.jpg
                // Entonces la ruta en el servidor es:
                // C:\xampp\htdocs (DOCUMENT_ROOT) + /SistemaParaRRHH/views/fotos/nombre_archivo.jpg
                // Se necesita ajustar esto para que sea consistente.
                // Por ahora, asumiré que $empleadoAEliminar['foto'] contiene solo el nombre del archivo.
                $pathFotoServidor = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/' . basename($empleadoAEliminar['foto']);
            }


            // 1. Eliminar de departamento (si id_d = id_e)
            $sql_dep = "DELETE FROM departamento WHERE id_d = ?";
            if ($this->change == 'PDO') {
                $pst_d = $this->db->prepare($sql_dep);
                $pst_d->bindParam(1, $id_e, PDO::PARAM_INT);
                if (!$pst_d->execute()) throw new Exception("Error al eliminar departamento.");
            } elseif ($this->change == 'mysqli') {
                $pst_d = $this->db->prepare($sql_dep);
                $pst_d->bind_param('i', $id_e);
                if (!$pst_d->execute()) throw new Exception("Error al eliminar departamento.");
                $pst_d->close();
            }

            // 2. Eliminar de contrato (si id_c = id_e y no tienes ON DELETE CASCADE, o para ser explícito)
            // Si tienes ON DELETE CASCADE en la FK de contrato referenciando empleado, este paso no es estrictamente necesario
            // pero no hace daño (a menos que el ON DELETE CASCADE ya lo haya borrado, entonces no encontrará nada).
            $sql_con = "DELETE FROM contrato WHERE id_c = ?";
            if ($this->change == 'PDO') {
                $pst_c = $this->db->prepare($sql_con);
                $pst_c->bindParam(1, $id_e, PDO::PARAM_INT);
                if (!$pst_c->execute()) throw new Exception("Error al eliminar contrato.");
            } elseif ($this->change == 'mysqli') {
                $pst_c = $this->db->prepare($sql_con);
                $pst_c->bind_param('i', $id_e);
                if (!$pst_c->execute()) throw new Exception("Error al eliminar contrato.");
                $pst_c->close();
            }

            // 3. Eliminar de empleado (esto debería activar ON DELETE CASCADE para contrato si está configurado correctamente)
            $sql_emp = "DELETE FROM empleado WHERE id_e = ?";
            if ($this->change == 'PDO') {
                $pst_e = $this->db->prepare($sql_emp);
                $pst_e->bindParam(1, $id_e, PDO::PARAM_INT);
                if (!$pst_e->execute()) throw new Exception("Error al eliminar empleado.");
            } elseif ($this->change == 'mysqli') {
                $pst_e = $this->db->prepare($sql_emp);
                $pst_e->bind_param('i', $id_e);
                if (!$pst_e->execute()) throw new Exception("Error al eliminar empleado.");
                $pst_e->close();
            }

            // Si todo fue bien, confirmar transacción
            if ($this->change == 'PDO') $this->db->commit();
            elseif ($this->change == 'mysqli') $this->db->commit();

            // Eliminar el archivo de la foto DESPUÉS de confirmar la transacción de BD
            if ($pathFotoServidor && file_exists($pathFotoServidor)) {
                unlink($pathFotoServidor);
            }

            return true;

        } catch (Exception $e) {
            if ($this->change == 'PDO') $this->db->rollBack();
            elseif ($this->change == 'mysqli') $this->db->rollback();
            error_log("Error en eliminar Empleado: " . $e->getMessage());
            return false;
        }
        // $this->db = null; // No cerrar conexión aquí
    }
}
?>