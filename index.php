<?php
// Siempre iniciar sesión al principio de cualquier script que la use
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si se está solicitando cerrar sesión
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    // 1. Limpiar todas las variables de sesión.
    $_SESSION = array();

    // 2. Si se desea destruir la sesión completamente, borrar también la cookie de sesión.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // 3. Finalmente, destruir la sesión.
    session_destroy();

    // 4. Redirigir a la página de login (index.php) con un mensaje opcional
    header("Location: index.php?mensaje_logout=Has cerrado sesión correctamente.");
    exit(); // Asegurarse de que el script se detiene después de la redirección
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php /* Bootstrap no parece usarse activamente en este formulario, pero se deja por si acaso */ ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="./views/index.css"> <?php // Ruta a tu CSS para el login ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <?php // Font Awesome 6 ?>
    <title>Iniciar Sesion</title>
</head>
<body>
    <div class="form">
        <form action="views/crud.php?c=usuario&a=iniciar" method="POST">
            <h1>Iniciar Sesion</h1>

            <div class="mensaje">
                <?php
                    // Mostrar mensaje de error del login
                    if(isset($_GET['error'])){
                        echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
                    }
                    // Mostrar mensaje de éxito (ej. después de crear cuenta o cerrar sesión)
                    if(isset($_GET['success'])){ // Usado por registrarse.php
                        echo '<p class="success" style="color: lightgreen; border-color: lightgreen;">' . htmlspecialchars($_GET['success']) . '</p>';
                    }
                    if(isset($_GET['mensaje_logout'])){ // Usado por el cierre de sesión
                        echo '<p class="success" style="color: lightgreen; border-color: lightgreen;">' . htmlspecialchars($_GET['mensaje_logout']) . '</p>';
                    }
                ?>
                <?php
                    // Limpiar los parámetros GET de la URL después de mostrarlos
                    if (isset($_GET['error']) || isset($_GET['success']) || isset($_GET['mensaje_logout'])) {
                        $url = strtok($_SERVER["REQUEST_URI"], '?');
                        echo "<script>history.replaceState({}, document.title, '$url');</script>";
                    }
                ?>
            </div>

            <div class="text">
                <i class="fa-solid fa-user"></i>
                <label for="Username">Usuario:</label> <?php // Añadido for para el label ?>
                <input type="text" id="Username" placeholder="Ingrese su usuario..." name="Username" required> <?php // Añadido required ?>
            </div>
            <div class="text">
                <i class="fa-solid fa-unlock"></i>
                <label for="Password">Contraseña:</label> <?php // Añadido for para el label ?>
                <input type="password" id="Password" placeholder="Ingrese su contraseña..." name="Password" required> <?php // Añadido required ?>
            </div>

            <button type="submit" class="btn">Iniciar Sesión</button>

            <div class="link">
                <p>No tiene una cuenta? <a href="./views/registrarse.php">Registrarse</a></p>
            </div>
        </form>
    </div>
</body>
</html>
