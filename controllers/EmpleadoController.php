<?php
// Asegúrate que helpers.php y su función verificarAcceso estén disponibles.
// Si session_start() no está en helpers.php o crud.php, ponlo aquí.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// require_once "../config/helpers.php"; // Si no está incluido globalmente

class EmpleadoController {
    private $empleadoModel; // Cambiado nombre de propiedad para claridad
    // private $db; // La conexión la maneja el modelo
    // private $change; // El tipo de conexión lo maneja el modelo

    public function __construct(){
        require_once "../models/EmpleadoModel.php";
        $this->empleadoModel = new EmpleadoModel(); // Usar la propiedad
        // La conexión a la BD y el tipo (PDO/MySQLi) son manejados por el EmpleadoModel.
    }

    public function index(){
        verificarAcceso(['jefe', 'empleado']);
        $data["titulo"] = "Empleados";
        $data["empleado"] = $this->empleadoModel->get_empleado(); // Usar la propiedad

        // Para el mensaje PDO/MySQLi (opcional, puede ir en una vista de config o helper)
        $conexionTemp = Conectar::conexion(); // Obtener una instancia para verificar tipo
        if ($conexionTemp) {
            $tipoConexion = get_class($conexionTemp);
            if ($tipoConexion == 'PDO') {
                $data["comprobar"] = "Estas trabajando con PDO";
            } elseif ($tipoConexion == 'mysqli') {
                $data["comprobar"] = "Estas trabajando con MySQLi";
            } else {
                $data["comprobar"] = "Tipo de conexión desconocido";
            }
            $conexionTemp = null; // Cerrar o liberar la conexión temporal si es necesario
        } else {
            $data["comprobar"] = "Error al obtener tipo de conexión";
        }


        require_once "../views/empleados/listarempleados.php";
    }

    public function nuevo(){
        verificarAcceso(['jefe']);
        $data["titulo"] = "Nuevo Empleado";
        // Aquí podrías cargar una lista de departamentos si la BD ya está normalizada
        // require_once "../models/DepartamentoModel.php";
        // $deptoModel = new DepartamentoModel();
        // $data['departamentos'] = $deptoModel->get_departamentos();
        require_once "../views/empleados/empleados_nuevo.php";
    }

