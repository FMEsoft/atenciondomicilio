<?php
	require_once '../../vendor/autoload.php';

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);


session_start();

$errores = '';
$exito ='';





if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//Hago el nombre usuario minuscul ademas de eso filtro letras y filtro la parte de sql inyeccion
	$usuario= filter_var(strtolower($_POST['usuario']), FILTER_SANITIZE_STRING);
	$password= $_POST['password'];
	$password2= $_POST['password2'];
	$sec_answer= $_POST['secure_answ'];


	//echo '<h1>'.$usuario.'</h1>'.$password.$password2;

	$errores = '';

	if (empty($usuario) or empty($password)or empty($password2)) {

		$errores.= 'Por favor rellena todos los campos';
		echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('errores'));	
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

			echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('errores'));	
			return;
		}

		//aplicar un hash a la contraseña es para evitar el robo de contraseñas de manera facil en caso de contar con la BD.Y ademas no esta bien que yo tenga la contraseña ...aunque puede ser que si...ya lo veo...
		//investigar otras formas de seguridad para la contraseña o loguin aunq mejor enfocar en la contraseña.

		//sha512 es el algoritmo de encriptacion

		$password=hash('sha512', $password);

		$password2=hash('sha512', $password2);

	//echo '<h1>'.$usuario.'</h1>'.$password.$password2;

		if ($password != $password2) {


			$errores.= 'Las contraseñas no son iguales';
			echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('errores'));	
			return;
			}


	}


	if ($errores == '') {
		// no hay errores

		$statement = $conexion->prepare('INSERT INTO usuarios (id_user,usuario,password,secur_answer) VALUES (null,:usuario,:pass,:secur )');

		$statement->execute(array(
			':usuario' => $usuario ,
			':pass' => $password   ,
			':secur' => $sec_answer));

		
		$exito.= 'usuario creado con exito';
		echo $GLOBALS['twig']->render('/Atenciones/registro.html', compact('exito'));	
			
		
		


	}
}


//$nombre=$_POST['usuario'];



echo $twig->render('/Atenciones/registro.html', compact(''));

?>



