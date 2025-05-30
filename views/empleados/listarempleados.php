<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data["titulo"]); ?></title>
    <style>
        /* Estilos básicos para la tabla y la imagen */
        .tablecliente {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            background-color: #f9f9f9;
        }
        .tablecliente th, .tablecliente td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .tablecliente th {
            background-color: #e7e7e7;
        }
        .img-empleado {
            max-height: 100px;
            max-width: 100px;
            object-fit: cover;
        }
        .btn a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            margin-right: 5px;
        }
        .btn-warning { background-color: #ffc107; color: black !important; }
        .btn-danger { background-color: #dc3545; }
        .btn-info { background-color: #17a2b8; } /* Para ver boleta */
    </style>
</head>
<body>
    <center>
        <h2><?php echo htmlspecialchars($data["titulo"]); ?></h2>
        <br>
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'jefe'): ?>
            <p><a href='crud.php?c=empleado&a=nuevo' class="btn btn-info" style="background-color: #28a745;">Agregar Nuevo Empleado</a></p>
        <?php endif; ?>

        <?php if (!empty($data["empleado"])): ?>
            <table border="1" class="tablecliente">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Foto</th>
                        <th>Fecha Nac.</th>
                        <th>Edad</th>
                        <th>Fecha Inicio Cont.</th>
                        <th>Fecha Fin Cont.</th>
                        <th>Salario Base</th>
                        <th>Antigüedad (años)</th>
                        <th>Bono</th>
                        <th>Duración Cont. (meses)</th>
                        <th>Departamento (Nombre)</th>
                        <th>Ubicación Dept.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data["empleado"] as $dato) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($dato["nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["apellido"]) . "</td>";
                        // Asegúrate que la ruta de la foto sea correcta y accesible
                        // y que $dato['foto'] contenga solo el nombre del archivo o una ruta relativa desde 'fotos/'
                        $rutaFoto = "../views/fotos/" . basename($dato['foto']);
                        if (!empty($dato['foto']) && file_exists($rutaFoto)) {
                             // La ruta en src debe ser accesible desde el navegador, no desde el sistema de archivos del servidor directamente si no es pública.
                             // Asumiendo que /SistemaParaRRHH/views/fotos/ es accesible vía web.
                            echo "<td><img class='img-empleado' src='http://localhost/SistemaParaRRHH/views/fotos/" . basename(htmlspecialchars($dato['foto'])) . "' alt='Foto de " . htmlspecialchars($dato["nombre"]) . "'/></td>";
                        } else {
                            echo "<td>Sin foto</td>";
                        }
                        echo "<td>" . htmlspecialchars($dato["fecha_nacimiento"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["edad"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["fecha_inicio"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["fecha_fin"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["salario_base"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["antiguedad"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["bono"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["duracion"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["nombre_d"]) . "</td>"; // Asumiendo que d.nombre_d existe
                        echo "<td>" . htmlspecialchars($dato["ubicacion"]) . "</td>"; // Asumiendo que d.ubicacion existe

                        echo "<td class='btn-actions'>";
                        if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'jefe') {
                            echo "<a href='crud.php?c=empleado&a=modificar&id=" . $dato["id_e"] . "' class='btn btn-warning'>Modificar</a>";
                            echo "<a href='crud.php?c=empleado&a=eliminar&id=" . $dato["id_e"] . "' class='btn btn-danger' onclick='return confirm(\"¿Está seguro de eliminar este empleado?\");'>Eliminar</a>";
                        }
                        // Siempre mostrar "Ver Boleta" o según tu lógica de roles para esta acción
                        echo "<a href='crud.php?c=empleado&a=verBoleta&id_empleado=" . $dato["id_e"] . "' class='btn btn-info'>Ver Boleta</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay empleados registrados.</p>
        <?php endif; ?>

        <br><br><br><br>
        <?php if (isset($cafe) && isset($cafe["comprobar"])): // Comprobar si $cafe está definido ?>
        <div>
            <h3><?php echo htmlspecialchars($cafe["comprobar"]); ?></h3>
            <form method="post" action="../config/configuracion.php">
                <label>Elige tipo de conexión:</label><br>
                <input type="radio" name="conexion" value="PDO" <?php echo (isset($_SESSION['db_driver']) && $_SESSION['db_driver'] == 'PDO') ? 'checked' : ''; ?>> PDO<br>
                <input type="radio" name="conexion" value="mysqli" <?php echo (isset($_SESSION['db_driver']) && $_SESSION['db_driver'] == 'mysqli') ? 'checked' : ''; ?>> MySQLi<br>
                <input type="submit" value="Guardar configuración">
            </form>
        </div>
        <?php endif; ?>
    </center>
</body>
</html>