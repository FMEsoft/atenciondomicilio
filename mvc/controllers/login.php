<?php

	

	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);
	



session_start();

if (isset($_SESSION['usuario'])) {
	header('location:index.php');
}

$errore = '';

//para comprobar que enviemos informacion.
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$usuario = filter_var( strtolower($_POST['usuario']), FILTER_SANITIZE_STRING);
	$password = $_POST['password'];
	$password = hash('sha512', $password);


try
{//Base de datos ejemplo

$conexion = new PDO('mysql:host=localhost;dbname=fme_mutual','root','');

}catch(PDOException $e){

	echo "Error:" . $e->getMessage();
}

$statement= $conexion->prepare('
	SELECT * FROM usuarios WHERE usuario = :usuario AND password = :password LIMIT 1'
);

$statement->execute(array(
':usuario' => $usuario,
':password' => $password

));

$resultado = $statement->fetch();



if ( $resultado != false) {

	$_SESSION ['usuario'] = $usuario;
	$_SESSION ['tipo'] = $resultado['tipo'];

	//header('Location:index.php');
	//echo $GLOBALS['twig']->render('/Base/header.html',compact('usuario'));
	//echo $GLOBALS['twig']->render('/controllers/index.php',compact('resultado'));
	header('Location:index.php');
	return;
	
} else{

	//Seria mostrarlo abajo  los errores ...porque el cuadro de alert es molesto...
		$errore.= 'El usuario y/o la contraseña son incorrectos';
		echo $GLOBALS['twig']->render('/Atenciones/login.html', compact('errore'));	
		return;


	//	echo '<script type="text/javascript">'; echo 'alert("El usuario y/o la contraseña son incorrectos")'; echo '</script>';
	//$errores.= '<li>El usuario y/o la contraseña son incorrectos</li>';

}


}


	
	echo $twig->render('/Atenciones/login.html', compact('usuario'));


?>