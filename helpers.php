<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function verificarAcceso($rolesPermitidos = array()) {
    // 1. Verificar si hay sesión iniciada
    if (!isset($_SESSION['usuario_id'])) {
        // Redirigir al login si no hay sesión y se esperaba una.
        // Importante: Asegúrate que index.php no incluya crud.php de una manera que cree un bucle.
        // La redirección debe ser a la página de login real, no a crud.php sin parámetros.
        header("Location: ../index.php?error=Acceso denegado. Inicie sesión."); // Asumiendo que index.php está un nivel arriba de views/
        exit();
    }

    // 2. Verificar rol (si se especificaron roles)
    if (!empty($rolesPermitidos) && isset($_SESSION['rol'])) {
        if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
            // Rol no permitido
            // Redirigir a una página de menú o dashboard con un mensaje de error.
            // Evita redirigir a crud.php sin c y a, podría causar bucles si esa es la página por defecto.
            header("Location: crud.php?c=Menu&a=index&error_permiso=No tiene permisos para esta acción.");
            exit();
        }
    } elseif (!empty($rolesPermitidos) && !isset($_SESSION['rol'])) {
        // Error: se esperan roles pero el usuario no tiene rol en sesión
        header("Location: ../index.php?error=Error de sesión. Rol no definido.");
        exit();
    }
    // Si $rolesPermitidos está vacío, solo se valida que haya sesión iniciada.
}
?>