<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
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
                    echo "<td class='btn'><a href='crud.php?c=empleado&a=modificar&id=".$dato["id_e"]."' class='btn btn-warning'>Modificar</a></td>";
	                echo "<td class='btn'><a href='crud.php?c=empleado&a=eliminar&id=".$dato["id_e"]."' class='btn btn-danger'>Eliminar</a></td>";
	                echo "<td class='btn'><a href='crud.php?c=empleado&a=eliminar&id=".$dato["id_e"]."' class='btn btn-danger'>Boleta</a></td>";
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