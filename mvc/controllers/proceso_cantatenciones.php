<?php
	//Clase para manejo de la conexion
	include ('conexion.php');
	//Datos para la conexion
	$config['db']='fme_mutual';
	$config['dbuser']='root';
	$config['dbpass']='';
	$config['dbhost']='localhost';
	$config['dbEngine']='MYSQL';
	
	//Para acceder a la variable $db en el ambito de una funcion, se usará la variable super global $GLOBALS['db']
	//de manera tal queda definida una unica vez la bd
	$db = new CONEXION($config['dbhost'],$config['dbuser'],$config['dbpass'],$config['db']);
	
	
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
					$data = $GLOBALS['db']->select("SELECT fec_ate AS fecha, YEAR(fec_ate) AS anio FROM fme_asistencia GROUP BY YEAR(fec_ate) ASC");
					 
					//Devuelvo los datos solicitados por el metodo ajax
					echo json_encode($data);
				}
?>