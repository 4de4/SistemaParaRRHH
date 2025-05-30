<?php
//session_start();

if (isset($_POST['conexion'])) {
    $_SESSION['db_driver'] = $_POST['conexion']; // 'pdo' o 'mysqli'
    // Redireccionar a otra vista o mostrar mensaje
    header("Location: ../views/crud.php");
    exit();
}