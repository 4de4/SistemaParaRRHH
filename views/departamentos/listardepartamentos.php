<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data["titulo"]); ?></title>
    <style>
        /* Puedes usar los mismos estilos que en listarempleados.php o unos específicos */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .table-custom { border-collapse: collapse; width: 90%; margin: 20px auto; background-color: #fff; }
        .table-custom th, .table-custom td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table-custom th { background-color: #007bff; color: white; }
        .table-custom tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-accion { text-decoration: none; padding: 6px 12px; border-radius: 4px; color: white; margin-right: 5px; font-size: 0.9em; }
        .btn-nuevo { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; }
        .btn-nuevo:hover { background-color: #218838; }
        .btn-warning { background-color: #ffc107; color: black !important; }
        .btn-danger { background-color: #dc3545; }
        .mensaje { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .mensaje-exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .mensaje-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .acciones-header {text-align: center !important;}
        .acciones-celda {text-align: center !important;}
         .comprobar-conexion { margin-top:30px; padding:10px; background-color:#eef; border:1px solid #ccf; text-align:center;}
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($data["titulo"]); ?></h2>

        <?php
        if (isset($_SESSION['mensaje_exito'])) {
            echo '<div class="mensaje mensaje-exito">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</div>';
            unset($_SESSION['mensaje_exito']);
        }
        if (isset($_SESSION['mensaje_error'])) {
            echo '<div class="mensaje mensaje-error">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
            unset($_SESSION['mensaje_error']);
        }
        ?>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'jefe'): // Solo el 'jefe' puede agregar ?>
            <div style="text-align: center; margin-bottom: 20px;">
                <a href='crud.php?c=departamento&a=nuevo' class="btn-nuevo">Agregar Nuevo Departamento</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($data["departamentos"])): ?>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'jefe'): ?>
                            <th class="acciones-header">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data["departamentos"] as $depto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($depto["id_d"]); ?></td>
                            <td><?php echo htmlspecialchars($depto["nombre"]); ?></td>
                            <td><?php echo htmlspecialchars($depto["ubicacion"]); ?></td>
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'jefe'): ?>
                                <td class="acciones-celda">
                                    <a href="crud.php?c=departamento&a=modificar&id=<?php echo $depto["id_d"]; ?>" class="btn-accion btn-warning">Modificar</a>
                                    <a href="crud.php?c=departamento&a=eliminar&id=<?php echo $depto["id_d"]; ?>" class="btn-accion btn-danger" onclick="return confirm('¿Está seguro de eliminar este departamento? Los empleados asignados quedarán sin departamento (o se impedirá la eliminación si está configurado así).');">Eliminar</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center;">No hay departamentos registrados.</p>
        <?php endif; ?>
        
        <?php if (isset($data["comprobar"])): ?>
            <div class="comprobar-conexion">
                <p><?php echo htmlspecialchars($data["comprobar"]); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>