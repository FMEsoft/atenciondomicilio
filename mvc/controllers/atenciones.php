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
	
	//---------------CONSULTA DE PRUEBA----------------
	$resultado = $db->select('SELECT * FROM fme_asistencia WHERE doctitu = 04191748');
	
	
	if($resultado)
	{
            foreach($resultado as $res)
                echo "IDNUM: " . $res['idnum'] . " " ;
				echo "CODSER: " . $res['cod_ser'] . " ";
				echo "NROORDATE: " . $res['nroordate'] . " ";
				echo "DOCTITU: " . $res['doctitu'] . " ";
				echo "NUMDOC: " . $res['numdoc'] . " ";
				echo "NOMBRE: " . $res['nombre'] . " ";
	}
	else
		echo "algo va mal";
	
	//---------------CONSULTA DE PRUEBA----------------

	echo $twig->render('/Atenciones/nueva_atencion_1.html');	
	
?>