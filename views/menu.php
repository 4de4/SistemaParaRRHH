<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/fico.ico" type="image/x-icon">
    <title><?php echo htmlspecialchars($data['titulo_pagina'] ?? 'Panel RRHH'); ?></title>
    <link rel="stylesheet" href="menu.css"> <!-- Este CSS lo vamos a reescribir -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    $nombreUsuario = $data['nombre_usuario'] ?? 'Usuario';
    $rolUsuario = $data['rol'] ?? 'default';
    ?>

    <div class="main-menu-container"> <?php // Cambié la clase para evitar conflictos si 'main-container-principal' se usa en otro lado ?>
        <h1>RECURSOS HUMANOS</h1>
        <p class="welcome-menu">
            <i class="fas fa-user-circle"></i> ¡Bienvenido/a, <span class="username-menu"><?php echo htmlspecialchars(ucfirst($nombreUsuario)); ?></span>!
        </p>
        <p class="info-menu">Seleccione una opción para continuar:</p>

        <div class="action-buttons-menu">
            <?php if ($rolUsuario == 'jefe'): ?>
                <a href="crud.php?c=usuario&a=mostrarFormularioRoles" class="menu-button"><i class="fas fa-user-shield"></i> Asignar Roles</a>
                <a href="crud.php?c=empleado&a=index" class="menu-button"><i class="fas fa-users"></i> Gestionar Empleados</a>
                <a href="crud.php?c=departamento&a=index" class="menu-button"><i class="fas fa-building"></i> Gestionar Departamentos</a>
                <a href="crud.php?c=configuracion&a=index" class="menu-button"><i class="fas fa-cogs"></i> Configuración DB</a>
            <?php elseif ($rolUsuario == 'empleado'): ?>
                <a href="crud.php?c=empleado&a=index" class="menu-button"><i class="fas fa-id-card"></i> Ver Mis Datos / Boletas</a>
                <a href="crud.php?c=departamento&a=index" class="menu-button"><i class="fas fa-info-circle"></i> Ver Departamentos</a>
            <?php endif; ?>
            <a href="index.php?logout=true" class="menu-button logout-menu-button"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

    <?php /* Footer eliminado */ ?>
</body>
</html>