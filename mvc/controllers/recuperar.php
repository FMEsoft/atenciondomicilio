<?php


//session_start();

	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);


	$errores = '';
	$exito ='';




	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//Hago el nombre usuario minuscul ademas de eso filtro letras y filtro la parte de sql inyeccion
	$usuario= filter_var(strtolower($_POST['usuario']), FILTER_SANITIZE_STRING);
	$sec_answer= $_POST['secure_answ'];
	$password = $_POST['pass'];
	$tipous= filter_var(strtolower($_POST['tipo']), FILTER_SANITIZE_STRING);



	$errores = '';

	if (empty($usuario) or empty($sec_answer)or empty($tipous)) {

		$errores.= 'Por favor rellena todos los campos';
		echo $GLOBALS['twig']->render('/Atenciones/recuperar.html', compact('errores'));	
			return;

	}
	else {

		try{
			$conexion = new PDO('mysql:host=localhost;dbname=fme_mutual','root','');


		}catch (PDOException $e){

			echo "Error: ". $e->getMessage();
		}

		$statement =$conexion->prepare('SELECT * FROM usuarios WHERE usuario = :usuario AND secur_answer = :sec_answer AND tipo = :tipous LIMIT 1');
		$statement->execute(array(':usuario' => $usuario,
		':sec_answer' => $sec_answer,
		':tipous' => $tipous));
		//comprobar si la consulta me devuelve error o el registro.
		$resultado=$statement->fetch();


		
		if($resultado != false){

			$passwordc = hash('sha512', $password);

			$statementes =$conexion->prepare('UPDATE usuarios SET password = :pass WHERE usuario = :usuario AND secur_answer = :sec_answer AND tipo = :tipous LIMIT 1');
			$statementes->execute(array(':usuario' => $usuario,
			':sec_answer' => $sec_answer,
			':tipous' => $tipous,
			':pass' => $passwordc ));

			$exito .= 'Su nueva contraseñia es:'.$password;

			echo $GLOBALS['twig']->render('/Atenciones/recuperar.html', compact('exito'));	
			return;
		}




	}



		
		$errores.= 'Los datos proporcionados son erroneos.Por favor ingresar nuevamente.';
		//echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('exito'));	
			

}





echo $twig->render('/Atenciones/recuperar.html', compact('errores'));

?>










