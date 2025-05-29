<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="registrarse.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Registrarse</title>
</head>
<body>
    <div class="form">
        <form action="crud.php?c=usuario&a=registrarse" method="POST">
            <h1>Registrarse</h1>

            <div class="mensaje">
                <?php
                    if(isset($_GET['error'])){
                    ?>
                        <p class="error"><?php echo $_GET['error'] ?></p>
                    <?php     
                    }
                ?>
                <?php
                    if(isset($_GET['success'])){
                    ?>
                        <p class="success"><?php echo $_GET['success'] ?></p>
                    <?php     
                    }
                ?>
                <?php
                    if (isset($_GET['error']) || isset($_GET['success'])) {
                        $url = strtok($_SERVER["REQUEST_URI"], '?'); // Elimina los parámetros
                        echo "<script>history.replaceState({}, '', '$url');</script>";
                    }
                ?>          
            </div>

            <div class="text">
                <i class="fa-solid fa-user"></i>
                <label for="">Usuario:</label>
                <input type="text" placeholder="Ingrese un usuario..." name="Username">
            </div>
            <div class="text">
                <i class="fa-solid fa-unlock-keyhole"></i>
                <label for="">Contraseña:</label>
                <input type="password" placeholder="Ingrese una contraseña..." name="Password">
            </div>
            <div class="text">
                <i class="fa-solid fa-key"></i>
                <label for="">Confirmar Contraseña:</label>
                <input type="password" placeholder="Ingrese confirmacion de contraseña..."  name="RPassword">
            </div>

            <button type="submit" class="btn">Registrarse</button>
            
            <div class="link">
                <p>Ya tiene una cuenta? <a href="../index.php">Iniciar Sesion</a></p>
            </div>
        </form>
    </div>
</body>
</html>