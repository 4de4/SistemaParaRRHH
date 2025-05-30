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
        .btn-container { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-container:hover { background-color: #218838; }
        .errores { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .errores ul { margin: 0; padding-left: 20px; }
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
        // Para repoblar el formulario
        $inputNombre = $data['input']['nombre'] ?? '';
        $inputApellido = $data['input']['apellido'] ?? '';
        $inputFechaNac = $data['input']['fecha_nacimiento'] ?? '';
        $inputFechaInicio = $data['input']['fecha_inicio'] ?? '';
        $inputFechaFin = $data['input']['fecha_fin'] ?? '';
        $inputSalario = $data['input']['salario_base'] ?? '';
        $inputNombreD = $data['input']['nombre_d'] ?? ''; // Nombre departamento
        $inputUbicacion = $data['input']['ubicacion'] ?? ''; // Ubicación departamento
        ?>

        <form id="nuevoEmpleadoForm" name="nuevo" method="POST" action="crud.php?c=empleado&a=guarda" autocomplete="off" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($inputNombre); ?>" required/>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($inputApellido); ?>" required/>
            </div>
            <div class="form-group">
                <label for="foto">Foto (JPG/PNG, máx 2MB):</label>
                <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.jpeg,.png,.gif" required/>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($inputFechaNac); ?>" required/>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio Contrato:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($inputFechaInicio); ?>" required/>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha Fin Contrato:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($inputFechaFin); ?>" required/>
            </div>
            <div class="form-group">
                <label for="salario_base">Salario Base:</label>
                <input type="number" step="0.01" class="form-control" id="salario_base" name="salario_base" value="<?php echo htmlspecialchars($inputSalario); ?>" required/>
            </div>
            <div class="form-group">
                <label for="nombre_d">Nombre del Departamento:</label>
                <input type="text" class="form-control" id="nombre_d" name="nombre_d" value="<?php echo htmlspecialchars($inputNombreD); ?>" required/>
            </div>
            <div class="form-group">
                <label for="ubicacion">Ubicación del Departamento:</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($inputUbicacion); ?>" required/>
            </div>
            <div class="form-group">
                <button id="guardar" name="guardar" type="submit" class="btn-container">Guardar Empleado</button>
                <a href="crud.php?c=empleado&a=index" style="margin-left: 10px;">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>