<?php

//Si la variable funcion no tiene ningun valor, se redirecciona al inicio------------
	if(!isset($_GET['funcion']))
	{
		require_once '../../vendor/autoload.php';

		$loader = new Twig_Loader_Filesystem('../views');

		$twig = new Twig_Environment($loader, []);
		
		echo $twig->render('/Inicio/inicio.html');
		
	}
	else
	{
		require_once '../../vendor/autoload.php';

		$loader = new Twig_Loader_Filesystem('../views');

		$twig = new Twig_Environment($loader, []);
	}

	
	$error=[
				'menu'			=>"Usuarios",
				'funcion'		=>"CrearUsuario",
				'descripcion'	=>"TODO PERFECTO"
				];
	echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));
	
?>