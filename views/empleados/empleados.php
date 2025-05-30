<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>empleados..php</title>
</head>
<body>
    <center>
    <h2><?php echo $data["titulo"]; ?></h2>			
    <br>
	<table border="1" class="tablecliente">
        <thead>
            <!-- Insertar puse recien -->
            <tr>
                <td colspan='13'><a href='crud.php?c=empleado&a=nuevo'>Agregar</a></td>
            </tr>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Foto</th>
                <th>Fecha de Nacimiento</th>
                <th>Edad</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Salario</th>
                <th>Antiguedad</th>
                <th>Bono</th>
                <th>Duracion</th>
                <th>Departamento</th>
                <th>Direccion</th>
            </tr>
        </thead>					
        <tbody>
            <div>
            <?php 
                foreach($data["empleado"] as $dato) {
                    echo "<tr>";
                    echo "<td>".$dato["nombre"]."</td>";
                    echo "<td>".$dato["apellido"]."</td>";
                    echo "<td><img class='img' height='100px' src='http://localhost/SistemaParaRRHH/views/fotos/".basename($dato['foto'])."'/></td>";
                    echo "<td>".$dato["fecha_nacimiento"]."</td>";
                    echo "<td>".$dato["edad"]."</td>";
                    echo "<td>".$dato["fecha_inicio"]."</td>";
                    echo "<td>".$dato["fecha_fin"]."</td>";
                    echo "<td>".$dato["salario_base"]."</td>";
                    echo "<td>".$dato["antiguedad"]."</td>";
                    echo "<td>".$dato["bono"]."</td>";
                    echo "<td>".$dato["duracion"]."</td>";
                    echo "<td>".$dato["nombre_d"]."</td>";
                    echo "<td>".$dato["ubicacion"]."</td>";
                    echo "<td class='btn'><a href='crud.php?c=empleado&a=modificar&id=".$dato["id_e"]."' class='btn btn-warning'>Modificar</a></td>";
	                echo "<td class='btn'><a href='crud.php?c=empleado&a=eliminar&id=".$dato["id_e"]."' class='btn btn-danger'>Eliminar</a></td>";
                    echo "</tr>";
                }
               
            ?>
            </div>
        </tbody>					
	</table>
    <br><br><br><br>
    <div>
            <h3><?php echo $cafe["comprobar"]?></h3>
            <form method="post" action="../config/configuracion.php">
                <label>Elige tipo de conexión(ES para ver si funciona el cambio de conexion):</label><br>
                <input type="radio" name="conexion" value="PDO"> PDO<br>
                <input type="radio" name="conexion" value="mysqli"> MySQLi<br>
                <input type="submit" value="Guardar configuración">
            </form>
        </div>
    </center>
</body>
</html>