<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data["titulo"]); ?></title>
    <style>
        /* Estilos similares al formulario de empleados_nuevo o personaliza */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { width: 60%; margin: auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-title { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"] {
            width: calc(100% - 22px); /* Ajuste para padding y borde */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-submit { padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .btn-submit:hover { background-color: #218838; }
        .btn-cancel { text-decoration: none; margin-left: 10px; padding: 10px 15px; background-color: #6c757d; color: white; border-radius: 4px; }
        .btn-cancel:hover { background-color: #5a6268; }
        .errores { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
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
        $inputUbicacion = $data['input']['ubicacion'] ?? '';
        ?>

        <form id="nuevoDepartamentoForm" method="POST" action="crud.php?c=departamento&a=guarda" autocomplete="off">
            <div class="form-group">
                <label for="nombre">Nombre del Departamento:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($inputNombre); ?>" required maxlength="60">
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($inputUbicacion); ?>" required maxlength="100">
            </div>

            <div class="form-group" style="text-align: center;">
                <button type="submit" name="guardar" class="btn-submit">Guardar Departamento</button>
                <a href="crud.php?c=departamento&a=index" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>