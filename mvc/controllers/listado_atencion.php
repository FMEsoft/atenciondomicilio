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
	
//array para la conexion de la bd
	include ('conexion.php');
	$config['db']='fme_mutual';
	$config['dbuser']='root';
	$config['dbpass']='';
	$config['dbhost']='localhost';
	$config['dbEngine']='MYSQL';
//para acceder a la variable $db en el ambito de una funcion, se usará la variable super global $GLOBALS['db'], de manera tal queda definida una unica vez la bd
	$db = new CONEXION($config['dbhost'],$config['dbuser'],$config['dbpass'],$config['db']);
	
function mostrarListado()
{
	$asistencias1 = $GLOBALS['db']->select("SELECT fme_asistencia.idnum, fme_asistencia.nombre, fme_asistencia.numdoc, persona.numdoc, persona.sexo, fme_asistencia.fec_pedido
									  FROM fme_asistencia, persona 
									  WHERE persona.numdoc = fme_asistencia.numdoc AND fme_asistencia.fec_ate='' ORDER BY fme_asistencia.fec_ate,fme_asistencia.hora_aten ASC");
									  
	$asistencias2 = $GLOBALS['db']->select("SELECT fme_asistencia.idnum, fme_asistencia.nombre, fme_asistencia.numdoc, persona.numdoc, persona.sexo, fme_asistencia.fec_pedido
									  FROM fme_asistencia, persona 
									  WHERE persona.numdoc = fme_asistencia.numdoc AND fme_asistencia.fec_ate<>'' ORDER BY fme_asistencia.fec_ate,fme_asistencia.hora_aten ASC");
	
	if(!$asistencias1 && !$asistencias2)
	{
		$error=[
		'menu'			=>"Atenciones",
		'funcion'		=>"mostrarListado",
		'descripcion'	=>"No se encontraron resultados de atenciones."
		];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
		return;
	}
	
	$asistencias;
	$i=0;
	if($asistencias1)
	{
		foreach($asistencias1 as $res1)
		{
			$asistencias[$i]['nro']		=	$res1['idnum'];
			$asistencias[$i]['nombre']	=	$res1['nombre'];
			$asistencias[$i]['dni']		=	$res1['numdoc'];
			if($res1['sexo']==1)
			{
				$asistencias[$i]['sexo']	=	'Masculino';
			}
			else
			{
				$asistencias[$i]['sexo']	=	'Femenino';
			}
			$asistencias[$i]['estado']	=	'0';
			$asistencias[$i]['fecha']	=	$res1['fec_pedido'];
			$i=$i+1;
		}
	}

	if($asistencias2)
	{
		foreach($asistencias2 as $res1)
		{
			$asistencias[$i]['nro']		=	$res1['idnum'];
			$asistencias[$i]['nombre']	=	$res1['nombre'];
			$asistencias[$i]['dni']		=	$res1['numdoc'];
			if($res1['sexo']==1)
			{
				$asistencias[$i]['sexo']	=	'Masculino';
			}
			else
			{
				$asistencias[$i]['sexo']	=	'Femenino';
			}
			$asistencias[$i]['estado']	=	'1';
			$asistencias[$i]['fecha']	=	$res1['fec_pedido'];
			$i=$i+1;
		}
	}

echo $GLOBALS['twig']->render('/Atenciones/listado_atencion.html',compact ('asistencias'));
}

function verMas()
{
	if(!isset($_GET['num_asist']))
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"verMas Listado_atencion",
				'descripcion'	=>"No se recibió num_asist como parametro de la función"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
		return;
	}
	
	
}

/*
function completarAtencion()
{
}
*/

//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
	
?>