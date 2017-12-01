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
					$data = $GLOBALS['db']->select("SELECT fec_pedido AS fecha, YEAR(fec_pedido) AS anio FROM fme_asistencia GROUP BY YEAR(fec_pedido) ASC");
					 
					//Devuelvo los datos solicitados por el metodo ajax
					echo json_encode($data);
				}
?>