<?php
require_once '../../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('../views');

$twig = new Twig_Environment($loader, []);




//---------------Esto se repetiria para cada uno de los php que añadimos//

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
	

$use=$_SESSION['usuario'];
$priv=$_SESSION['privilegios'];


echo $GLOBALS['twig']->render('/Atenciones/index.html', compact('use','priv'));

//
?>