<?php
	require_once '../../vendor/autoload.php';
	
	include ('conexion.php');

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);
	
	// Array de conexión con la base de datos
	$config['db']='fme_mutual';
	$config['dbuser']='root';
	$config['dbpass']='';
	$config['dbhost']='localhost';
	$config['dbEngine']='MYSQL';
	
	$db = new CONEXION($config['dbhost'],$config['dbuser'],$config['dbpass'],$config['db']);
	
	//NOTA: Para que el ejemplo funcione tienen que asociarle un adherente en la tabla socios, ya que en los datos de ejemplo todos son titulares
	//NOTA: Lo que hice fue: en tabla socios en numero_soc=00044 cambiar el campo soc_titula de manera que quede soc_titula=00277
	
	//---------------CONSULTA QUE DEVUELVE TODA LA INFO DEL ASOCIADO TITULAR----------------
	
	$numero_socio = "00277"; //Es el número del socio titular que debe ser tomado del PASO 1
	
	$resultadoTitular = $db->select("SELECT * FROM socios, persona 
							  WHERE soc_titula = '$numero_socio' 
							  AND socios.id_persona = persona.id_persona
							  AND numero_soc = soc_titula");
	
	if(!$resultadoTitular)
	echo "Ocurrió un error en la consulta de la info del asociado titular";
	
	
	//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS SERVICIOS DEL ASOCIADO TITULAR----------------
	
	//Por cuota
	$resultadoTitularServicios1 = $db->select("SELECT * FROM fme_adhsrv, tar_srv,socios 
							   WHERE socnumero = '$numero_socio' 
							   AND fme_adhsrv.codigo = tar_srv.idmutual
							   AND soc_titula = '$numero_socio'
							   AND numero_soc = soc_titula");
							   
	if(!$resultadoTitularServicios1)
	echo "Ocurrió un error en la consulta de los servicios del asociado titular en aportes por cuota";
	
	//Por tarjeta
	$resultadoTitularServicios2 = $db->select("SELECT * FROM tar_srvadherentes, tar_srv, socios 
								WHERE socnumero = '$numero_socio' 
								AND tar_srvadherentes.codigo = tar_srv.codigo
								AND soc_titula = '$numero_socio'
								AND numero_soc = soc_titula");
	
	if(!$resultadoTitularServicios2)
	echo "Ocurrió un error en la consulta de los servicios del asociado titular en aportes por tarjeta";
	
	
	//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS ADHERENTES DEL ASOCIADO TITULAR CON APORTES POR CUOTA----------------
	
	$resultadoAdherentes1 = $db->select("SELECT * FROM socios, fme_adhsrv, tar_srv
							   WHERE soc_titula = '$numero_socio' 
							   AND socios.numero_soc <> '$numero_socio' 
							   AND socios.numero_soc = fme_adhsrv.socnumero
							   AND fme_adhsrv.codigo = tar_srv.idmutual");
	
	if(!$resultadoAdherentes1)
	echo "Ocurrió un error en la consulta de los servicios del ADHERENTE en aportes por cuota";

	//Esta consulta solo recoge los nombres
	$resultadoAdherentes1Nombres = $db->select("SELECT * FROM socios, fme_adhsrv
							   WHERE soc_titula = '$numero_socio' 
							   AND socios.numero_soc <> '$numero_socio' 
							   AND socios.numero_soc = fme_adhsrv.socnumero");
	
	if(!$resultadoAdherentes1Nombres)
	echo "Ocurrió un error en la consulta del NOMBRE del ADHERENTE en aportes por cuota";
	
	
	//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS ADHERENTES DEL ASOCIADO TITULAR CON APORTES POR TARJETA----------------
	
	$resultadoAdherentes2 = $db->select("SELECT * FROM socios, tar_srvadherentes, tar_srv 
							   WHERE socios.soc_titula = '$numero_socio' 
							   AND numero_soc <> '$numero_socio' 
							   AND socios.numero_soc = tar_srvadherentes.socnumero
							   AND tar_srvadherentes.codigo = tar_srv.codigo");
	
	if(!$resultadoAdherentes2)
	echo "Ocurrió un error en la consulta de los servicios del ADHERENTE en aportes por tarjeta";

	//Esta consulta solo recoge los nombres
	$resultadoAdherentes2Nombres = $db->select("SELECT * FROM socios, tar_srvadherentes 
							   WHERE socios.soc_titula = '$numero_socio' 
							   AND numero_soc <> '$numero_socio' 
							   AND socios.numero_soc = tar_srvadherentes.socnumero");
	
	if(!$resultadoAdherentes2Nombres)
	echo "Ocurrió un error en la consulta del NOMBRE del ADHERENTE en aportes por tarjeta";
	
	
	

	echo $twig->render('/Atenciones/VerMas.html', compact('resultadoTitular', 
														  'resultadoTitularServicios1', 
														  'resultadoTitularServicios2', 
														  'resultadoAdherentes1',
														  'resultadoAdherentes1Nombres',
														  'resultadoAdherentes2',
														  'resultadoAdherentes2Nombres'));	
	
	$db->cerrar_sesion();
	
?>