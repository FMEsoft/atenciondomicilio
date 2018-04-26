<?php
//Si la variable funcion no tiene ningun valor, se redirecciona al inicio------------

session_start();

if (!isset($_SESSION['usuario'])) {

header('location:login.php');
# code...
}
$use=$_SESSION['usuario'];

if(!isset($_GET['id_atencion']))
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

$id_atencion=$_GET['id_atencion'];

echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_finalizar.html', compact('id_atencion','use'));	
?>