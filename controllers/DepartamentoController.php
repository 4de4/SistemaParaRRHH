<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Asumiendo que helpers.php y verificarAcceso() están disponibles globalmente
// require_once "../helpers.php"; // Si no lo está

class DepartamentoController {
    private $departamentoModel;

    public function __construct() {
        require_once "../models/DepartamentoModel.php";
        $this->departamentoModel = new DepartamentoModel();
    }

    public function index() {
        verificarAcceso(['jefe', 'empleado']); // Quienes pueden ver la lista
        $data["titulo"] = "Departamentos";
        $data["departamentos"] = $this->departamentoModel->get_departamentos();
        
        // Mensaje PDO/MySQLi (opcional)
        $conexionTemp = Conectar::conexion();
         if ($conexionTemp) {
            $tipoConexion = get_class($conexionTemp);
            $data["comprobar"] = ($tipoConexion == 'PDO') ? "Trabajando con PDO" : "Trabajando con MySQLi";
            $conexionTemp = null;
        } else {
            $data["comprobar"] = "Error al obtener tipo de conexión";
        }

        require_once "../views/departamentos/listardepartamentos.php";
    }

    public function nuevo() {
        verificarAcceso(['jefe']); // Solo admin/jefe puede crear
        $data["titulo"] = "Nuevo Departamento";
        require_once "../views/departamentos/departamentos_nuevo.php";
    }

    public function guarda() {
        verificarAcceso(['jefe']);
        $nombre = trim($_POST['nombre'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $data["titulo"] = "Departamentos"; // Para la vista si hay error

        $errores = [];
        if (empty($nombre)) {
            $errores[] = "El nombre del departamento es obligatorio.";
        }
        if (empty($ubicacion)) {
            $errores[] = "La ubicación es obligatoria.";
        }

        if (!empty($errores)) {
            $data["errores"] = $errores;
            $data["input"] = $_POST; // Repoblar formulario
            $data["titulo"] = "Nuevo Departamento"; // Corregir título para vista de error
            require_once "../views/departamentos/departamentos_nuevo.php";
            return;
        }

        $resultado = $this->departamentoModel->insertar_departamento($nombre, $ubicacion);

        if ($resultado === 'duplicado') {
            $data["errores"] = ["El nombre del departamento ya existe."];
            $data["input"] = $_POST;
            $data["titulo"] = "Nuevo Departamento";
            require_once "../views/departamentos/departamentos_nuevo.php";
        } elseif ($resultado) { // Si es un ID (true en booleano)
            $_SESSION['mensaje_exito'] = "Departamento guardado correctamente.";
            header("Location: crud.php?c=departamento&a=index");
            exit();
        } else {
            $data["errores"] = ["Hubo un error al guardar el departamento."];
            $data["input"] = $_POST;
            $data["titulo"] = "Nuevo Departamento";
            require_once "../views/departamentos/departamentos_nuevo.php";
        }
    }

    public function modificar($id_d = null) {
        verificarAcceso(['jefe']);
         if ($id_d === null && isset($_GET['id'])) {
            $id_d = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        } elseif($id_d !== null) {
             $id_d = filter_var($id_d, FILTER_VALIDATE_INT);
        }


        if (!$id_d) {
            $_SESSION['mensaje_error'] = "ID de departamento no válido.";
            header("Location: crud.php?c=departamento&a=index");
            exit();
        }

        $departamento = $this->departamentoModel->get_departamento_por_id($id_d);

        if (!$departamento) {
            $_SESSION['mensaje_error'] = "Departamento no encontrado.";
            header("Location: crud.php?c=departamento&a=index");
            exit();
        }

        $data["titulo"] = "Modificar Departamento";
        $data["departamento_data"] = $departamento;
        require_once "../views/departamentos/departamentos_modificar.php";
    }

    public function actualizar() {
        verificarAcceso(['jefe']);
        if (!isset($_POST['id_d'])) {
            $_SESSION['mensaje_error'] = "ID de departamento no proporcionado.";
            header("Location: crud.php?c=departamento&a=index");
            exit();
        }

        $id_d = filter_var($_POST['id_d'], FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $data["titulo"] = "Modificar Departamento"; // Para la vista de error

        $errores = [];
        if (!$id_d) $errores[] = "ID de departamento inválido.";
        if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
        if (empty($ubicacion)) $errores[] = "La ubicación es obligatoria.";

        if (!empty($errores)) {
            $data["errores"] = $errores;
            $data["departamento_data"] = $_POST; // Repoblar
            require_once "../views/departamentos/departamentos_modificar.php";
            return;
        }

        $resultado = $this->departamentoModel->actualizar_departamento($id_d, $nombre, $ubicacion);

        if ($resultado === 'duplicado') {
            $data["errores"] = ["El nombre del departamento ya existe."];
            $data["departamento_data"] = $_POST;
            require_once "../views/departamentos/departamentos_modificar.php";
        } elseif ($resultado === true) { // Actualización exitosa
            $_SESSION['mensaje_exito'] = "Departamento actualizado correctamente.";
            header("Location: crud.php?c=departamento&a=index");
            exit();
        } elseif ($resultado === false && $resultado !== 'duplicado') { // Error genérico o no se afectaron filas
             // Si $resultado es false pero no 'duplicado', puede ser que no se afectaron filas (datos iguales) o un error.
            // Si quieres un mensaje específico para "no cambios":
            // $_SESSION['mensaje_info'] = "No se realizaron cambios en el departamento (datos iguales) o error.";
            // Por ahora, un error genérico si no fue duplicado y no fue true.
            $data["errores"] = ["No se pudo actualizar el departamento o no hubo cambios."];
            $data["departamento_data"] = $_POST;
            require_once "../views/departamentos/departamentos_modificar.php";
        } else { // Si $resultado fue false específicamente (no 'duplicado' y no true)
            $data["errores"] = ["Error al actualizar el departamento."];
            $data["departamento_data"] = $_POST;
            require_once "../views/departamentos/departamentos_modificar.php";
        }
    }

    public function eliminar($id_d = null) {
        verificarAcceso(['jefe']);
        if ($id_d === null && isset($_GET['id'])) {
            $id_d = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        } elseif($id_d !== null) {
             $id_d = filter_var($id_d, FILTER_VALIDATE_INT);
        }

        if (!$id_d) {
            $_SESSION['mensaje_error'] = "ID de departamento no válido.";
            header("Location: crud.php?c=departamento&a=index");
            exit();
        }

        $resultado = $this->departamentoModel->eliminar_departamento($id_d);

        if ($resultado === true) {
            $_SESSION['mensaje_exito'] = "Departamento eliminado correctamente.";
        } elseif ($resultado === 'en_uso') { // Si implementas esta lógica en el modelo
             $_SESSION['mensaje_error'] = "No se puede eliminar el departamento porque tiene empleados asignados.";
        } else {
            $_SESSION['mensaje_error'] = "Error al eliminar el departamento. Verifique que no esté en uso.";
        }
        header("Location: crud.php?c=departamento&a=index");
        exit();
    }
}
?>