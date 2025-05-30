<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data["titulo"]); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { width: 70%; margin: auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .table-roles { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        .table-roles th, .table-roles td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table-roles th { background-color: #007bff; color: white; }
        .table-roles select { padding: 8px; border-radius: 4px; border: 1px solid #ccc; min-width: 120px; }
        .btn-actualizar-rol { padding: 8px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-actualizar-rol:hover { background-color: #218838; }
        .mensaje { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .mensaje-exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .mensaje-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($data["titulo"]); ?></h2>

        <?php
        if (isset($_SESSION['mensaje_exito_rol'])) {
            echo '<div class="mensaje mensaje-exito">' . htmlspecialchars($_SESSION['mensaje_exito_rol']) . '</div>';
            unset($_SESSION['mensaje_exito_rol']);
        }
        if (isset($_SESSION['mensaje_error_rol'])) {
            echo '<div class="mensaje mensaje-error">' . htmlspecialchars($_SESSION['mensaje_error_rol']) . '</div>';
            unset($_SESSION['mensaje_error_rol']);
        }
        ?>

        <?php if (!empty($data["usuarios"])): ?>
            <table class="table-roles">
                <thead>
                    <tr>
                        <th>ID Usuario</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol Actual</th>
                        <th>Nuevo Rol</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data["usuarios"] as $usuario): ?>
                        <tr>
                            <form method="POST" action="crud.php?c=usuario&a=actualizarRol">
                                <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario['id_u']); ?>">
                                <td><?php echo htmlspecialchars($usuario['id_u']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($usuario['rol'])); // ucfirst para capitalizar ?></td>
                                <td>
                                    <select name="nuevo_rol">
                                        <?php foreach ($data['roles_disponibles'] as $rol_opcion): ?>
                                            <option value="<?php echo htmlspecialchars($rol_opcion); ?>" <?php echo ($usuario['rol'] == $rol_opcion) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars(ucfirst($rol_opcion)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" class="btn-actualizar-rol">Actualizar Rol</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay otros usuarios para asignar roles o ha ocurrido un error al cargarlos.</p>
        <?php endif; ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="crud.php?c=menu&a=index">Volver al Menú Principal</a>
        </div>
    </div>
</body>
</html>