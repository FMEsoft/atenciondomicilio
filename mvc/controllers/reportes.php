<?php
	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);


	$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"Listado de asociados",
				'descripcion'	=>"En contrucción."
				];
	echo $twig->render('/Atenciones/error.html', compact('error'));

?>