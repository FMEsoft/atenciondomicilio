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
	
	
//Para acceder a cada funcion se debe pasar por parametro una variable de nombre funcion=nombrefuncion; por ejemeplo atenciones.php?funcion=nuevaAtencion		
	
		
		
//funcion verMAS, el cual debe realizar la consulta del asociado seleccionado para mostrar toda su informacion
function verMas()	
		{
			
			$use=$_SESSION['usuario'];
			
			//NOTA: Para ver si funciona tienen que asociarle un adherente en la tabla socios, ya que en los datos de ejemplo todos son titulares
			//NOTA: Lo que hice fue: en tabla socios en numero_soc=00044 cambiar el campo soc_titula de manera que quede soc_titula=00277
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DEL ASOCIADO TITULAR----------------
			
			$numero_socio = $_GET['num_soc']; //Es el número del socio titular que debe ser tomado del PASO 1
			

				$resultadoTitular = $GLOBALS['db']->select("SELECT socios.id_persona,socios.numero_soc,socios.beneficio,socios.fec_alt,socios.fec_baja,socios.lugar_pago,socios.soc_titula
				,persona.id_persona,persona.nombre,persona.numdoc,persona.cuil,persona.sexo,persona.fecnacim,persona.domicilio,persona.casa_nro,persona.barrio,persona.localidad,persona.codpostal
				,persona.dpmto,persona.tel_fijo,persona.tel_cel,persona.fec_alta AS fec_alta2,persona.fec_baja AS fec_baja2,persona.cbu,persona.banco,persona.usualta
				,fme_adhsrv.codigo,fme_adhsrv.parentesco,fme_adhsrv.periodoini,fme_adhsrv.periodofin,fme_adhsrv.motivobaja,fme_adhsrv.documento
				,tar_srv.nombre AS nombreplan,tar_srv.idmutual FROM socios,persona,fme_adhsrv,tar_srv WHERE socios.soc_titula = '$numero_socio' 
									  AND socios.id_persona = persona.id_persona
									  AND persona.numdoc = fme_adhsrv.documento
									  AND fme_adhsrv.codigo = tar_srv.idmutual
                                      AND socios.numero_soc= socios.soc_titula");
									 

			if(!$resultadoTitular)
			{
				$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"verMas",
				'descripcion'	=>"No se encuentra al titular $numero_socio"
				];
				echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
				return;
			}
			
			$fecha=$resultadoTitular[0]['fecnacim'];
			$dias = explode("-", $fecha, 3);
			$dias = mktime(0,0,0,$dias[2],$dias[1],$dias[0]);
			$edad = (int)((time()-$dias)/31556926 );
			$resultadoTitular[0]['edad']=$edad;
			
			$estado[0]='1';
			$estado[1]='1';
			$estado[2]='1';
			$estado[3]='1';
			$estado[4]='1';
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS SERVICIOS DEL ASOCIADO TITULAR----------------
			
			//Por cuota
			$resultadoTitularServicios1 = $GLOBALS['db']->select("SELECT * FROM fme_adhsrv, tar_srv,socios 
									   WHERE socnumero = '$numero_socio' 
									   AND fme_adhsrv.codigo = tar_srv.idmutual
									   AND soc_titula = '$numero_socio'
									   AND numero_soc = soc_titula");
									   
			if(!$resultadoTitularServicios1)
				$estado[0]='0';
			
			//Por tarjeta
			$resultadoTitularServicios2 = $GLOBALS['db']->select("SELECT * FROM tar_srvadherentes, tar_srv, socios 
										WHERE socnumero = '$numero_socio' 
										AND tar_srvadherentes.codigo = tar_srv.codigo
										AND soc_titula = '$numero_socio'
										AND numero_soc = soc_titula");
			
			if(!$resultadoTitularServicios2)
				$estado[1]='0';
			
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS ADHERENTES DEL ASOCIADO TITULAR CON APORTES POR CUOTA----------------
			

		   $resultadoAdherentes1 = $GLOBALS['db']->select("SELECT socios.id_persona,socios.numero_soc,socios.beneficio,socios.fec_alt,socios.fec_baja,socios.lugar_pago,socios.soc_titula
				,persona.id_persona,persona.nombre,persona.numdoc,persona.cuil,persona.sexo,persona.fecnacim,persona.domicilio,persona.casa_nro,persona.barrio,persona.localidad,persona.codpostal
				,persona.dpmto,persona.tel_fijo,persona.tel_cel,persona.fec_alta AS fec_alta2,persona.fec_baja AS fec_baja2,persona.cbu,persona.banco,persona.usualta
				,fme_adhsrv.codigo,fme_adhsrv.parentesco,fme_adhsrv.periodoini,fme_adhsrv.periodofin,fme_adhsrv.motivobaja,fme_adhsrv.documento
				,tar_srv.nombre AS nombreplan,tar_srv.idmutual FROM socios,persona,fme_adhsrv,tar_srv
									 WHERE soc_titula = '$numero_socio' 
									   AND socios.numero_soc != '$numero_socio'
                                       AND socios.id_persona = persona.id_persona
									   AND socios.numero_soc = fme_adhsrv.socnumero
									   AND fme_adhsrv.codigo = tar_srv.idmutual;");
			
			if(!$resultadoAdherentes1)
				$estado[2]='0';
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS ADHERENTES DEL ASOCIADO TITULAR CON APORTES POR TARJETA----------------

//esta consulta habria q revisarla con mas datos una vez q el señor ip nos pase ++DB.

			$resultadoAdherentes2 = $GLOBALS['db']->select("SELECT socios.id_persona,socios.numero_soc,socios.beneficio,socios.fec_alt,socios.fec_baja,socios.lugar_pago,socios.soc_titula
				,persona.id_persona,persona.nombre,persona.numdoc,persona.cuil,persona.sexo,persona.fecnacim,persona.domicilio,persona.casa_nro,persona.barrio,persona.localidad,persona.codpostal
				,persona.dpmto,persona.tel_fijo,persona.tel_cel,persona.fec_alta AS fec_alta2,persona.fec_baja AS fec_baja2,persona.cbu,persona.banco,persona.usualta
				,fme_adhsrv.codigo,fme_adhsrv.parentesco,fme_adhsrv.periodoini,fme_adhsrv.periodofin,fme_adhsrv.motivobaja,fme_adhsrv.documento
				,tar_srv.nombre AS nombreplan,tar_srv.idmutual FROM socios,persona,fme_adhsrv,tar_srv,tar_srvadherentes
									   WHERE socios.soc_titula = '$numero_socio' 
									   AND numero_soc <> '$numero_socio' 
									   AND socios.numero_soc = tar_srvadherentes.socnumero
									   AND tar_srvadherentes.codigo = tar_srv.codigo");	

			

								   
			
			if(!$resultadoAdherentes2)
				$estado[3]='0';
		
		    
			//---------------CONSULTA QUE DEVUELVE EL LISTADO DE TODAS LAS ASISTENCIAS----------------
			
			//NOTA: Para que puedan ver si funciona o no hacer la prueba con el siguiente ejemplo:
			// En la tabla fme_asistencia modifiquen en cualquier lado y pongan alguno con doctitu = 06948018 (o busquen cualquier DNI de un socio titular y usen ese)
			// Cuando prueben el sistema vayan al ver más de Barrionuevo Samuel y van a ver el listado de atenciones que tiene asociado
			
			$asistencias = $GLOBALS['db']->select("SELECT fme_asistencia.doctitu, fme_asistencia.numdoc, fme_asistencia.nombre,
									  fme_asistencia.fec_pedido, fme_asistencia.hora_pedido, fme_asistencia.dessit, fme_asistencia.fec_ate,
									  fme_asistencia.sintomas, fme_asistencia.diagnostico, fme_asistencia.tratamiento, fme_asistencia.hora_aten,
									  fme_asistencia.profesional, fme_asistencia.feccanasis, fme_asistencia.horacanasis, fme_asistencia.motivo,
									  fme_asistencia.cuenta, fme_asistencia.idafiliado
									  FROM fme_asistencia, socios, persona 
									  WHERE soc_titula = '$numero_socio' 
									  AND socios.id_persona = persona.id_persona
									  AND numero_soc = soc_titula
									  AND persona.numdoc = fme_asistencia.doctitu");
			
			if(!$asistencias)
				$estado[4]='0';
			
			
			

			echo $GLOBALS['twig']->render('/Atenciones/perfil.html', compact('resultadoTitular', 
																  'resultadoTitularServicios1', 
																  'resultadoTitularServicios2', 
																  'resultadoAdherentes1',
																  'resultadoAdherentes2',
																  'asistencias',
																  'estado','use'));	
			
			
		}
		
		
function verMasParticular()	
		{
			
			$use=$_SESSION['usuario'];
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DEL PARTICULAR----------------
			
			$id_persona = $_GET['persona']; //Es el número del particular
			
			$particular = $GLOBALS['db']->select("SELECT fme_adhsrv.id_persona, fme_adhsrv.codigo, persona.nombre, persona.sexo,persona.fecnacim, 
			persona.domicilio,persona.casa_nro,persona.barrio,persona.localidad,persona.codpostal
			FROM persona, fme_adhsrv
			WHERE persona.id_persona='$id_persona'
			AND fme_adhsrv.codigo=021 
			AND fme_adhsrv.id_persona=persona.id_persona");

			
			if(!$particular)
			{
				$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"verMas",
				'descripcion'	=>"No se encuentra al particular $id_persona"
				];
				echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
				return;
			}
			
			$fecha=$particular[0]['fecnacim'];
			$dias = explode("-", $fecha, 3);
			$dias = mktime(0,0,0,$dias[2],$dias[1],$dias[0]);
			$edad = (int)((time()-$dias)/31556926 );
			$particular[0]['edad']=$edad;
			
			$estado='1';
			
			
			//---------------CONSULTA QUE DEVUELVE EL LISTADO DE TODAS LAS ASISTENCIAS----------------
			
			$asistencias = $GLOBALS['db']->select("SELECT fme_asistencia.numdoc, fme_asistencia.nombre,
									  fme_asistencia.fec_pedido, fme_asistencia.hora_pedido, fme_asistencia.dessit, fme_asistencia.fec_ate,
									  fme_asistencia.sintomas, fme_asistencia.diagnostico, fme_asistencia.tratamiento, fme_asistencia.hora_aten,
									  fme_asistencia.profesional, fme_asistencia.feccanasis, fme_asistencia.horacanasis, fme_asistencia.motivo,
									  fme_asistencia.cuenta, fme_asistencia.idafiliado
									  FROM fme_asistencia, persona, fme_adhsrv
									  WHERE persona.id_persona='$id_persona'
									  AND persona.numdoc = fme_asistencia.numdoc
									  AND fme_adhsrv.codigo=021 ");
			
			if(!$asistencias)
				$estado='0';
			
			
			

			echo $GLOBALS['twig']->render('/Atenciones/perfil_particular.html', compact('particular',
																  'asistencias',
																  'estado','use'));	
			
			
		}
	
	
//funcion mostrarFormulario, que debe mostrar el formulario con los datos del asociado seleccionado
//se debe pasar por parametro el la variable 

function mostrarFormulario()
{$use=$_SESSION['usuario'];
	if(!isset($_GET['num_soc']))
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se recibió num_soc como parametro de la función"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	
	if(!isset($_GET['cod_servicio']))
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se recibió cod_servicio como parametro de la función"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	
	
	$numero_socio = $_GET['num_soc'];
	$cod_servicio = $_GET['cod_servicio'];

	$resultado = $GLOBALS['db']->select("SELECT * FROM socios, persona
										WHERE socios.numero_soc = '$numero_socio'
										AND socios.id_persona=persona.id_persona");

	$id_persona;
	if($resultado)
	{
		date_default_timezone_set('America/Argentina/Catamarca');
		$fecha['year']=date("Y");
		$fecha['mon']=date("m");
		$fecha['mday']=date("d");
		$fecha['hours']=date("H");
		$fecha['minutes']=date("i");
		foreach($resultado as $res)
		{
			$persona =[
					'nro'		=>	$res['numero_soc'],
					'sexo'		=>	$res['sexo'],
					'nombre'	=>	$res['nombre'],
					'doc' 		=>	$res['numdoc'],
					'tel' 		=>	$res['tel_fijo'],
					'cel'		=>	$res['tel_cel'],
					'fecha' 	=>	$fecha,
					'dom'		=>	$res['domicilio'],
					'nro_casa'		=>	$res['casa_nro'],
					'barrio'		=>	$res['barrio'],
					'localidad'		=>	$res['localidad'],
					'cod_postal'		=>	$res['codpostal'],
					'dpmto'		=>	$res['dpmto'],
					'cod_serv'	=>	$cod_servicio,
					'id_persona' => $res['id_persona']
					];
			if($res['numero_soc']==$res['soc_titula'])
			{
				$persona['doctit'] = $res['numdoc'];
			}
			else
			{
				$soc_titular=$res['soc_titula'];
				$resultado2 = $GLOBALS['db']->select("SELECT * FROM socios, persona
										WHERE socios.numero_soc='$soc_titular'
										AND persona.id_persona=socios.id_persona");
				foreach($resultado2 as $res2)
				{
					$persona['doctit'] = $res2['numdoc'];
				}
			}
			$id_persona=$res['id_persona'];
		}	
	}
	else
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se encontraron datos para el socio '$numero_socio'"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	
	$resultado_historia = $GLOBALS['db']->select("SELECT * FROM historia_clinica
										WHERE id_persona='$id_persona'");
	if($resultado_historia){
		foreach($resultado_historia as $res_historia){
			$historia =[
					'paperas'		=>	$res_historia['paperas'],
					'rubeola'		=>	$res_historia['rubeola'],
					'varicela'	=>	$res_historia['varicela'],
					'epilepsia' 		=>	$res_historia['epilepsia'],
					'hepatitis' 		=>	$res_historia['hepatitis'],
					'sinusitis'		=>	$res_historia['sinusitis'],
					'diabetes' 	=>	$res_historia['diabetes'],
					'apendicitis'		=>	$res_historia['apendicitis'],
					'amigdalitis'		=>	$res_historia['amigdalitis'],
					'comidas'		=>	$res_historia['comidas'],
					'medicamentos'		=>	$res_historia['medicamentos'],
					'otras'		=>	$res_historia['otras'],
					];
					
		}
	}
	else{
		$historia =[
				'paperas'		=>	0,
				'rubeola'		=>	0,
				'varicela'	=>	0,
				'epilepsia' 		=>	0,
				'hepatitis' 		=>	0,
				'sinusitis'		=>	0,
				'diabetes' 	=>	0,
				'apendicitis'		=>	0,
				'amigdalitis'		=>	0,
				'comidas'		=>	'',
				'medicamentos'		=>	'',
				'otras'		=>	'',
				];
	}
	

	echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_formulario.html', compact('persona','use','historia'));
}


function mostrarFormularioParticular()
{$use=$_SESSION['usuario'];
	if(!isset($_GET['id_persona']))
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se recibió id_persona como parametro de la función"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	
	
	$id_persona = $_GET['id_persona'];

	$resultado = $GLOBALS['db']->select("SELECT * FROM persona, fme_adhsrv
										WHERE persona.id_persona = '$id_persona'
										AND fme_adhsrv.codigo=021");

	if($resultado)
	{
		date_default_timezone_set('America/Argentina/Catamarca');
		$fecha['year']=date("Y");
		$fecha['mon']=date("m");
		$fecha['mday']=date("d");
		$fecha['hours']=date("H");
		$fecha['minutes']=date("i");
		foreach($resultado as $res)
		{
			$persona =[
					'sexo'		=>	$res['sexo'],
					'nombre'	=>	$res['nombre'],
					'doc' 		=>	$res['numdoc'],
					'tel' 		=>	$res['tel_fijo'],
					'cel'		=>	$res['tel_cel'],
					'fecha' 	=>	$fecha,
					'dom'		=>	$res['domicilio'],
					'nro_casa'		=>	$res['casa_nro'],
					'barrio'		=>	$res['barrio'],
					'localidad'		=>	$res['localidad'],
					'cod_postal'		=>	$res['codpostal'],
					'dpmto'		=>	$res['dpmto'],
					'id_persona' => $res['id_persona']
					];
			$id_persona=$res['id_persona'];
		}	
	}
	else
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se encontraron datos para el particular '$id_persona'"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
		return;
	}
	
	$resultado_historia = $GLOBALS['db']->select("SELECT * FROM historia_clinica
										WHERE id_persona='$id_persona'");
	if($resultado_historia){
		foreach($resultado_historia as $res_historia){
			$historia =[
					'paperas'		=>	$res_historia['paperas'],
					'rubeola'		=>	$res_historia['rubeola'],
					'varicela'	=>	$res_historia['varicela'],
					'epilepsia' 		=>	$res_historia['epilepsia'],
					'hepatitis' 		=>	$res_historia['hepatitis'],
					'sinusitis'		=>	$res_historia['sinusitis'],
					'diabetes' 	=>	$res_historia['diabetes'],
					'apendicitis'		=>	$res_historia['apendicitis'],
					'amigdalitis'		=>	$res_historia['amigdalitis'],
					'comidas'		=>	$res_historia['comidas'],
					'medicamentos'		=>	$res_historia['medicamentos'],
					'otras'		=>	$res_historia['otras'],
					];
					
		}
	}
	else{
		$historia =[
				'paperas'		=>	0,
				'rubeola'		=>	0,
				'varicela'	=>	0,
				'epilepsia' 		=>	0,
				'hepatitis' 		=>	0,
				'sinusitis'		=>	0,
				'diabetes' 	=>	0,
				'apendicitis'		=>	0,
				'amigdalitis'		=>	0,
				'comidas'		=>	'',
				'medicamentos'		=>	'',
				'otras'		=>	'',
				];
	}
	
	

	echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_formulario_particular.html', compact('persona','use','historia'));
}
	
	
//funcion generarAtencion, que se ejecuta tras completar el formulario
function generarAtencion()
{   $use=$_SESSION['usuario'];

		//DISTINGUE ENTRE ATENCION A DOMICILIO O ATENCION EN CONSULTORIO
		if(isset($_POST['atencion_domicilio'])){
			$dom=$_POST['dom'];
			$nrocasa=$_POST['nrocasa'];
			$barrio=$_POST['barrio'];
			$localidad=$_POST['localidad'];
			$cod_postal=$_POST['codpostal'];
			$dpto=$_POST['dpto'];
		}
		else{
			$dom='EN CONSULTORIO';
			$nrocasa='';
			$barrio='';
			$localidad='';
			$cod_postal='';
			$dpto='';
		}

	if(isset($_POST['doctit']) && isset($_POST['nro'])){
		
		$cod_ser=$_POST['cod_serv'];
		$doctitu=$_POST['doctit'];
		$numdoc=$_POST['doc'];
		$nombre=$_POST['nombre'];
		$fec_pedido=$_POST['fecha'];
		$hora_pedido=$_POST['hora'];
		$dessit=$_POST['desc'];
		$profesional=$_POST['prof'];
		$sexo=$_POST['sexo'];
		$tel=$_POST['tel'];

		$nro=$_POST['nro'];		//nro es el numero de asociado
		
		$resultado=$GLOBALS['db']->query("INSERT INTO fme_asistencia (cod_ser,doctitu,numdoc,nombre,fec_pedido,hora_pedido,dessit,profesional)
				VALUES ('$cod_ser','$doctitu','$numdoc','$nombre','$fec_pedido','$hora_pedido','$dessit','$profesional')");

		if(!$resultado)
		{
			$error=[
					'menu'			=>"Atenciones",
					'funcion'		=>"generarAtencion",
					'descripcion'	=>"No se pudo realizar la consulta: INSERT INTO fme_asistencia (doctitu,numdoc,nombre,fec_pedido,hora_pedido,dessit,profesional)
				VALUES ('$doctitu','$numdoc','$nombre','$fec_pedido','$hora_pedido','$dessit','$profesional')"
					];
			echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
			return;
		}

		$persona=[
			'cod_serv'	=>	$cod_ser,
			'fecha'		=>	$fec_pedido,
			'hora'		=>	$hora_pedido,
			'nro'		=>	$nro,
			'nombre'	=>	$nombre,
			'sexo'		=>	$sexo,
			'tel'		=>	$tel,
			'doc'		=>	$numdoc,
			'doctit'	=>	$doctitu,
			'dom'		=>	$dom,
			'nro_casa'		=>	$nrocasa,
			'barrio'		=>	$barrio,
			'localidad'		=>	$localidad,
			'cod_postal'	=>	$cod_postal,
			'dpmto'			=>	$dpto,
			'prof'		=>	$profesional,
			'desc'		=>	$dessit
		];
		
		
		//HISTORIA CLINICA
		if(isset($_POST['paperas'])){
			$paperas=1;
		}
		else{
			$paperas=0;
		}
		if(isset($_POST['rubeola'])){
			$rubeola=1;
		}
		else{
			$rubeola=0;
		}
		if(isset($_POST['varicela'])){
			$varicela=1;
		}
		else{
			$varicela=0;
		}
		if(isset($_POST['epilepsia'])){
			$epilepsia=1;
		}
		else{
			$epilepsia=0;
		}
		if(isset($_POST['hepatitis'])){
			$hepatitis=1;
		}
		else{
			$hepatitis=0;
		}
		if(isset($_POST['sinusitis'])){
			$sinusitis=1;
		}
		else{
			$sinusitis=0;
		}
		if(isset($_POST['diabetes'])){
			$diabetes=1;
		}
		else{
			$diabetes=0;
		}
		if(isset($_POST['apendicitis'])){
			$apendicitis=1;
		}
		else{
			$apendicitis=0;
		}
		if(isset($_POST['amigdalitis'])){
			$amigdalitis=1;
		}
		else{
			$amigdalitis=0;
		}
		
		$comidas=$_POST['comidas'];
		$medicamentos=$_POST['medicamentos'];
		$otras=$_POST['otras'];
		$id_persona=$_POST['id_persona'];
		
		$resultado = $GLOBALS['db']->select("SELECT * FROM historia_clinica
										WHERE id_persona = '$id_persona' ");

		if($resultado)
		{
			$res=$GLOBALS['db']->query("UPDATE historia_clinica SET 
			paperas='$paperas',rubeola='$rubeola',varicela='$varicela',epilepsia='$epilepsia',
			hepatitis='$hepatitis',sinusitis='$sinusitis',diabetes='$diabetes',
			apendicitis='$apendicitis',amigdalitis='$amigdalitis',comidas='$comidas',
			medicamentos='$medicamentos',otras='$otras'
			WHERE id_persona='$id_persona'");
		}
		else{
			$res=$GLOBALS['db']->query("INSERT INTO historia_clinica (id_persona,paperas,rubeola,varicela,epilepsia,
			hepatitis,sinusitis,diabetes,apendicitis,amigdalitis,comidas,medicamentos,otras)
					VALUES ('$id_persona','$paperas','$rubeola','$varicela','$epilepsia',
					'$hepatitis','$sinusitis','$diabetes','$apendicitis','$amigdalitis',
					'$comidas','$medicamentos','$otras')");
		}

			echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_finalizar.html', compact('persona','use'));
	}
	
	
	//SI NO SE SETEA DOCTIT, ES PORQUE ES UN PARTICULAR ENTONCES:
	else{
				
		$cod_ser=$_POST['cod_serv'];
		$doctitu='';
		$numdoc=$_POST['doc'];
		$nombre=$_POST['nombre'];
		$fec_pedido=$_POST['fecha'];
		$hora_pedido=$_POST['hora'];
		$dessit=$_POST['desc'];
		$profesional=$_POST['prof'];
		$sexo=$_POST['sexo'];
		$tel=$_POST['tel'];
		$nro='';		//nro es el numero de asociado
		
		$resultado=$GLOBALS['db']->query("INSERT INTO fme_asistencia (cod_ser,doctitu,numdoc,nombre,fec_pedido,hora_pedido,dessit,profesional)
				VALUES ('$cod_ser','$doctitu','$numdoc','$nombre','$fec_pedido','$hora_pedido','$dessit','$profesional')");

		if(!$resultado)
		{
			$error=[
					'menu'			=>"Atenciones",
					'funcion'		=>"generarAtencion",
					'descripcion'	=>"No se pudo realizar la consulta: INSERT INTO fme_asistencia (doctitu,numdoc,nombre,fec_pedido,hora_pedido,dessit,profesional)
				VALUES ('$doctitu','$numdoc','$nombre','$fec_pedido','$hora_pedido','$dessit','$profesional')"
					];
			echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error','use'));	
			return;
		}

		$persona=[
			'cod_serv'	=>	$cod_ser,
			'fecha'		=>	$fec_pedido,
			'hora'		=>	$hora_pedido,
			'nro'		=>	$nro,
			'nombre'	=>	$nombre,
			'sexo'		=>	$sexo,
			'tel'		=>	$tel,
			'doc'		=>	$numdoc,
			'doctit'	=>	$doctitu,
			'dom'		=>	$dom,
			'nro_casa'		=>	$nrocasa,
			'barrio'		=>	$barrio,
			'localidad'		=>	$localidad,
			'cod_postal'	=>	$cod_postal,
			'dpmto'			=>	$dpto,
			'prof'		=>	$profesional,
			'desc'		=>	$dessit
		];
		
		//HISTORIA CLINICA
		if(isset($_POST['paperas'])){
			$paperas=1;
		}
		else{
			$paperas=0;
		}
		if(isset($_POST['rubeola'])){
			$rubeola=1;
		}
		else{
			$rubeola=0;
		}
		if(isset($_POST['varicela'])){
			$varicela=1;
		}
		else{
			$varicela=0;
		}
		if(isset($_POST['epilepsia'])){
			$epilepsia=1;
		}
		else{
			$epilepsia=0;
		}
		if(isset($_POST['hepatitis'])){
			$hepatitis=1;
		}
		else{
			$hepatitis=0;
		}
		if(isset($_POST['sinusitis'])){
			$sinusitis=1;
		}
		else{
			$sinusitis=0;
		}
		if(isset($_POST['diabetes'])){
			$diabetes=1;
		}
		else{
			$diabetes=0;
		}
		if(isset($_POST['apendicitis'])){
			$apendicitis=1;
		}
		else{
			$apendicitis=0;
		}
		if(isset($_POST['amigdalitis'])){
			$amigdalitis=1;
		}
		else{
			$amigdalitis=0;
		}
		
		$comidas=$_POST['comidas'];
		$medicamentos=$_POST['medicamentos'];
		$otras=$_POST['otras'];
		$id_persona=$_POST['id_persona'];
		
		$resultado = $GLOBALS['db']->select("SELECT * FROM historia_clinica
										WHERE id_persona = '$id_persona' ");

		if($resultado)
		{
			$res=$GLOBALS['db']->query("UPDATE historia_clinica SET 
			paperas='$paperas',rubeola='$rubeola',varicela='$varicela',epilepsia='$epilepsia',
			hepatitis='$hepatitis',sinusitis='$sinusitis',diabetes='$diabetes',
			apendicitis='$apendicitis',amigdalitis='$amigdalitis',comidas='$comidas',
			medicamentos='$medicamentos',otras='$otras'
			WHERE id_persona='$id_persona'");
		}
		else{
			$res=$GLOBALS['db']->query("INSERT INTO historia_clinica (id_persona,paperas,rubeola,varicela,epilepsia,
			hepatitis,sinusitis,diabetes,apendicitis,amigdalitis,comidas,medicamentos,otras)
					VALUES ('$id_persona','$paperas','$rubeola','$varicela','$epilepsia',
					'$hepatitis','$sinusitis','$diabetes','$apendicitis','$amigdalitis',
					'$comidas','$medicamentos','$otras')");
		}

		echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_finalizar.html', compact('persona','use'));
		
	}

	
}

function generarPDF()
	{
		$cod_ser=$_POST['cod_serv'];
		$doctitu=$_POST['doctit'];	
		$numdoc=$_POST['doc'];		
		$nombre=$_POST['nombre'];	
		$fec_pedido=$_POST['fecha'];
		$hora_pedido=$_POST['hora'];
		$dessit=$_POST['desc'];
		$profesional=$_POST['prof'];
		$sexo=$_POST['sexo'];		
		$tel=$_POST['tel'];
		$dom=$_POST['dom'];
		$nrocasa=$_POST['nrocasa'];
		$barrio=$_POST['barrio'];
		$localidad=$_POST['localidad'];
		$cod_postal=$_POST['codpostal'];
		$dpto=$_POST['dpto'];
		$nro=$_POST['nro'];
	
	//---------------Generar PDF -------------------------------
	
	require('../../fpdf/fpdf.php');
	
	$pdf=new FPDF('P', 'pt', 'A4');
	$pdf->AddPage();
	
	$pdf->AddFont('Century','','CENTURY.php');

	//Texto de Título
	$pdf->SetXY(70, 100);
	$pdf->AddFont('Roboto','','Roboto-Regular.php');
	$pdf->SetFont('Roboto','',22);
	$pdf->Cell(0,20,'SOLICITUD DE ASISTENCIA','0','0','');

	$pdf->SetFont('Roboto','',14);

	$pdf->SetXY(70,130);
	$pdf->Cell(15, 8, utf8_decode('Fecha: '.$fec_pedido), 0, '');

	$pdf->SetXY(70,150);
	$pdf->Cell(15, 8, utf8_decode('Hora: '.$hora_pedido), 0, '');	
	
	//De aqui en adelante se colocan distintos métodos
	//para diseñar el formato.
	 
	
	$pdf->SetFont('Century','',12);

	//$pdf->SetFont('Century','', 12);
	 
	$pdf->SetXY(70,220);
	$pdf->Cell(10, 8, utf8_decode('Nombre del paciente: '.$nombre), 0, 'L');

	$pdf->SetXY(70,240);
	$pdf->Cell(19, 8, utf8_decode('D.N.I. del paciente: '.$numdoc), 0, 'L');

	$pdf->SetXY(70,260);
	$pdf->Cell(20, 8, utf8_decode('D.N.I. del titular: '.$doctitu), 0, 'L');

	$pdf->SetXY(70,280);
	$pdf->Cell(10, 8, utf8_decode('Sexo del paciente: '.$sexo), 0, 'L');

	$pdf->SetXY(70,300);
	$pdf->Cell(10, 8, utf8_decode($tel), 0, 'L');
	
	$pdf->SetXY(70,340);
	$pdf->Cell(10, 8, utf8_decode('Domicilio: '.$dom), 0, 'L');
	
	$pdf->SetXY(70,360);
	$pdf->Cell(10, 8, utf8_decode('Nº Casa: '.$nrocasa), 0, 'L');
	
	$pdf->SetXY(70,380);
	$pdf->Cell(10, 8, utf8_decode('Barrio: '.$barrio), 0, 'L');
	
	$pdf->SetXY(70,400);
	$pdf->Cell(10, 8, utf8_decode('Localidad: '.$localidad), 0, 'L');
	
	$pdf->SetXY(70,420);
	$pdf->Cell(10, 8, utf8_decode('Codigo postal: '.$cod_postal), 0, 'L');
	
	$pdf->SetXY(70,440);
	$pdf->Cell(10, 8, utf8_decode('Dpto: '.$dpto), 0, 'L');
	
	$pdf->SetXY(70,480);
	$pdf->Cell(10, 8, utf8_decode('Profesional: '.$profesional), 0, 'L');
	
	$pdf->SetXY(70,500);
	$pdf->Cell(10, 8, utf8_decode('Situacion: '.$dessit), 0, 'L');

	$pdf->Line(70,320,525,320);
	$pdf->Line(70,460,525,460);

	$pdf->Image('../../static/images/logo_mutual.png','425','100','100','100','PNG');	
	$pdf->Image('../../static/images/back.png','0','0','595','841','PNG');	

	$pdf->Output(); //Salida al navegador	
}




	
//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
?>