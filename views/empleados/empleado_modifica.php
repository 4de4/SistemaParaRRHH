<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Datos del Medicamento</title>
</head>
<body>
    <h1>Formulario de Actualización de Datos</h1>
    <form action="../crud.php?c=empleado&a=guardarAct" method="post">
        <table border="1">
            <thead>
                <tr>
                    <th colspan="2">Empleado <?php echo $dato['titulo'];?></th>

                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td><strong>Codigo:</strong></td>
                    <td><input disabled readonly type="text" name="id" value="<?php echo $dato["id"] ;?>"></td>
                </tr>
                <tr>
                    <td><strong>Nombre:</strong></td>
                    <td><input type="text" name="nombre" value="<?php echo $data[0]["nombre"] ;?>" require placeholder="Ingrese el nombre del medicamento ..."></td>
                </tr>
                <tr>
                    <td><strong>Apellido:</strong></td>
                    <td><input type="text" name="descripcion" value="<?php echo $data[0]['apellido'] ;?>" require placeholder="Ingrese el nombre del medicamento ..."></td>
                </tr>
                <tr>
                    <td><strong>Fecha de Nacimiento:</strong></td>
                    <td><input type="date" step="any" name="precio" value="<?php echo $data[0]['fecha_nacimiento'] ;?>"></td>
                </tr>
                <tr>
                    <td><strong>Foto:</strong></td>
                    <td><input type="file" name="stock" value="<?php echo $data[0]['foto'] ;?>"></td>
                </tr>
                <tr>
                    <td><strong>Salario:</strong></td>
                    <td><input type="number" name="stock" value="<?php echo $data[0]['salario_base'] ;?>"></td>
                </tr>
                <tr>
                    <td><strong>Fecha de Contrato:</strong></td>
                    <td><input type="date" name="stock" value="<?php echo $data[0]['fecha_inicio'] ;?>"></td>
                </tr>
                <tr>
                    <td><strong>Fin del Contrato:</strong></td>
                    <td><input type="date" name="stock" value="<?php echo $data[0]['fecha_fin'] ;?>"></td>
                </tr>
                <tr>
                    <td><strong>Departamento:</strong></td>
                    <td><input type="text" name="stock" value="<?php echo $data[0]['nombre_d'] ;?>"></td>
                </tr>
                <tr>
                    <td><strong>Direccion:</strong></td>
                    <td><input type="text" name="stock" value="<?php echo $data[0]['ubicacion'] ;?>"></td>
                </tr>
                <tr>
                    <td><input type="submit"  value="Actualizar"></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>