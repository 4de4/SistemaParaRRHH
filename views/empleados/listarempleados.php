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
        .btn a, .btn-actions a { /* Unificando estilo para enlaces de botón */
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            margin-right: 5px;
            display: inline-block; /* Para que el padding y margin funcionen bien */
            margin-bottom: 5px; /* Espacio si se van a la siguiente línea */
        }
        .btn-warning { background-color: #ffc107; color: black !important; }
        .btn-danger { background-color: #dc3545; }
        .btn-info { background-color: #17a2b8; } /* Para ver boleta */
        .btn-add-empleado { background-color: #28a745 !important; } /* Estilo específico para agregar */

        .mensaje { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .mensaje-exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .mensaje-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .comprobar-conexion { margin-top:30px; padding:10px; background-color:#eef; border:1px solid #ccf; text-align:center;}

    </style>
</head>
<body>
    <center>
        <h2><?php echo htmlspecialchars($data["titulo"]); ?></h2>
        <br>

        <?php
        // Mostrar mensajes flash de éxito o error
        if (isset($_SESSION['mensaje_exito'])) {
            echo '<div class="mensaje mensaje-exito">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</div>';
            unset($_SESSION['mensaje_exito']);
        }
        if (isset($_SESSION['mensaje_error'])) {
            echo '<div class="mensaje mensaje-error">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
            unset($_SESSION['mensaje_error']);
        }
        ?>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'jefe'): ?>
            <p><a href='crud.php?c=empleado&a=nuevo' class="btn-add-empleado btn-info">Agregar Nuevo Empleado</a></p>
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
                        <th>Departamento</th>
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
                        
                        $nombreFoto = basename($dato['foto'] ?? '');
                        $rutaFotoServidor = $_SERVER['DOCUMENT_ROOT'] . '/SistemaParaRRHH/views/fotos/' . $nombreFoto;
                        $urlFotoWeb = 'http://localhost/SistemaParaRRHH/views/fotos/' . $nombreFoto;

                        if (!empty($nombreFoto) && file_exists($rutaFotoServidor)) {
                            echo "<td><img class='img-empleado' src='" . htmlspecialchars($urlFotoWeb) . "' alt='Foto de " . htmlspecialchars($dato["nombre"]) . "'/></td>";
                        } else {
                            echo "<td>Sin foto</td>";
                        }
                        echo "<td>" . htmlspecialchars($dato["fecha_nacimiento"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["edad"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["fecha_inicio"]) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["fecha_fin"]) . "</td>";
                        echo "<td>" . htmlspecialchars(number_format($dato["salario_base"], 2)) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["antiguedad"]) . "</td>";
                        echo "<td>" . htmlspecialchars(number_format($dato["bono"], 2)) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["duracion"]) . "</td>";
                        // Estas columnas dependen de si ya ajustaste el JOIN con la tabla departamento normalizada
                        echo "<td>" . htmlspecialchars($dato["nombre_departamento"] ?? ($dato["nombre_d"] ?? 'N/A')) . "</td>";
                        echo "<td>" . htmlspecialchars($dato["ubicacion_departamento"] ?? ($dato["ubicacion"] ?? 'N/A')) . "</td>";

                        echo "<td class='btn-actions'>";
                        if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'jefe') {
                            echo "<a href='crud.php?c=empleado&a=modificar&id=" . $dato["id_e"] . "' class='btn-warning'>Modificar</a>";
                            echo "<a href='crud.php?c=empleado&a=eliminar&id=" . $dato["id_e"] . "' class='btn-danger' onclick='return confirm(\"¿Está seguro de eliminar este empleado?\");'>Eliminar</a>";
                        }
                        echo "<a href='crud.php?c=empleado&a=verBoleta&id_empleado=" . $dato["id_e"] . "' class='btn-info'>Ver Boleta</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay empleados registrados.</p>
        <?php endif; ?>

        <br><br>
        <?php
        // Esta sección es la que muestra el tipo de conexión actual (PDO/MySQLi).
        // Ya no necesita el formulario para cambiarlo desde aquí.
        // La variable $cafe venía del EmpleadoController, ahora la pasamos como $data["comprobar"]
        // desde EmpleadoController y ConfiguracionController.
        ?>
        <?php if (isset($data["comprobar"])): ?>
        <div class="comprobar-conexion">
            <p><?php echo htmlspecialchars($data["comprobar"]); ?></p>
        </div>
        <?php endif; ?>
    </center>
</body>
</html>