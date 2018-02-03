<?php
	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);

	session_start();

	if (!isset($_SESSION['usuario'])) {

	header('location:login.php');
	# code...
	}

	
	if (strcmp($_SESSION['tipo'], 'admin')!= 0) {
		header('location:index.php');

	}

	$use=$_SESSION['usuario'];

	$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"Listado de asociados",
				'descripcion'	=>"En contrucción."
				];
	echo $twig->render('/Atenciones/error.html', compact('error','use'));

?>