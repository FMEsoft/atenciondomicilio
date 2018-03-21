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
	
	$usuarios = $GLOBALS['db']->select('SELECT * FROM usuarios, persona_sistema
								WHERE usuarios.id_persona=persona_sistema.id_persona');								
	if($usuarios)
	{
		$i=0;
		foreach($usuarios as $res){
			if($res['sexo']=='M'){
				$usuarios[$i]['sexo']='Masculino';
			}
			else{
				$usuarios[$i]['sexo']='Femenino';
			}
			$i++;
		}
		echo $GLOBALS['twig']->render('/Atenciones/usuarios_listado.html', compact('usuarios'));
	}
	else
	{
		$error=[
				'menu'			=>"Usuarios",
				'funcion'		=>"Listado de usuarios",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
	}
}

function verMas(){
	
	if(!isset($_GET['id_usuario']))
	{
		$error=[
				'menu'			=>"Usuarios",
				'funcion'		=>"Perfil de usuarios",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	$id_usuario=$_GET['id_usuario'];
	
	$usuario = $GLOBALS['db']->select("SELECT * FROM usuarios, persona_sistema
								WHERE usuarios.id_persona=persona_sistema.id_persona
								AND usuarios.id_usuario='$id_usuario' ");
								
	if(!$usuario){
		$error=[
				'menu'			=>"Usuarios",
				'funcion'		=>"Perfil de usuarios",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	
	$usuario[0]['fech_creacion'] = date("d/m/Y", strtotime($usuario[0]['fech_creacion']));
	
	
	$fecha=$usuario[0]['fecnacim'];
			$dias = explode("-", $fecha, 3);
			$dias = mktime(0,0,0,$dias[2],$dias[1],$dias[0]);
			$edad = (int)((time()-$dias)/31556926 );
			$usuario[0]['edad']=$edad;
	
	echo $GLOBALS['twig']->render('/Atenciones/usuarios_perfil.html', compact('usuario'));
	
}
	

	
//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
?>