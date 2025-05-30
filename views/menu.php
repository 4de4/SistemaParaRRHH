<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/fico.ico" type="imge/x-icon">
    <title>Menu RRHH</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>
    <?php
    // Es una buena práctica asegurarse que la sesión esté iniciada
    // if (session_status() == PHP_SESSION_NONE) {
    //     session_start(); // Descomenta si no estás seguro de que ya está iniciada
    // }
    $rol = $_SESSION['rol'] ?? 'default'; // Usar el operador de fusión de null para evitar warnings
    ?>
    <header>
    <div class="menu">
      <nav>
          <ul>
            <?php if ($rol=='empleado') : ?>
                <li><a href="crud.php?c=empleado&a=index">Empleados</a></li>
                <li><a href="crud.php?c=departamento&a=index">Departamentos</a></li>
            <?php elseif ($rol=='jefe') : ?>
                <li><a href="crud.php?c=usuario&a=mostrarFormularioRoles">Asignar rol</a></li>
                <li><a href="crud.php?c=empleado&a=index">Empleados</a></li>
                <li><a href="crud.php?c=departamento&a=index">Departamentos</a></li>
                <li><a href="crud.php?c=configuracion&a=index">Configuración DB</a></li>
            <?php endif; ?>
          </ul>
      </nav>
    </div>
  </header>
</body>
</html>


                      <!-- El empleado común NO debería acceder a la configuración del driver de BD.
                       Si "Configuracion" para el empleado es para su perfil, necesitará otra ruta y controlador.
                       Por ahora, se comenta o elimina este enlace para el rol 'empleado' si se refiere a la config de BD.
                       Si el enlace original `crud.php?c=configuracion&a=index` ya estaba protegido por
                       `verificarAcceso(['jefe'])` en el ConfiguracionController, entonces el empleado
                       vería un error de acceso, lo cual también es una forma de manejarlo.
                       Pero es más limpio no mostrarle la opción si no tiene permiso. -->