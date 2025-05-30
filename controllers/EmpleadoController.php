<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class EmpleadoController {
    private $empleadoModel;
    private $departamentoModel; // Necesitaremos el modelo de departamento

    public function __construct(){
        require_once "../models/EmpleadoModel.php";
        $this->empleadoModel = new EmpleadoModel();

        // Cargar también el modelo de Departamento
        require_once "../models/DepartamentoModel.php";
        $this->departamentoModel = new DepartamentoModel();
    }

    public function index(){
        verificarAcceso(['jefe', 'empleado']);
        $data["titulo"] = "Empleados";
        // El EmpleadoModel->get_empleado() ya debería hacer el JOIN con departamento
        $data["empleado"] = $this->empleadoModel->get_empleado();

        $conexionTemp = Conectar::conexion();
        if ($conexionTemp) {
            $tipoConexion = get_class($conexionTemp);
            $data["comprobar"] = ($tipoConexion == 'PDO') ? "Trabajando con PDO" : "Trabajando con MySQLi";
            $conexionTemp = null;
        } else {
            $data["comprobar"] = "Error al obtener tipo de conexión";
        }
        require_once "../views/empleados/listarempleados.php";
    }

    public function nuevo(){
        verificarAcceso(['jefe']);
        $data["titulo"] = "Nuevo Empleado";
        // Cargar la lista de departamentos para el <select>
        $data['departamentos'] = $this->departamentoModel->get_departamentos();
        require_once "../views/empleados/empleados_nuevo.php";
    }

    public function guarda(){
        verificarAcceso(['jefe']);
        $data["titulo"] = "Empleados";

        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        // MODIFICADO: Ahora recibimos id_departamento
        $id_departamento = filter_var($_POST['id_departamento'] ?? '', FILTER_VALIDATE_INT);
        if(($_POST['id_departamento'] ?? '') === '') $id_departamento = null; // Permitir NULL si se envía vacío

        $fecha_inicio_contrato = $_POST['fecha_inicio'] ?? '';
        $fecha_fin_contrato = $_POST['fecha_fin'] ?? '';
        $salario_base = filter_input(INPUT_POST, 'salario_base', FILTER_VALIDATE_FLOAT);


        $errores = [];
        if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
        if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
        if (empty($fecha_nacimiento)) $errores[] = "La fecha de nacimiento es obligatoria.";
        // Validación para id_departamento (puedes hacerla más estricta si siempre es requerido)
        if ($id_departamento === false && $id_departamento !== null) { // Si no es INT válido y no es explícitamente NULL
             $errores[] = "Departamento no válido.";
        }
        // ... más validaciones ...

        $pathFotoParaBD = null;
        $nombreArchivoFoto = null;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            // ... (tu lógica de subida de foto se mantiene igual) ...
            $fotoInfo = $_FILES['foto'];
            $nombreArchivoFoto = basename($fotoInfo["name"]);
            $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/';
            $rutaArchivoDestino = $directorioDestino . $nombreArchivoFoto;
            $tipoArchivo = strtolower(pathinfo($rutaArchivoDestino, PATHINFO_EXTENSION));
            $tamañoArchivo = $fotoInfo["size"];

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0775, true);
            }

            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                $errores[] = "Tipo de archivo no permitido para la foto (solo JPG, JPEG, PNG, GIF).";
            }
            if ($tamañoArchivo > 2 * 1024 * 1024) {
                $errores[] = "El tamaño de la foto no debe exceder los 2MB.";
            }

            if (empty($errores)) {
                $contador = 0;
                $nombreArchivoOriginal = pathinfo($nombreArchivoFoto, PATHINFO_FILENAME);
                while (file_exists($directorioDestino . $nombreArchivoFoto)) {
                    $contador++;
                    $nombreArchivoFoto = $nombreArchivoOriginal . "_" . $contador . "." . $tipoArchivo;
                }
                $rutaArchivoDestino = $directorioDestino . $nombreArchivoFoto;

                if (move_uploaded_file($fotoInfo["tmp_name"], $rutaArchivoDestino)) {
                    $pathFotoParaBD = $nombreArchivoFoto;
                } else {
                    $errores[] = "Error al subir la foto. Verifique permisos en el directorio 'fotos'.";
                }
            }
        } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE && $_FILES['foto']['error'] != UPLOAD_ERR_INI_SIZE) {
            $errores[] = "Error al procesar el archivo de la foto. Código: " . $_FILES['foto']['error'];
        }
        
        if (empty($pathFotoParaBD) && (empty($_POST['id_e'])) && (!isset($_FILES['foto']) || $_FILES['foto']['error'] == UPLOAD_ERR_NO_FILE || $_FILES['foto']['error'] == UPLOAD_ERR_INI_SIZE) ) {
            if(empty($_POST['id_e'])) {
                 $errores[] = "La foto es obligatoria para nuevos empleados.";
            }
        }


        if (!empty($errores)) {
            $data["titulo"] = "Nuevo Empleado";
            $data["errores"] = $errores;
            $data['input'] = $_POST;
            // Volver a cargar departamentos para el formulario
            $data['departamentos'] = $this->departamentoModel->get_departamentos();
            require_once "../views/empleados/empleados_nuevo.php";
            return;
        }

        // MODIFICADO: Pasar $id_departamento al modelo
        // Ya no se pasan nombre_departamento ni ubicacion_departamento
        $exito = $this->empleadoModel->set_Empleado(
            $nombre,
            $apellido,
            $pathFotoParaBD,
            $fecha_nacimiento,
            $id_departamento, // ID del departamento seleccionado
            $fecha_inicio_contrato,
            $fecha_fin_contrato,
            (float)$salario_base // Asegurar que es float
        );

        if ($exito) {
            $_SESSION['mensaje_exito'] = "Empleado guardado correctamente.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        } else {
            $data["titulo"] = "Nuevo Empleado";
            $data["errores"] = ["Hubo un error al guardar el empleado en la base de datos."];
            $data['input'] = $_POST;
            $data['departamentos'] = $this->departamentoModel->get_departamentos();
            require_once "../views/empleados/empleados_nuevo.php";
        }
    }


    public function modificar($id_e = null){
        verificarAcceso(['jefe']);
        // ... (tu lógica para obtener id_e se mantiene) ...
        if ($id_e === null && isset($_GET['id'])) {
            $id_e = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        } elseif ($id_e !== null) {
            $id_e = filter_var($id_e, FILTER_VALIDATE_INT);
        }

        if (!$id_e) {
            $_SESSION['mensaje_error'] = "ID de empleado no válido.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }

        $empleado = $this->empleadoModel->get_empleado_por_id($id_e);

        if (!$empleado) {
            $_SESSION['mensaje_error'] = "Empleado no encontrado.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }

        $data["titulo"] = "Modificar Empleado";
        $data["empleado_data"] = $empleado;
        // Cargar la lista de departamentos para el <select>
        $data['departamentos'] = $this->departamentoModel->get_departamentos();
        require_once "../views/empleados/empleados_modificar.php";
    }

    public function actualizar(){
        verificarAcceso(['jefe']);
        // ... (tu lógica para obtener datos del POST se mantiene, pero ajustamos para id_departamento) ...
        if (!isset($_POST['id_e'])) {
            $_SESSION['mensaje_error'] = "ID de empleado no proporcionado para actualizar.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }

        $id_e = filter_var($_POST['id_e'], FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        // MODIFICADO: Ahora recibimos id_departamento
        $id_departamento = filter_var($_POST['id_departamento'] ?? '', FILTER_VALIDATE_INT);
        if(($_POST['id_departamento'] ?? '') === '') $id_departamento = null;


        $fecha_inicio_contrato = $_POST['fecha_inicio'] ?? '';
        $fecha_fin_contrato = $_POST['fecha_fin'] ?? '';
        $salario_base = filter_input(INPUT_POST, 'salario_base', FILTER_VALIDATE_FLOAT);
        $foto_actual = $_POST['foto_actual'] ?? null;

        $errores = [];
        if (!$id_e) $errores[] = "ID de empleado inválido.";
        if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
        if ($id_departamento === false && $id_departamento !== null) {
             $errores[] = "Departamento no válido.";
        }
        // ... más validaciones ...

        $pathFotoParaBD = $foto_actual;
        $nombreArchivoFotoNueva = null;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            // ... (tu lógica de subida de foto se mantiene igual) ...
            $fotoInfo = $_FILES['foto'];
            $nombreArchivoFotoNueva = basename($fotoInfo["name"]);
            $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/';
            // ... (resto de la lógica de subida)
            if (!is_dir($directorioDestino)) mkdir($directorioDestino, 0775, true);
            $rutaArchivoDestino = $directorioDestino . $nombreArchivoFotoNueva; // Path antes de verificar duplicados
            $tipoArchivo = strtolower(pathinfo($rutaArchivoDestino, PATHINFO_EXTENSION));
            $tamañoArchivo = $fotoInfo["size"];
            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($tipoArchivo, $tiposPermitidos)) $errores[] = "Tipo de archivo no permitido.";
            if ($tamañoArchivo > 2 * 1024 * 1024) $errores[] = "Tamaño de archivo excede 2MB.";

            if (empty($errores)) {
                $contador = 0;
                $nombreBase = pathinfo($nombreArchivoFotoNueva, PATHINFO_FILENAME);
                $extension = pathinfo($nombreArchivoFotoNueva, PATHINFO_EXTENSION);
                $nombreFinalArchivo = $nombreArchivoFotoNueva;
                while (file_exists($directorioDestino . $nombreFinalArchivo)) {
                    $contador++;
                    $nombreFinalArchivo = $nombreBase . "_" . $contador . "." . $extension;
                }
                $rutaArchivoDestino = $directorioDestino . $nombreFinalArchivo;

                if (move_uploaded_file($fotoInfo["tmp_name"], $rutaArchivoDestino)) {
                    $pathFotoParaBD = $nombreFinalArchivo;
                    if ($foto_actual && $foto_actual != $pathFotoParaBD && file_exists($directorioDestino . $foto_actual)) {
                        unlink($directorioDestino . $foto_actual);
                    }
                } else {
                    $errores[] = "Error al subir la nueva foto.";
                }
            }
        } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE && $_FILES['foto']['error'] != UPLOAD_ERR_INI_SIZE) {
            $errores[] = "Error al procesar archivo de foto. Código: " . $_FILES['foto']['error'];
        }


        if (!empty($errores)) {
            $data["titulo"] = "Modificar Empleado";
            $data["errores"] = $errores;
            $data["empleado_data"] = $_POST;
            $data["empleado_data"]['id_e'] = $id_e; // Asegurar que el ID se mantenga
            if(isset($foto_actual)) $data["empleado_data"]['foto'] = $foto_actual;
            $data['departamentos'] = $this->departamentoModel->get_departamentos();
            require_once "../views/empleados/empleados_modificar.php";
            return;
        }

        // MODIFICADO: Pasar $id_departamento al modelo
        // Ya no se pasan nombre_departamento ni ubicacion_departamento
        $exito = $this->empleadoModel->update_Empleado(
            $id_e,
            $nombre,
            $apellido,
            ($nombreArchivoFotoNueva !== null) ? $pathFotoParaBD : $foto_actual,
            $fecha_nacimiento,
            $id_departamento, // ID del departamento seleccionado
            $fecha_inicio_contrato,
            $fecha_fin_contrato,
            (float)$salario_base // Asegurar que es float
        );

        if ($exito) {
            $_SESSION['mensaje_exito'] = "Empleado actualizado correctamente.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        } else {
            $data["titulo"] = "Modificar Empleado";
            $data["errores"] = ["Hubo un error al actualizar el empleado o no hubo cambios."];
            $data["empleado_data"] = $_POST; // Usar $_POST para repoblar
            $data["empleado_data"]['id_e'] = $id_e;
            if(isset($foto_actual)) $data["empleado_data"]['foto'] = $foto_actual;
            $data['departamentos'] = $this->departamentoModel->get_departamentos();
            require_once "../views/empleados/empleados_modificar.php";
        }
    }

    // Tus métodos eliminar() y verBoleta() se mantienen igual por ahora.
    public function eliminar($id_e = null){ // ... (código existente) ...
        verificarAcceso(['jefe']);
         if ($id_e === null && isset($_GET['id'])) {
            $id_e = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        } elseif ($id_e !== null) {
            $id_e = filter_var($id_e, FILTER_VALIDATE_INT);
        }

        if (!$id_e) {
            $_SESSION['mensaje_error'] = "ID de empleado no válido para eliminar.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }
        $exito = $this->empleadoModel->eliminar($id_e);
        if ($exito) {
            $_SESSION['mensaje_exito'] = "Empleado eliminado correctamente.";
        } else {
            $_SESSION['mensaje_error'] = "Error al eliminar el empleado.";
        }
        header("Location: crud.php?c=empleado&a=index");
        exit();
    }

    public function verBoleta($id_empleado = null){ // ... (código existente) ...
        verificarAcceso(['jefe', 'empleado']);
        if ($id_empleado === null && isset($_GET['id_empleado'])) {
            $id_empleado = filter_var($_GET['id_empleado'], FILTER_VALIDATE_INT);
        } elseif ($id_empleado !== null) {
            $id_empleado = filter_var($id_empleado, FILTER_VALIDATE_INT);
        }

        if (!$id_empleado) {
             $_SESSION['mensaje_error'] = "ID de empleado no válido para ver boleta.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }
        if ($_SESSION['rol'] == 'empleado') {
            // Aquí deberías comparar $id_empleado con el ID del empleado asociado al $_SESSION['usuario_id']
            // Esta lógica es un poco más compleja y requiere obtener el id_e del usuario actual.
            // Por ahora, se simplifica.
            // echo "Lógica para rol empleado viendo su propia boleta PENDIENTE.";
        }
        $empleado = $this->empleadoModel->get_empleado_por_id($id_empleado);
        if (!$empleado) {
            $_SESSION['mensaje_error'] = "Empleado no encontrado para generar boleta.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }
        $data['titulo'] = "Boleta de Pago - " . htmlspecialchars($empleado['nombre'] . " " . $empleado['apellido']);
        $data['empleado'] = $empleado;
        echo "<pre>Vista de Boleta para Empleado ID: " . htmlspecialchars($id_empleado) . "\n";
        print_r($empleado);
        echo "</pre>";
        echo "<a href='crud.php?c=empleado&a=index'>Volver al listado</a>";
    }
}
?>