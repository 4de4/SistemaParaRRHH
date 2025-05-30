<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados nuevo</title>
</head>
<body>
    <div class="container">
		<h2 class="form-title"><?php echo $data["titulo"]; ?></h2>
            <!--El atributo autocomplete="off" en HTML se usa en formularios o campos de entrada (<input>) 
            para desactivar la función de autocompletado del navegador. Cuando se establece este atributo 
            en un campo de formulario, el navegador no sugiere valores almacenados previamente para ese 
            campo específico.   -->
        <form id="nuevo" name="nuevo" method="POST" action="crud.php?c=empleado&a=guarda" autocomplete="off" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required/>
            </div>
                        
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required/>
            </div>
                        
            <div class="form-group">
                <label for="modelo">Foto:</label>
                <input type="file" class="form-control" id="foto" name="foto" required/>
            </div>
                    
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required/>
            </div>
                        
            <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required/>
            </div>

            <div class="form-group">
                <label for="feceha_fin">Fecha Fin:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required/>
            </div>

            <div class="form-group">
                <label for="salario_base">Salario:</label>
                <input type="number" class="form-control" id="salario_base" name="salario_base" required/>
            </div>

            <div class="form-group">
                <label for="nombre_d">Departamento:</label>
                <input type="text" class="form-control" id="nombre_d" name="nombre_d" required/>
            </div>
            <div class="form-group">
                <label for="ubicacion">Direccion:</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" required/>
            </div>
            <div class="form-container">
            <button id="guardar" name="guardar" type="submit" class="btn-container">Guardar</button>
				
        </form>
    </div>
</body>
</html>