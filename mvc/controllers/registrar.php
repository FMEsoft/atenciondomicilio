<?php
	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);


	session_start();

	if (!isset($_SESSION['usuario'])||($_SESSION['tipo']!='admin')) {

	header('location:login.php');
	# code...
	}


$errores = '';
$exito ='';

$use=$_SESSION['usuario'];




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//Hago el nombre usuario minuscul ademas de eso filtro letras y filtro la parte de sql inyeccion
	$usuario= filter_var(strtolower($_POST['usuario']), FILTER_SANITIZE_STRING);
	$password= $_POST['password'];
	$password2= $_POST['password2'];
	$sec_answer= $_POST['secure_answ'];
	$tipous= filter_var(strtolower($_POST['tipo']), FILTER_SANITIZE_STRING);



	$errores = '';

	if (empty($usuario) or empty($password) or empty($password2)) {

		$errores.= 'Por favor rellena todos los campos';
		echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('errores','use'));	
			return;

	}
	else {

		try{
			$conexion = new PDO('mysql:host=localhost;dbname=fme_mutual','root','');


		}catch (PDOException $e){

			echo "Error: ". $e->getMessage();
		}

		$statement =$conexion->prepare('SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1');
		$statement->execute(array(':usuario' => $usuario));
		//comprobar si la consulta me devuelve error o el registro.
		$resultado=$statement->fetch();

		
		if($resultado != false){
			$errores .= 'El Nombre de usuario ya existe, Por favor ingresar otro';

			echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('errores','use'));	
			return;
		}

		

		$password=hash('sha512', $password);

		$password2=hash('sha512', $password2);

	//echo '<h1>'.$usuario.'</h1>'.$password.$password2;

		if ($password != $password2) {


			$errores.= 'Las contraseñas no son iguales';
			echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('errores','use'));	
			return;
			}


	}


	if ($errores == '') {
		// no hay errores

		$statement = $conexion->prepare('INSERT INTO usuarios (id_user,usuario,password,secur_answer,tipo) VALUES (null,:usuario,:pass,:secur,:tipous )');

		$statement->execute(array(
			':usuario' => $usuario ,
			':pass' => $password   ,
			':secur' => $sec_answer ,
			':tipous' => $tipous	));

		
		$exito.= 'usuario creado con exito';
		//echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('exito'));	
			
		
		


	}
}





echo $twig->render('/Atenciones/registro.html', compact('use','exito'));

?>



