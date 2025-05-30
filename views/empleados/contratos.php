<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleado Contrato</title>
</head>
<body>

    <center>
    <h2><?php echo $data["titulo_c"]; ?></h2>			
    <br>
    <table border="1" class="tablecliente">
        <thead>
            <!-- Insertar puse recien -->
            <tr>
                <td colspan='7'><a href='crud.php?c=empleado&a=index'>Volver</a></td>
            </tr>
            <tr>
                <th>Codigo</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Salario Base</th>
                <th>Antiguedad</th>
                <th>Bono</th>
                <th>Duracion</th>
            </tr>
        </thead>					
        <tbody>
            <div>
            <?php 
                foreach($data["contrato"] as $dato) {
                    echo "<tr>";
                    echo "<td>".$dato["id_c"]."</td>";
                    echo "<td>".$dato["fecha_inicio"]."</td>";
                    echo "<td>".$dato["fecha_fin"]."</td>";
                    echo "<td>".$dato["salario_base"]."</td>";
                    echo "<td>".$dato["antiguedad"]."</td>";
                    echo "<td>".$dato["bono"]."</td>";
                    echo "<td>".$dato["duracion"]."</td>";
                    echo "</tr>";
                }
               
            ?>
            </div>
        </tbody>					
	</table>
    </center>
</body>
</html>