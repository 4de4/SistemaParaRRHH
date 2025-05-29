<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="./views/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Iniciar Sesion</title>
</head>
<body>
    <div class="form">
        <form action="views/crud.php?c=usuario&a=iniciar" method="POST">
            <h1>Iniciar Sesion</h1>

            <div class="mensaje">
                <?php
                    if(isset($_GET['error'])){
                    ?>
                        <p class="error"><?php echo $_GET['error'] ?></p>
                    <?php     
                    }
                ?>
                <?php
                    if (isset($_GET['error'])) {
                        $url = strtok($_SERVER["REQUEST_URI"], '?'); // Elimina los parámetros
                        echo "<script>history.replaceState({}, '', '$url');</script>";
                    }
                ?>          
            </div>

            <div class="text">
                <i class="fa-solid fa-user"></i>
                <label for="">Usuario:</label>
                <input type="text" placeholder="Ingrese su usuario..." name="username">
            </div>
            <div class="text">
                <i class="fa-solid fa-unlock"></i>
                <label for="">Contraseña:</label>
                <input type="password" placeholder="Ingrese su contraseña..." name="password">
            </div>
            
            <button type="submit" class="btn">Iniciar Sesión</button>
            
            <div class="link">
                <p>No tiene una cuenta? <a href="./views/registrarse.php">Registrarse</a></p>
            </div>
        </form>
    </div>
</body>
</html>