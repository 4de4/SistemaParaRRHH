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
    
    $rol = $_SESSION['rol'];
    ?>
    <header>
    <div class="menu">
      <nav>
          <ul>
            <?php if ($rol=='empleado') : ?>
            <li><a href="crud.php?c=empleado&a=index">Empleados</a></li>
            <li><a href="crud.php?c=departamento&a=index">Departamentos</a></li>
            <li><a href="crud.php?c=configuracion&a=index">Configuracion</a></li>
            <?php elseif ($rol=='jefe') : ?>
            <li><a href="crud.php?c=menu&a=asignar">Asignar rol</a></li>
            <li><a href="crud.php?c=empleado&a=index">Empleados</a></li>
            <li><a href="crud.php?c=departamento&a=index">Departamentos</a></li>
            <li><a href="crud.php?c=menu&a=config">Configuracion</a></li>
            <?php endif; ?>
          </ul>
      </nav>
    </div>
  </header>
</body>
</html>