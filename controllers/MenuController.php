<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class MenuController {

    public function __construct(){
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ../index.php?error=Acceso denegado. Debe iniciar sesión.");
            exit();
        }
    }

    public function index(){
        $data["titulo_pagina"] = "Panel Principal - RRHH";
        $data["nombre_usuario"] = $_SESSION['username'] ?? 'Usuario';
        $data["rol"] = $_SESSION['rol'] ?? 'default';

        require_once "../views/menu.php";
    }
}
?>