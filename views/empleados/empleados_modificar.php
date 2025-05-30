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
        .form-group select { /* Añadido select */
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .btn-container { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .btn-container:hover { background-color: #0056b3; }
        .btn-cancel { text-decoration:none; margin-left:10px; padding:10px 15px; background-color:#6c757d; color:white; border-radius:4px; font-size:1em;}
        .btn-cancel:hover { background-color:#5a6268;}
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
        $empleado = $data['empleado_data'] ?? [];
        $id_e = $empleado['id_e'] ?? '';
        $nombre = $empleado['nombre'] ?? '';
        $apellido = $empleado['apellido'] ?? '';
        $foto_actual = $empleado['foto'] ?? null;
        $fecha_nacimiento = $empleado['fecha_nacimiento'] ?? '';
        // Obtener el id_departamento actual del empleado para preseleccionar.
        // El EmpleadoModel->get_empleado_por_id() ya debería traer e.id_departamento
        $idDepartamentoActual = $empleado['id_departamento'] ?? ($data['input']['id_departamento'] ?? '');

        $fecha_inicio = $empleado['fecha_inicio'] ?? '';
        $fecha_fin = $empleado['fecha_fin'] ?? '';
        $salario_base = $empleado['salario_base'] ?? '';
        // Los campos nombre_d y ubicacion ya no se usan directamente del empleado para input, sino del select
        ?>

        <form id="modificarEmpleadoForm" name="modificar" method="POST" action="crud.php?c=empleado&a=actualizar" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="id_e" value="<?php echo htmlspecialchars($id_e); ?>">
            <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($foto_actual ?? ''); ?>">

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required/>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required/>
            </div>
            <div class="form-group">
                <label for="foto">Foto (JPG/PNG/GIF, máx 2MB) - Dejar vacío para no cambiar:</label>
                <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.gif"/>
                <?php if ($foto_actual): ?>
                    <div class="foto-actual-preview">
                        <p>Foto actual:</p>
                        <img src="<?php echo 'http://localhost/SistemaParaRRHH/views/fotos/' . basename(htmlspecialchars($foto_actual)); ?>" alt="Foto actual">
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($fecha_nacimiento); ?>" required/>
            </div>

            <!-- CAMPO SELECT PARA DEPARTAMENTO -->
            <div class="form-group">
                <label for="id_departamento">Departamento:</label>
                <select id="id_departamento" name="id_departamento" required>
                    <option value="">-- Seleccione un Departamento --</option>
                    <?php if (!empty($data['departamentos'])): ?>
                        <?php foreach ($data['departamentos'] as $depto): ?>
                            <option value="<?php echo htmlspecialchars($depto['id_d']); ?>" <?php echo ($idDepartamentoActual == $depto['id_d']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($depto['nombre']); ?> (<?php echo htmlspecialchars($depto['ubicacion']); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <option value="" disabled>No hay departamentos disponibles. Por favor, cree uno primero.</option>
                    <?php endif; ?>
                </select>
            </div>
            <!-- FIN CAMPO SELECT -->

            <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio Contrato:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required/>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha Fin Contrato:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" required/>
            </div>
            <div class="form-group">
                <label for="salario_base">Salario Base:</label>
                <input type="number" step="0.01" id="salario_base" name="salario_base" value="<?php echo htmlspecialchars($salario_base); ?>" required/>
            </div>

            <?php /* Eliminados los campos para nombre_d y ubicacion */ ?>

            <div class="form-group" style="text-align: center; margin-top: 20px;">
                <button id="actualizar" name="actualizar" type="submit" class="btn-container">Actualizar Empleado</button>
                <a href="crud.php?c=empleado&a=index" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>