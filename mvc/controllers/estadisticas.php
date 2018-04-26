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
	
	
//funcion cantidadAtenciones
function cantidadAtenciones(){
	require_once '../../vendor/autoload.php';
	
		$loader = new Twig_Loader_Filesystem('../views');
	
		$twig = new Twig_Environment($loader, []);
	
		echo $twig->render('/Atenciones/estadisticas_cantatenciones.html', compact(''));
}


//funcion atencionesAsociados
function asociados(){
	require_once '../../vendor/autoload.php';
	
		$loader = new Twig_Loader_Filesystem('../views');
	
		$twig = new Twig_Environment($loader, []);
	
		echo $twig->render('/Atenciones/estadisticas_asociados.html', compact(''));
}

//funcion horarioAtenciones
function horarios(){
	require_once '../../vendor/autoload.php';
	
		$loader = new Twig_Loader_Filesystem('../views');
	
		$twig = new Twig_Environment($loader, []);
	
		echo $twig->render('/Atenciones/estadisticas_horario.html', compact(''));
}

//Funciones Accesibles mediante AJAX
//funcion para AJAX cantidadAsocadosProceso
function cantidadAtencionesProceso(){
	//Datos que recibo desde ajax por el metodo POST
	if(isset($_POST['anio']))
	{
		$anio = $_POST['anio'];
		
		if(isset($_POST['mes']) && isset($_POST['dia']))
			{
				$mes = $_POST['mes'];
				$dia = $_POST['dia'];
			
				//Consulta que me devuelve la cantidad de atenciones que se dieron en cada mes, del año enviado
				$data = $GLOBALS['db']->select("SELECT fec_ate AS fecha, COUNT(*) AS numatenciones 
												FROM fme_asistencia 
												WHERE YEAR(fec_ate)='$anio'
												AND MONTH(fec_ate)='$mes'
												AND DAY(fec_ate)='$dia'
												GROUP BY MONTH(fec_ate)");
				 
				//Devuelvo los datos solicitados por el metodo ajax
				echo json_encode($data);
			}
		else
			{
				//Consulta que me devuelve la cantidad de atenciones que se dieron en cada mes, del año enviado
				$data = $GLOBALS['db']->select("SELECT MONTH(fec_ate) AS mes, COUNT(*) AS numatenciones 
												FROM fme_asistencia 
												WHERE YEAR(fec_ate)='$anio' 
												GROUP BY MONTH(fec_ate)");
				 
				//Devuelvo los datos solicitados por el metodo ajax
				echo json_encode($data);
			}
	}
	else
			{
				//Consulta que devuelve los años de las atenciones que hay en la BD, son para rellenar el select
				$data = $GLOBALS['db']->select("SELECT fec_ate AS fecha, YEAR(fec_ate) AS anio FROM fme_asistencia WHERE YEAR(fec_ate)<>'0000' GROUP BY YEAR(fec_ate) ASC");
				 
				//Devuelvo los datos solicitados por el metodo ajax
				echo json_encode($data);
			}
}

//funcion para AJAX atencionesAsociadosProceso
function atencionesAsociadosProceso(){	
	//Datos que recibo desde ajax por el metodo POST
	if(isset($_POST['anio']))
	{
		$anio = $_POST['anio'];
		
		if(isset($_POST['mes']))
			{
				$mes = $_POST['mes'];
				
				if(isset($_POST['dia']))
					{
						$dia = $_POST['dia']; 
			
						//Consulta que me devuelve la cantidad de atenciones que recibió cada asociado, con el nombre del asociado y su numero de documento
						$data = $GLOBALS['db']->select("SELECT tablaAUX.cantidad AS cantidad, persona.numdoc AS numdoc, persona.nombre AS nombre 
														FROM (SELECT doctitu, nombre, COUNT(*) AS cantidad FROM fme_asistencia 
														WHERE YEAR(fec_ate)='$anio'
														AND MONTH(fec_ate)='$mes'
														AND DAY(fec_ate)='$dia'
														GROUP BY doctitu) AS tablaAUX 
														INNER JOIN persona on persona.numdoc = tablaAUX.doctitu ORDER BY cantidad DESC");
						 
						//Devuelvo los datos solicitados por el metodo ajax
						
						echo json_encode($data);
				}
				else
				{
					//Consulta que me devuelve la cantidad de atenciones que recibió cada asociado, con el nombre del asociado y su numero de documento
					$data = $GLOBALS['db']->select("SELECT tablaAUX.cantidad AS cantidad, persona.numdoc AS numdoc, persona.nombre AS nombre 
													FROM (SELECT doctitu, nombre, COUNT(*) AS cantidad FROM fme_asistencia 
													WHERE YEAR(fec_ate)='$anio'
													AND MONTH(fec_ate)='$mes'
													GROUP BY doctitu) AS tablaAUX 
													INNER JOIN persona on persona.numdoc = tablaAUX.doctitu ORDER BY cantidad DESC");
					 
					//Devuelvo los datos solicitados por el metodo ajax
					
					echo json_encode($data);
				}
			}
		else
			{
				//Consulta que me devuelve la cantidad de atenciones que se dieron en cada mes, del año enviado
				$data = $GLOBALS['db']->select("SELECT tablaAUX.cantidad AS cantidad, persona.numdoc AS numdoc, persona.nombre AS nombre 
												FROM (SELECT doctitu, nombre, COUNT(*) AS cantidad FROM fme_asistencia WHERE YEAR(fec_ate)='$anio' GROUP BY doctitu) AS tablaAUX 
												INNER JOIN persona on persona.numdoc = tablaAUX.doctitu ORDER BY cantidad DESC");
				 
				//Devuelvo los datos solicitados por el metodo ajax
				
				echo json_encode($data);
			}
	}
}

//funcion para AJAX cantidadAsocados
function horarioAtencionesProceso(){
	//Datos que recibo desde ajax por el metodo POST
	if(isset($_POST['anio']))
	{
		$anio = $_POST['anio'];
		
		if(isset($_POST['mes']))
			{
				$mes = $_POST['mes'];
				
				if(isset($_POST['dia']))
					{
						$dia = $_POST['dia']; 
			
						//Consulta que me devuelve las horas de los pedidos y la cantidad de atenciones que se dieron en esos horarios
						$data = $GLOBALS['db']->select("SELECT hora_pedido, HOUR(hora_pedido) AS hora, COUNT(*) AS cantidad FROM fme_asistencia 
												WHERE YEAR(fec_pedido)='$anio'													
												AND MONTH(fec_pedido)='$mes'
												AND DAY(fec_pedido)='$dia'
												GROUP BY HOUR(hora_pedido) 
												ORDER BY cantidad DESC");
						 
						//Devuelvo los datos solicitados por el metodo ajax
						echo json_encode($data);
				}
				else
				{
					//Consulta que me devuelve las horas de los pedidos y la cantidad de atenciones que se dieron en esos horarios
					$data = $GLOBALS['db']->select("SELECT hora_pedido, HOUR(hora_pedido) AS hora, COUNT(*) AS cantidad FROM fme_asistencia 
												WHERE YEAR(fec_pedido)='$anio'													
												AND MONTH(fec_pedido)='$mes'
												GROUP BY HOUR(hora_pedido) 
												ORDER BY cantidad DESC");
					 
					//Devuelvo los datos solicitados por el metodo ajax
					echo json_encode($data);
				}
			}
		else
			{
				//Consulta que me devuelve las horas de los pedidos y la cantidad de atenciones que se dieron en esos horarios
				$data = $GLOBALS['db']->select("SELECT hora_pedido, HOUR(hora_pedido) AS hora, COUNT(*) AS cantidad FROM fme_asistencia 
												WHERE YEAR(fec_pedido)='$anio' 
												GROUP BY HOUR(hora_pedido) 
												ORDER BY cantidad DESC");
				 
				//Devuelvo los datos solicitados por el metodo ajax
				echo json_encode($data);
			}
	}
	else
			{
				//Consulta que devuelve los años de las atenciones que hay en la BD, son para rellenar el select
				$data = $GLOBALS['db']->select("SELECT fec_pedido AS fecha, YEAR(fec_pedido) AS anio FROM fme_asistencia WHERE YEAR(fec_ate)<>'0000' GROUP BY YEAR(fec_pedido) ASC");
				 
				//Devuelvo los datos solicitados por el metodo ajax
				echo json_encode($data);
			}
}

//llamada a la funcion con el parametro pasado por la url.	
$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
$db->cerrar_sesion();
?>