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
	<br>
    
	<table border="1" class="tablecliente">
        <thead>
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
                    echo "<td>".$dato["foto"]."</td>";
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
                    echo "</tr>";
                }
               
            ?>
            </div>
        </tbody>					
	</table>
    
    </center>
</body>
</html>