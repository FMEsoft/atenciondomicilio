<?php
require_once '../../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('../views');

$twig = new Twig_Environment($loader, []);



session_start();








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
		
	echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_1.html', compact('asociado','resultado'));
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


?>