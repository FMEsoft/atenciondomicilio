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
?>