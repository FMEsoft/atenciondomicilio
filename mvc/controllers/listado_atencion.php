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

	
	
function mostrarListado()
{
	$use=$_SESSION['usuario'];
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

echo $GLOBALS['twig']->render('/Atenciones/listado_atencion.html',compact ('asistencias','use'));
}

function verMas()
{$use=$_SESSION['usuario'];

	if(!isset($_GET['num_asist']))
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"verMas Listado_atencion",
				'descripcion'	=>"No se recibió num_asist como parametro de la función"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	$numero_asistencia=$_GET['num_asist'];
	
	
	
		$asistencia = $GLOBALS['db']->select("SELECT fme_asistencia.cod_ser, fme_asistencia.fec_pedido, fme_asistencia.hora_pedido, 
													 fme_asistencia.nombre, persona.sexo, fme_asistencia.numdoc, fme_asistencia.doctitu,
													 fme_asistencia.profesional, fme_asistencia.dessit, fme_asistencia.fec_ate, fme_asistencia.hora_aten,
													 fme_asistencia.sintomas, fme_asistencia.tratamiento, fme_asistencia.diagnostico, 
													 fme_asistencia.domicilio, fme_asistencia.casa_nro, fme_asistencia.barrio, fme_asistencia.localidad,
													 fme_asistencia.codpostal, fme_asistencia.dpmto
									  FROM fme_asistencia, persona 
									  WHERE fme_asistencia.idnum='$numero_asistencia' AND persona.numdoc = fme_asistencia.numdoc");

		if(!$asistencia)
		{
			$error=[
			'menu'			=>"Atenciones",
			'funcion'		=>"verMas",
			'descripcion'	=>"No se encontraron resultados para la atencion '$numero_asistencia'."
			];
			echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
			return;
		}
	
		if($asistencia[0]['fec_ate']=="0000-00-00")
			$estado=0;
		else
			$estado=1;

		$persona=[
		'cod_serv'	=>	$asistencia[0]['cod_ser'],
		'fecha'		=>	$asistencia[0]['fec_pedido'],
		'hora'		=>	$asistencia[0]['hora_pedido'],
		'nombre'	=>	$asistencia[0]['nombre'],
		'doc'		=>	$asistencia[0]['numdoc'],
		'doctit'	=>	$asistencia[0]['doctitu'],
		'prof'		=>	$asistencia[0]['profesional'],
		'desc'		=>	$asistencia[0]['dessit'],
		'fech_ate'		=>	$asistencia[0]['fec_ate'],
		'hora_ate'		=>	$asistencia[0]['hora_aten'],
		'sintomas'		=>	$asistencia[0]['sintomas'],
		'tratamiento'		=>	$asistencia[0]['tratamiento'],
		'diagnostico'		=>	$asistencia[0]['diagnostico'],
		'dom'			=>	$asistencia[0]['domicilio'],
		'nro_casa'		=>	$asistencia[0]['casa_nro'],
		'barrio'		=>	$asistencia[0]['barrio'],
		'localidad'		=>	$asistencia[0]['localidad'],
		'cod_postal'	=>	$asistencia[0]['codpostal'],
		'dpmto'			=>	$asistencia[0]['dpmto'],
		'num_asistencia'	=>	$numero_asistencia
	];
	if($asistencia[0]['sexo']==1)
		$persona['sexo']='Masculino';
	else
		$persona['sexo']='Femenino';

echo $GLOBALS['twig']->render('/Atenciones/listado_atencion_formulario.html',compact ('persona','estado','use'));
}


function finalizarAtencion()
{$use=$_SESSION['usuario'];
	$num_asistencia=$_POST['num_asistencia'];
	$fecha_ate=$_POST['fech_ate'];		
	$hora_ate=$_POST['hora_ate'];
	$sintomas=$_POST['sintomas'];
	$diagnostico=$_POST['diagnostico'];
	$tratamiento=$_POST['tratamiento'];
	$resultado=$GLOBALS['db']->query("UPDATE fme_asistencia SET fec_ate='$fecha_ate',sintomas='$sintomas',diagnostico='$diagnostico',tratamiento='$tratamiento',hora_aten='$hora_ate'
										WHERE idnum='$num_asistencia'");
	
	if(!$resultado)
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"FinalizarAtencion",
				'descripcion'	=>"No se pudo realizar la consulta: UPDATE fme_asistencia SET fec_ate='$fecha_ate',sintomas='$sintomas',diagnostico='$diagnostico',tratamiento='$tratamiento',hora_aten='$hora_ate'
										WHERE idnum='$num_asistencia'"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	
	$_GET['num_asist']=$num_asistencia;
	verMas();
}


//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
	
?>