    public function guarda(){
        verificarAcceso(['jefe']);
        $data["titulo"] = "Empleados"; // Título para la vista si hay error o redirección

        // Validación básica de campos (puedes mejorarla)
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $fecha_inicio_contrato = $_POST['fecha_inicio'] ?? '';
        $fecha_fin_contrato = $_POST['fecha_fin'] ?? '';
        $salario_base = filter_var($_POST['salario_base'] ?? '', FILTER_VALIDATE_FLOAT);
        // Para departamento, según tu lógica actual:
        $nombre_departamento = trim($_POST['nombre_d'] ?? '');
        $ubicacion_departamento = trim($_POST['ubicacion'] ?? '');

        $errores = [];
        if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
        if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
        if (empty($fecha_nacimiento)) $errores[] = "La fecha de nacimiento es obligatoria.";
        // ... más validaciones para fechas, salario, etc.

        // Manejo de la subida de la foto
        $pathFotoParaBD = null; // Ruta que se guardará en la BD (solo nombre de archivo o ruta relativa)
        $nombreArchivoFoto = null;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            $fotoInfo = $_FILES['foto'];
            $nombreArchivoFoto = basename($fotoInfo["name"]);
            $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/'; // Asegúrate que esta ruta sea correcta y con permisos de escritura
            $rutaArchivoDestino = $directorioDestino . $nombreArchivoFoto;
            $tipoArchivo = strtolower(pathinfo($rutaArchivoDestino, PATHINFO_EXTENSION));
            $tamañoArchivo = $fotoInfo["size"];

            // Crear directorio si no existe (buena práctica)
            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0775, true);
            }

            // Validaciones de la foto
            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                $errores[] = "Tipo de archivo no permitido para la foto (solo JPG, JPEG, PNG, GIF).";
            }
            if ($tamañoArchivo > 2 * 1024 * 1024) { // 2MB
                $errores[] = "El tamaño de la foto no debe exceder los 2MB.";
            }

            if (empty($errores)) { // Solo intentar mover si no hay errores previos ni de validación de foto
                // Evitar sobrescribir si ya existe un archivo con el mismo nombre (puedes añadir un prefijo único)
                $contador = 0;
                $nombreArchivoOriginal = pathinfo($nombreArchivoFoto, PATHINFO_FILENAME);
                while (file_exists($directorioDestino . $nombreArchivoFoto)) {
                    $contador++;
                    $nombreArchivoFoto = $nombreArchivoOriginal . "_" . $contador . "." . $tipoArchivo;
                }
                $rutaArchivoDestino = $directorioDestino . $nombreArchivoFoto;

                if (move_uploaded_file($fotoInfo["tmp_name"], $rutaArchivoDestino)) {
                    // Foto subida con éxito, guardar solo el nombre del archivo en la BD
                    $pathFotoParaBD = $nombreArchivoFoto;
                } else {
                    $errores[] = "Error al subir la foto. Verifique permisos en el directorio 'fotos'.";
                }
            }
        } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE) {
            // Hubo un error diferente a "no se subió archivo"
            $errores[] = "Error al procesar el archivo de la foto. Código: " . $_FILES['foto']['error'];
        }
        // Si el campo foto era 'required' en el HTML y no se subió, deberías añadir un error.
        // Tu HTML tiene `required` para foto.
        if (empty($pathFotoParaBD) && (!isset($_FILES['foto']) || $_FILES['foto']['error'] == UPLOAD_ERR_NO_FILE) ) {
             // Si es un nuevo empleado y la foto es requerida
            if(empty($_POST['id_e'])) { // Solo para nuevos, para modificar puede no subirse foto nueva
                 $errores[] = "La foto es obligatoria.";
            }
        }


        if (!empty($errores)) {
            // Hay errores, volver al formulario de nuevo empleado mostrando los errores
            $data["titulo"] = "Nuevo Empleado";
            $data["errores"] = $errores;
            // Pasar los datos ingresados de vuelta al formulario para no perderlos
            $data['input'] = $_POST;
            require_once "../views/empleados/empleados_nuevo.php";
            return; // Detener ejecución
        }

        // Si no hay errores, proceder a guardar en la BD
        $exito = $this->empleadoModel->set_Empleado(
            $nombre,
            $apellido,
            $pathFotoParaBD, // Solo el nombre del archivo
            $fecha_nacimiento,
            $fecha_inicio_contrato,
            $fecha_fin_contrato,
            $salario_base,
            $nombre_departamento,
            $ubicacion_departamento
        );

        if ($exito) {
            // Redirigir al listado con mensaje de éxito (usar sesiones para mensajes flash es mejor)
            $_SESSION['mensaje_exito'] = "Empleado guardado correctamente.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        } else {
            // Error al guardar en BD
            $data["titulo"] = "Nuevo Empleado";
            $data["errores"] = ["Hubo un error al guardar el empleado en la base de datos."];
            $data['input'] = $_POST;
            require_once "../views/empleados/empleados_nuevo.php";
        }
    }


    public function modificar($id_e = null){
        verificarAcceso(['jefe']);
        if ($id_e === null && isset($_GET['id'])) { // Compatibilidad si el ID viene por GET 'id'
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
        $data["empleado_data"] = $empleado; // Pasar los datos del empleado a la vista
        // Aquí también podrías cargar la lista de departamentos si es necesario
        // $data['departamentos'] = $this->departamentoModel->get_departamentos();
        require_once "../views/empleados/empleados_modificar.php"; // Nueva vista
    }

    public function actualizar(){
        verificarAcceso(['jefe']);
        $data["titulo"] = "Modificar Empleado"; // Para errores

        if (!isset($_POST['id_e'])) {
            $_SESSION['mensaje_error'] = "ID de empleado no proporcionado para actualizar.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }

        $id_e = filter_var($_POST['id_e'], FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $fecha_inicio_contrato = $_POST['fecha_inicio'] ?? '';
        $fecha_fin_contrato = $_POST['fecha_fin'] ?? '';
        $salario_base = filter_var($_POST['salario_base'] ?? '', FILTER_VALIDATE_FLOAT);
        $nombre_departamento = trim($_POST['nombre_d'] ?? '');
        $ubicacion_departamento = trim($_POST['ubicacion'] ?? '');
        $foto_actual = $_POST['foto_actual'] ?? null; // Para mantener la foto si no se sube una nueva

        $errores = [];
        if (!$id_e) $errores[] = "ID de empleado inválido.";
        if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
        // ... más validaciones ...

        $pathFotoParaBD = $foto_actual; // Por defecto, mantener la foto actual si no se sube una nueva
        $nombreArchivoFotoNueva = null;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            $fotoInfo = $_FILES['foto'];
            $nombreArchivoFotoNueva = basename($fotoInfo["name"]);
            $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/';
            $rutaArchivoDestino = $directorioDestino . $nombreArchivoFotoNueva;
            $tipoArchivo = strtolower(pathinfo($rutaArchivoDestino, PATHINFO_EXTENSION));
            $tamañoArchivo = $fotoInfo["size"];

            if (!is_dir($directorioDestino)) mkdir($directorioDestino, 0775, true);

            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($tipoArchivo, $tiposPermitidos)) $errores[] = "Tipo de archivo no permitido.";
            if ($tamañoArchivo > 2 * 1024 * 1024) $errores[] = "Tamaño de archivo excede 2MB.";

            if (empty($errores)) {
                $contador = 0;
                $nombreArchivoOriginal = pathinfo($nombreArchivoFotoNueva, PATHINFO_FILENAME);
                while (file_exists($directorioDestino . $nombreArchivoFotoNueva)) {
                    $contador++;
                    $nombreArchivoFotoNueva = $nombreArchivoOriginal . "_" . $contador . "." . $tipoArchivo;
                }
                $rutaArchivoDestino = $directorioDestino . $nombreArchivoFotoNueva;

                if (move_uploaded_file($fotoInfo["tmp_name"], $rutaArchivoDestino)) {
                    $pathFotoParaBD = $nombreArchivoFotoNueva; // Nueva foto subida
                    // Opcional: borrar la foto anterior si $foto_actual existe y es diferente
                    if ($foto_actual && $foto_actual != $pathFotoParaBD && file_exists($directorioDestino . $foto_actual)) {
                        unlink($directorioDestino . $foto_actual);
                    }
                } else {
                    $errores[] = "Error al subir la nueva foto.";
                }
            }
        } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE) {
            $errores[] = "Error al procesar archivo de foto. Código: " . $_FILES['foto']['error'];
        }

        if (!empty($errores)) {
            $data["errores"] = $errores;
            $data["empleado_data"] = $_POST; // Re-poblar formulario con datos ingresados
            $data["empleado_data"]['id_e'] = $id_e; // Asegurar que el ID se mantenga
            if(isset($foto_actual)) $data["empleado_data"]['foto'] = $foto_actual; // Mantener el nombre de la foto actual para la vista
            require_once "../views/empleados/empleados_modificar.php";
            return;
        }

        $exito = $this->empleadoModel->update_Empleado(
            $id_e,
            $nombre,
            $apellido,
            ($nombreArchivoFotoNueva !== null) ? $pathFotoParaBD : $foto_actual, // Pasar la nueva foto o la actual
            $fecha_nacimiento,
            $fecha_inicio_contrato,
            $fecha_fin_contrato,
            $salario_base,
            $nombre_departamento,
            $ubicacion_departamento
        );

        if ($exito) {
            $_SESSION['mensaje_exito'] = "Empleado actualizado correctamente.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        } else {
            $data["errores"] = ["Hubo un error al actualizar el empleado."];
            $data["empleado_data"] = $_POST;
            $data["empleado_data"]['id_e'] = $id_e;
            if(isset($foto_actual)) $data["empleado_data"]['foto'] = $foto_actual;
            require_once "../views/empleados/empleados_modificar.php";
        }
    }


    public function eliminar($id_e = null){
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

        // Opcional: obtener el nombre de la foto para borrarla del servidor ANTES de borrar de BD
        // $empleado = $this->empleadoModel->get_empleado_por_id($id_e);
        // $foto_a_borrar = ($empleado && !empty($empleado['foto'])) ? $empleado['foto'] : null;

        $exito = $this->empleadoModel->eliminar($id_e);

        if ($exito) {
            // if ($foto_a_borrar) {
            //     $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/';
            //     if (file_exists($directorioDestino . $foto_a_borrar)) {
            //         unlink($directorioDestino . $foto_a_borrar);
            //     }
            // }
            // La lógica de borrar la foto ya está en el modelo EmpleadoModel::eliminar()
            $_SESSION['mensaje_exito'] = "Empleado eliminado correctamente.";
        } else {
            $_SESSION['mensaje_error'] = "Error al eliminar el empleado.";
        }
        header("Location: crud.php?c=empleado&a=index");
        exit();
    }

    // Pendiente: acción para verBoleta
    public function verBoleta($id_empleado = null){
        verificarAcceso(['jefe', 'empleado']); // O quien pueda ver boletas
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

        // Si es un rol 'empleado', solo permitir ver su propia boleta
        if ($_SESSION['rol'] == 'empleado') {
            // Necesitarías una forma de obtener el id_e del usuario logueado si es un empleado
            // Esto es un ejemplo, requeriría que $_SESSION['empleado_id_bd'] se establezca en el login
            // if (!isset($_SESSION['empleado_id_bd']) || $_SESSION['empleado_id_bd'] != $id_empleado) {
            //     $_SESSION['mensaje_error'] = "No tiene permisos para ver esta boleta.";
            //     header("Location: crud.php?c=empleado&a=index");
            //     exit();
            // }
            echo "Lógica para rol empleado viendo su propia boleta PENDIENTE.";
            // Por ahora, permitimos si tiene acceso general.
        }


        $empleado = $this->empleadoModel->get_empleado_por_id($id_empleado);
        if (!$empleado) {
            $_SESSION['mensaje_error'] = "Empleado no encontrado para generar boleta.";
            header("Location: crud.php?c=empleado&a=index");
            exit();
        }

        $data['titulo'] = "Boleta de Pago - " . htmlspecialchars($empleado['nombre'] . " " . $empleado['apellido']);
        $data['empleado'] = $empleado;
        // Aquí cargarías una vista específica para la boleta:
        // require_once "../views/empleados/boleta_empleado.php";
        echo "<pre>Vista de Boleta para Empleado ID: " . htmlspecialchars($id_empleado) . "\n";
        print_r($empleado);
        echo "</pre>";
        echo "<a href='crud.php?c=empleado&a=index'>Volver al listado</a>";
    }
}
?>