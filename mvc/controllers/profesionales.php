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
	
	$profesionales = $GLOBALS['db']->select('SELECT * FROM profesionales, persona_sistema
								WHERE profesionales.id_persona=persona_sistema.id_persona');								
	if($profesionales)
	{
		$i=0;
		foreach($profesionales as $res){
			if($res['sexo']=='M'){
				$profesionales[$i]['sexo']='Masculino';
			}
			else{
				$profesionales[$i]['sexo']='Femenino';
			}
			$i++;
		}

		$exito=0;
		if(isset($_GET['exito'])){
			$exito=1;
		}

		echo $GLOBALS['twig']->render('/Atenciones/profesionales_listado.html', compact('profesionales', 'exito'));
	}
	else
	{
		$error=[
				'menu'			=>"Profesionales",
				'funcion'		=>"Listado de profesionales",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
	}
}

function verMas(){
	
	if(!isset($_GET['id_profesional']))
	{
		$error=[
				'menu'			=>"Profesionales",
				'funcion'		=>"Perfil del profesional",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	$id_profesional=$_GET['id_profesional'];
	
	$profesional = $GLOBALS['db']->select("SELECT * FROM profesionales, persona_sistema
								WHERE profesionales.id_persona=persona_sistema.id_persona
								AND profesionales.id_profesional='$id_profesional' ");
								
	if(!$profesional){
		$error=[
				'menu'			=>"Profesionales",
				'funcion'		=>"Perfil del profesional",
				'descripcion'	=>"No se encontraron resultados."
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));
	}
	
	$profesional[0]['fech_creacion'] = date("d/m/Y", strtotime($profesional[0]['fech_alta']));
	
	
	///---FUNCIÓN PARA CALCULAR EDAD----
	
	$fecha=$profesional[0]['fecnacim'];
	$dias = explode("-", $fecha, 3);
	
	// $dias[0] es el año
	// $dias[1] es el mes
	// $dias[2] es el dia
	
	// mktime toma los datos en el orden (0,0,0, mes, dia, año) 
	$dias = mktime(0,0,0,$dias[1],$dias[2],$dias[0]);
	$edad = (int)((time()-$dias)/31556926 );
	$profesional[0]['edad']=$edad;
	
	///---FIN FUNCIÓN PARA CALCULAR EDAD----

	$modificar=0;
	if(isset($_GET['modificar'])){
		$modificar=1;
	}

	$modprofesional=0;
	if(isset($_GET['modprofesional'])){
		$modprofesional=1;
	}
	
	echo $GLOBALS['twig']->render('/Atenciones/profesionales_perfil.html', compact('profesional','modificar','modprofesional'));
	
}

function mostrarFormulario(){
	echo $GLOBALS['twig']->render('/Atenciones/nuevo_profesional_formulario.html');
	return;
}

function registrarProfesional(){
	
	$use=$_SESSION['usuario']; //Usuario que realiza la creacion del nuevo profesional
	
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
	
	$matr=$_POST['especialidad'];
	$espec=$_POST['matricula'];
	
	date_default_timezone_set('America/Argentina/Catamarca');
	$fec_alta=date("Y")."-".date("m")."-".date("d");
	
	
	$resultado=$GLOBALS['db']->query("INSERT INTO persona_sistema (nombre,numdoc,sexo,fecnacim,domicilio,casa_nro,barrio,localidad,codpostal,dpmto,tel_fijo,tel_cel,fec_alta,usualta)
				VALUES ('$nombre','$doc','$sexo','$fech_nac','$dom','$nrocasa','$barrio','$localidad','$cod_postal','$dpto','$tel_fijo','$tel_celu','$fec_alta','$use')");

	if(!$resultado)
	{
		$error=[
				'menu'			=>"Profesionales",
				'funcion'		=>"registrarProfesional",
				'descripcion'	=>"No se pudo registrar el profesional, error tabla persona"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	
	
	
	$resultado2=$GLOBALS['db']->query("INSERT INTO profesionales (id_persona, matricula, especialidad, fech_alta)
				VALUES (LAST_INSERT_ID(),'$matr','$espec','$fec_alta')");
				
	if(!$resultado2)
	{

		$error=[
				'menu'			=>"profesionales",
				'funcion'		=>"registrarProfesional",
				'descripcion'	=>"No se pudo crear el usuario, error tabla usuarios"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
		
	
	header('Location: ./profesionales.php?funcion=mostrarListado&exito');

}

function modificarProfesional(){
	$id_profesional=$_POST['id_profesional'];
	$espec=$_POST['espec'];
	$matricula=$_POST['matricula'];

	$res=$GLOBALS['db']->query("UPDATE profesionales SET 
	matricula='$matricula', especialidad='$espec'
	WHERE id_profesional='$id_profesional'");

	if(!$res){
		$error=[
			'menu'			=>"Profesionales",
			'funcion'		=>"Modificar datos del profesional",
			'descripcion'	=>"No se pudo modificar los datos del profesional ".$id_profesional
			];
			echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
			return;
	}
	


	header('Location: ./profesionales.php?funcion=verMas&id_profesional='.$id_profesional.'&modprofesional');
}

function modificarPersonaProfesional(){
	$id_profesional=$_POST['id_profesional'];
	$id_persona=$_POST['id_persona'];
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

	$res=$GLOBALS['db']->query("UPDATE persona_sistema SET nombre='$nombre', numdoc='$doc', sexo='$sexo', fecnacim='$fech_nac', domicilio='$dom',
	casa_nro='$nrocasa', barrio='$barrio', localidad='$localidad', codpostal='$cod_postal', dpmto='$dpto', tel_fijo='$tel_fijo', tel_cel='$tel_celu'
	WHERE id_persona='$id_persona'");

	if(!$res){
		$error=[
			'menu'			=>"Profesionales",
			'funcion'		=>"ModificarPersonaProfesional",
			'descripcion'	=>"No se pudo modificar los datos de la persona ".$id_persona
			];
			echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
			return;
	}
	


	header('Location: ./profesionales.php?funcion=verMas&id_profesional='.$id_profesional.'&modificar');
}

	
//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
?>