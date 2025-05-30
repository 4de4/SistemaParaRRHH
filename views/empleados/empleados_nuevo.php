<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data["titulo"]); ?></title>
    <style>
        .container { width: 80%; margin: auto; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); background-color: #fff; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="number"],
        .form-group input[type="file"],
        .form-group select { /* Añadido select a los estilos */
            width: 100%;
            padding: 10px; /* Aumentado padding */
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em; /* Tamaño de fuente consistente */
        }
        .btn-container { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .btn-container:hover { background-color: #218838; }
        .btn-cancel { text-decoration:none; margin-left:10px; padding:10px 15px; background-color:#6c757d; color:white; border-radius:4px; font-size:1em;}
        .btn-cancel:hover { background-color:#5a6268;}
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
        $inputNombre = $data['input']['nombre'] ?? '';
        $inputApellido = $data['input']['apellido'] ?? '';
        $inputFechaNac = $data['input']['fecha_nacimiento'] ?? '';
        $selectedDepartamento = $data['input']['id_departamento'] ?? ''; // Para repoblar el select
        $inputFechaInicio = $data['input']['fecha_inicio'] ?? '';
        $inputFechaFin = $data['input']['fecha_fin'] ?? '';
        $inputSalario = $data['input']['salario_base'] ?? '';
        // Los campos inputNombreD e inputUbicacion ya no son necesarios aquí
        ?>

        <form id="nuevoEmpleadoForm" name="nuevo" method="POST" action="crud.php?c=empleado&a=guarda" autocomplete="off" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($inputNombre); ?>" required/>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($inputApellido); ?>" required/>
            </div>
            <div class="form-group">
                <label for="foto">Foto (JPG/PNG/GIF, máx 2MB):</label>
                <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.gif" <?php echo empty($_POST['id_e']) ? 'required' : ''; ?>/>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($inputFechaNac); ?>" required/>
            </div>

            <!-- NUEVO CAMPO: SELECT PARA DEPARTAMENTO -->
            <div class="form-group">
                <label for="id_departamento">Departamento:</label>
                <select id="id_departamento" name="id_departamento" required>
                    <option value="">-- Seleccione un Departamento --</option>
                    <?php if (!empty($data['departamentos'])): ?>
                        <?php foreach ($data['departamentos'] as $depto): ?>
                            <option value="<?php echo htmlspecialchars($depto['id_d']); ?>" <?php echo ($selectedDepartamento == $depto['id_d']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($depto['nombre']); ?> (<?php echo htmlspecialchars($depto['ubicacion']); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No hay departamentos disponibles. Por favor, cree uno primero.</option>
                    <?php endif; ?>
                </select>
            </div>
            <!-- FIN NUEVO CAMPO -->

            <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio Contrato:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($inputFechaInicio); ?>" required/>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha Fin Contrato:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($inputFechaFin); ?>" required/>
            </div>
            <div class="form-group">
                <label for="salario_base">Salario Base:</label>
                <input type="number" step="0.01" id="salario_base" name="salario_base" value="<?php echo htmlspecialchars($inputSalario); ?>" required/>
            </div>

            <?php /* Eliminados los campos para nombre_d y ubicacion, ya que se seleccionan del <select> */ ?>

            <div class="form-group" style="text-align: center; margin-top: 20px;">
                <button id="guardar" name="guardar" type="submit" class="btn-container">Guardar Empleado</button>
                <a href="crud.php?c=empleado&a=index" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>