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
	

	session_start();

	if (!isset($_SESSION['usuario'])) {

	header('location:login.php');
	# code...
	}

	
//array para la conexion de la bd
	include ('conexion.php');
	$config['db']='fme_mutual';
	$config['dbuser']='root';
	$config['dbpass']='';
	$config['dbhost']='localhost';
	$config['dbEngine']='MYSQL';
//para acceder a la variable $db en el ambito de una funcion, se usará la variable super global $GLOBALS['db'], de manera tal queda definida una unica vez la bd
	$db = new CONEXION($config['dbhost'],$config['dbuser'],$config['dbpass'],$config['db']);
	
	
function mostrarListado(){
	
	$profesionales = $GLOBALS['db']->select('SELECT * FROM profesionales, persona_sistema
								WHERE profesionales.id_persona=persona_sistema.id_persona');								
	if($profesionales)
	{
		$i=0;
		foreach($profesionales as $res){
			if($res['sexo']=='M'){
				$profesionales[$i]['sexo']='Masculino';
			}
			else{
				$profesionales[$i]['sexo']='Femenino';
			}
			$i++;
		}
		echo $GLOBALS['twig']->render('/Atenciones/profesionales_listado.html', compact('profesionales'));
	}
	else
	{
		$error=[
				'menu'			=>"Profesionales",
				'funcion'		=>"Listado de profesionales",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
	}
}

function verMas(){
	
	if(!isset($_GET['id_profesional']))
	{
		$error=[
				'menu'			=>"Profesionales",
				'funcion'		=>"Perfil del profesional",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	$id_profesional=$_GET['id_profesional'];
	
	$profesional = $GLOBALS['db']->select("SELECT * FROM profesionales, persona_sistema
								WHERE profesionales.id_persona=persona_sistema.id_persona
								AND profesionales.id_profesional='$id_profesional' ");
								
	if(!$profesional){
		$error=[
				'menu'			=>"Profesionales",
				'funcion'		=>"Perfil del profesional",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	
	$profesional[0]['fech_creacion'] = date("d/m/Y", strtotime($profesional[0]['fech_alta']));
	
	
	$fecha=$profesional[0]['fecnacim'];
			$dias = explode("-", $fecha, 3);
			$dias = mktime(0,0,0,$dias[2],$dias[1],$dias[0]);
			$edad = (int)((time()-$dias)/31556926 );
			$profesional[0]['edad']=$edad;
	
	echo $GLOBALS['twig']->render('/Atenciones/profesionales_perfil.html', compact('profesional'));
	
}
	

	
//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
?>