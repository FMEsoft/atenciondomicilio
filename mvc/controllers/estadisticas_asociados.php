<?php
	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);

	session_start();

	if (!isset($_SESSION['usuario'])||($_SESSION['tipo']!='admin')) {

	header('location:login.php');
	# code...
	}

	$use=$_SESSION['usuario'];
	
	echo $twig->render('/Atenciones/estadisticas_asociados.html', compact('use'));

?>