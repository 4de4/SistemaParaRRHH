<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data["titulo"]); ?></title>
    <style>
        .container { width: 80%; margin: auto; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="number"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-container { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-container:hover { background-color: #0056b3; }
        .errores { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .errores ul { margin: 0; padding-left: 20px; }
        .foto-actual-preview img { max-width: 150px; max-height: 150px; border: 1px solid #ccc; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="form-title"><?php echo htmlspecialchars($data["titulo"]); ?></h2>

        <?php if (!empty($data["errores"])): ?>
            <div class="errores">
                <strong>Por favor, corrija los siguientes errores:</strong>
                <ul>
                    <?php foreach ($data["errores"] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
        // Datos del empleado a modificar, o datos de re-población si hubo error
        $empleado = $data['empleado_data'] ?? [];
        $id_e = $empleado['id_e'] ?? '';
        $nombre = $empleado['nombre'] ?? '';
        $apellido = $empleado['apellido'] ?? '';
        $foto_actual = $empleado['foto'] ?? null; // Nombre del archivo de la foto actual
        $fecha_nacimiento = $empleado['fecha_nacimiento'] ?? '';
        $fecha_inicio = $empleado['fecha_inicio'] ?? ''; // De la tabla contrato
        $fecha_fin = $empleado['fecha_fin'] ?? '';       // De la tabla contrato
        $salario_base = $empleado['salario_base'] ?? ''; // De la tabla contrato
        // Nombre y ubicación del departamento, asumiendo que vienen del JOIN y tu lógica actual
        $nombre_d = $empleado['nombre_d'] ?? ($empleado['nombre'] ?? ''); // Campo 'nombre' de la tabla departamento
        $ubicacion = $empleado['ubicacion'] ?? ''; // Campo 'ubicacion' de la tabla departamento
        ?>

        <form id="modificarEmpleadoForm" name="modificar" method="POST" action="crud.php?c=empleado&a=actualizar" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="id_e" value="<?php echo htmlspecialchars($id_e); ?>">
            <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($foto_actual ?? ''); ?>">

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required/>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required/>
            </div>

            <div class="form-group">
                <label for="foto">Foto (JPG/PNG, máx 2MB) - Dejar vacío para no cambiar:</label>
                <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.jpeg,.png,.gif"/>
                <?php if ($foto_actual): ?>
                    <div class="foto-actual-preview">
                        <p>Foto actual:</p>
                        <img src="<?php echo 'http://localhost/SistemaParaRRHH/views/fotos/' . basename(htmlspecialchars($foto_actual)); ?>" alt="Foto actual">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($fecha_nacimiento); ?>" required/>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio Contrato:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required/>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha Fin Contrato:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" required/>
            </div>
            <div class="form-group">
                <label for="salario_base">Salario Base:</label>
                <input type="number" step="0.01" class="form-control" id="salario_base" name="salario_base" value="<?php echo htmlspecialchars($salario_base); ?>" required/>
            </div>
            <div class="form-group">
                <label for="nombre_d">Nombre del Departamento:</label>
                <input type="text" class="form-control" id="nombre_d" name="nombre_d" value="<?php echo htmlspecialchars($nombre_d); ?>" required/>
            </div>
            <div class="form-group">
                <label for="ubicacion">Ubicación del Departamento:</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($ubicacion); ?>" required/>
            </div>

            <div class="form-group">
                <button id="actualizar" name="actualizar" type="submit" class="btn-container">Actualizar Empleado</button>
                <a href="crud.php?c=empleado&a=index" style="margin-left: 10px;">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>