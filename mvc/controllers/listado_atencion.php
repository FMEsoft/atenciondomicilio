<?php
	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);

	echo $twig->render('/Atenciones/listado_atencion.html');	
	
?>