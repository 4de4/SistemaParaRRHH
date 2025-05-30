<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data["titulo"]); ?></title>
    <style>
        /* Estilos básicos o puedes usar los de otros formularios */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { width: 50%; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input[type="radio"] { margin-right: 8px; }
        .btn-submit { display: block; width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; text-align: center; }
        .btn-submit:hover { background-color: #0056b3; }
        .mensaje { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .mensaje-exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .mensaje-info { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .current-driver { font-weight: bold; color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($data["titulo"]); ?></h2>

        <?php
        if (isset($_SESSION['mensaje_config'])) {
            echo '<div class="mensaje mensaje-exito">' . htmlspecialchars($_SESSION['mensaje_config']) . '</div>';
            unset($_SESSION['mensaje_config']);
        }
        ?>

        <p style="text-align: center; margin-bottom: 20px;">
            Driver de conexión actual:
            <span class="current-driver">
                <?php echo isset($_SESSION['db_driver']) ? strtoupper($_SESSION['db_driver']) : 'PDO (Por defecto)'; ?>
            </span>
        </p>

        <form method="post" action="crud.php?c=configuracion&a=guardarConexion">
            <div class="form-group">
                <label>Seleccione el tipo de conexión a la base de datos:</label>
                <div>
                    <input type="radio" id="pdo" name="conexion_driver" value="PDO" <?php echo (isset($_SESSION['db_driver']) && $_SESSION['db_driver'] == 'PDO') ? 'checked' : (!isset($_SESSION['db_driver']) ? 'checked' : ''); ?>>
                    <label for="pdo">PDO</label>
                </div>
                <div>
                    <input type="radio" id="mysqli" name="conexion_driver" value="mysqli" <?php echo (isset($_SESSION['db_driver']) && $_SESSION['db_driver'] == 'mysqli') ? 'checked' : ''; ?>>
                    <label for="mysqli">MySQLi</label>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit">Guardar Configuración</button>
            </div>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <a href="crud.php?c=menu&a=index">Volver al Menú Principal</a>
        </div>
    </div>
</body>
</html>