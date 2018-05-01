<?php

//Si la variable funcion no tiene ningun valor, se redirecciona al inicio------------
	if(!isset($_GET['funcion']))
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
	
	
function mostrarListado(){
	$use=$_SESSION['usuario'];
	
	$usuarios = $GLOBALS['db']->select('SELECT usuarios.id_usuario, usuarios.usuario, persona_sistema.nombre, persona_sistema.numdoc, persona_sistema.sexo FROM usuarios, persona_sistema
								WHERE usuarios.id_persona=persona_sistema.id_persona');								
	if($usuarios)
	{
		$i=0;
		foreach($usuarios as $res){
			if($res['sexo']=='M'){
				$usuarios[$i]['sexo']='Masculino';
			}
			else{
				$usuarios[$i]['sexo']='Femenino';
			}
			$i++;
		}

		$exito=0;
		if(isset($_GET['exito'])){
			$exito=1;
		}

		echo $GLOBALS['twig']->render('/Atenciones/usuarios_listado.html', compact('usuarios','use', 'exito'));
	}
	else
	{
		$error=[
				'menu'			=>"Usuarios",
				'funcion'		=>"Listado de usuarios",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
	}
}

function verMas(){
	
	if(!isset($_GET['id_usuario']))
	{
		$error=[
				'menu'			=>"Usuarios",
				'funcion'		=>"Perfil de usuarios",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	$id_usuario=$_GET['id_usuario'];
	
	$usuario = $GLOBALS['db']->select("SELECT * FROM usuarios, persona_sistema
								WHERE usuarios.id_persona=persona_sistema.id_persona
								AND usuarios.id_usuario='$id_usuario' ");
								
	if(!$usuario){
		$error=[
				'menu'			=>"Usuarios",
				'funcion'		=>"Perfil de usuarios",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	
	$usuario[0]['fech_creacion'] = date("d/m/Y", strtotime($usuario[0]['fech_creacion']));
	
			
	///---FUNCIÓN PARA CALCULAR EDAD----
	
	$fecha=$usuario[0]['fecnacim'];
	$dias = explode("-", $fecha, 3);
	
	// $dias[0] es el año
	// $dias[1] es el mes
	// $dias[2] es el dia
	
	// mktime toma los datos en el orden (0,0,0, mes, dia, año) 
	$dias = mktime(0,0,0,$dias[1],$dias[2],$dias[0]);
	$edad = (int)((time()-$dias)/31556926 );
	$usuario[0]['edad']=$edad;
	
	///---FIN FUNCIÓN PARA CALCULAR EDAD----
	
	echo $GLOBALS['twig']->render('/Atenciones/usuarios_perfil.html', compact('usuario'));
	
}

function mostrarFormulario(){
	echo $GLOBALS['twig']->render('/Atenciones/nuevo_usuario_formulario.html');
	return;
}

function validarUsuario(){
    $user = $_POST["user"];
	$usuario = $GLOBALS['db']->select("SELECT usuario FROM usuarios
								WHERE usuario='$user' ");

    if(!$usuario)
        echo 1;
    else
        echo 0;
	return;
}

function crearUsuario(){
	
	$use=$_SESSION['usuario']; //Usuario que realiza la creacion del nuevo usuario
	
	$nombre=$_POST['nombre'];
	$doc=$_POST['doc'];
	$sexo=$_POST['sexo'];
	$fech_nac=$_POST['fech_nac'];
	$tel_fijo=$_POST['fijo'];
	$tel_celu=$_POST['celu'];
	
	$dom=$_POST['dom'];
	$nrocasa=$_POST['nrocasa'];
	$barrio=$_POST['barrio'];
	$localidad=$_POST['localidad'];
	$cod_postal=$_POST['codpostal'];
	$dpto=$_POST['dpto'];
	
	$usuario=$_POST['user'];
	$pass=$_POST['pass'];
	
	date_default_timezone_set('America/Argentina/Catamarca');
	$fec_alta=date("Y")."-".date("m")."-".date("d");
	
	//Hacemos la prueba de que no quiera registrar un usuario ya existente
	$usuarioTEST = $GLOBALS['db']->select("SELECT usuario FROM usuarios
								WHERE usuario='$usuario' ");

    if(!$usuarioTEST)
        {
			$db=$GLOBALS['db'];



			$resultado=$GLOBALS['db']->query("INSERT INTO persona_sistema (nombre,numdoc,sexo,fecnacim,domicilio,casa_nro,barrio,localidad,codpostal,dpmto,tel_fijo,tel_cel,fec_alta,usualta)
				VALUES ('$nombre','$doc','$sexo','$fech_nac','$dom','$nrocasa','$barrio','$localidad','$cod_postal','$dpto','$tel_fijo','$tel_celu','$fec_alta','$use')");

			if(!$resultado)
			{
				$error=[
						'menu'			=>"Usuarios",
						'funcion'		=>"CrearUsuario",
						'descripcion'	=>"No se pudo crear el usuario, error tabla persona"
						];
				echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));

				return;
			}
			
			
			
			$resultado2=$GLOBALS['db']->query("INSERT INTO usuarios (id_persona, usuario, password, fech_creacion, activa)
						VALUES (LAST_INSERT_ID(),'$usuario','$pass','$fec_alta',1)");
						
			if(!$resultado2)
			{

				$error=[
						'menu'			=>"Usuarios",
						'funcion'		=>"CrearUsuario",
						'descripcion'	=>"No se pudo crear el usuario, error tabla usuarios"
						];
				echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	

				return;
			}
			header('Location: ./usuarios.php?funcion=mostrarListado&exito');
		}
    else
        {
			$error=[
					'menu'			=>"Usuarios",
					'funcion'		=>"CrearUsuario",
					'descripcion'	=>"No se pudo crear el usuario, el usuario ".$usuario." ya existe"
					];
			echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
			return;
		}
}
	

	
//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
?>