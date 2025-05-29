<?php

function cargarControlador($controlador){
	// use ucwords para convertir la primera letra en mayusculas, teniendo
    // en cuenta que nuestros controladores inician con la primera letra
    // en mayusculas. esto por si se equivocan y ponen la primera letra 
    // del nombre de un controlador en minusculas.
	$nombreControlador = ucwords($controlador)."Controller";
	$archivoControlador = '../controllers/'.ucwords($controlador).'Controller.php';
		//si no existe el archivo $archivoControlador
	if(!is_file($archivoControlador)){
		//debera cargar el controlador principal
		$archivoControlador= 'controllers/'.CONTROLADOR_PRINCIPAL.'.php';
	}
	require_once $archivoControlador;
	$control = new $nombreControlador();//creando el objeto
	return $control;
}
function cargarAccion($controller, $accion, $id = null){
	if(isset($accion) && method_exists($controller, $accion)){
		if($id == null){
			$controller->$accion();
		} else{
			$controller->$accion($id);
		}
	} else {
		$controller->ACCION_PRINCIPAL();
	}	
}