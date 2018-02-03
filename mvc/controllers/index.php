<?php
require_once '../../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('../views');

$twig = new Twig_Environment($loader, []);




//---------------Esto se repetiria para cada uno de los php que añadimos//
session_start();

if (!isset($_SESSION['usuario'])) {

	header('location:login.php');
	# code...
}

$use=$_SESSION['usuario'];



$tipo=$_SESSION['tipo'];

//echo $tipo;









include ('conexion.php');
	$config['db']='fme_mutual';
	$config['dbuser']='root';
	$config['dbpass']='';
	$config['dbhost']='localhost';
	$config['dbEngine']='MYSQL';
	$db = new CONEXION($config['dbhost'],$config['dbuser'],$config['dbpass'],$config['db']);

$resultado = $db->select('SELECT socios.beneficio,socios.soc_titula,socios.numero_soc,persona.sexo,persona.nombre,persona.numdoc 
							FROM socios, persona 
							WHERE socios.soc_titula=socios.numero_soc 
							AND persona.id_persona=socios.id_persona;');

							
		
							
if($resultado)
{
	foreach($resultado as $res)
		{
			$asociado =[
				];				
		}
		
	echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_1.html', compact('asociado','resultado','use'));
}
else
{
	$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"Listado de asociados",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
}

//
?>