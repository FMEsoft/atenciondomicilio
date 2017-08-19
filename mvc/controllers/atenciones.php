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
			

			
			//NOTA: Para ver si funciona tienen que asociarle un adherente en la tabla socios, ya que en los datos de ejemplo todos son titulares
			//NOTA: Lo que hice fue: en tabla socios en numero_soc=00044 cambiar el campo soc_titula de manera que quede soc_titula=00277
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DEL ASOCIADO TITULAR----------------
			
			$numero_socio = $_GET['num_soc']; //Es el número del socio titular que debe ser tomado del PASO 1
			
			$resultadoTitular = $GLOBALS['db']->select("SELECT * FROM socios, persona 
									  WHERE soc_titula = '$numero_socio' 
									  AND socios.id_persona = persona.id_persona
									  AND numero_soc = soc_titula");
			
			if(!$resultadoTitular)
			echo "Ocurrió un error en la consulta de la info del asociado titular";
			
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS SERVICIOS DEL ASOCIADO TITULAR----------------
			
			//Por cuota
			$resultadoTitularServicios1 = $GLOBALS['db']->select("SELECT * FROM fme_adhsrv, tar_srv,socios 
									   WHERE socnumero = '$numero_socio' 
									   AND fme_adhsrv.codigo = tar_srv.idmutual
									   AND soc_titula = '$numero_socio'
									   AND numero_soc = soc_titula");
									   
			if(!$resultadoTitularServicios1)
			echo "Ocurrió un error en la consulta de los servicios del asociado titular en aportes por cuota";
			
			//Por tarjeta
			$resultadoTitularServicios2 = $GLOBALS['db']->select("SELECT * FROM tar_srvadherentes, tar_srv, socios 
										WHERE socnumero = '$numero_socio' 
										AND tar_srvadherentes.codigo = tar_srv.codigo
										AND soc_titula = '$numero_socio'
										AND numero_soc = soc_titula");
			
			if(!$resultadoTitularServicios2)
			echo "Ocurrió un error en la consulta de los servicios del asociado titular en aportes por tarjeta";
			
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS ADHERENTES DEL ASOCIADO TITULAR CON APORTES POR CUOTA----------------
			
			$resultadoAdherentes1 = $GLOBALS['db']->select("SELECT * FROM socios, fme_adhsrv, tar_srv
									   WHERE soc_titula = '$numero_socio' 
									   AND socios.numero_soc <> '$numero_socio' 
									   AND socios.numero_soc = fme_adhsrv.socnumero
									   AND fme_adhsrv.codigo = tar_srv.idmutual");
			
			if(!$resultadoAdherentes1)
			echo "Ocurrió un error en la consulta de los servicios del ADHERENTE en aportes por cuota";

			//Esta consulta solo recoge los nombres
			$resultadoAdherentes1Nombres = $GLOBALS['db']->select("SELECT * FROM socios, fme_adhsrv
									   WHERE soc_titula = '$numero_socio' 
									   AND socios.numero_soc <> '$numero_socio' 
									   AND socios.numero_soc = fme_adhsrv.socnumero");
			
			if(!$resultadoAdherentes1Nombres)
			echo "Ocurrió un error en la consulta del NOMBRE del ADHERENTE en aportes por cuota";
		


			if(is_array($resultadoAdherentes1Nombres)){
				$i=0;
				foreach($resultadoAdherentes1Nombres as $res1)
				{
					$resultadoAdherentes1[$i]['nombrePersona']=$res1['nombre'];
					$i=$i+1;
				}	
			}

			
			
			//---------------CONSULTA QUE DEVUELVE TODA LA INFO DE LOS ADHERENTES DEL ASOCIADO TITULAR CON APORTES POR TARJETA----------------
			
			$resultadoAdherentes2 = $GLOBALS['db']->select("SELECT * FROM socios, tar_srvadherentes, tar_srv 
									   WHERE socios.soc_titula = '$numero_socio' 
									   AND numero_soc <> '$numero_socio' 
									   AND socios.numero_soc = tar_srvadherentes.socnumero
									   AND tar_srvadherentes.codigo = tar_srv.codigo");
			
			if(!$resultadoAdherentes2)
			echo "Ocurrió un error en la consulta de los servicios del ADHERENTE en aportes por tarjeta";

			//Esta consulta solo recoge los nombres
			$resultadoAdherentes2Nombres = $GLOBALS['db']->select("SELECT * FROM socios, tar_srvadherentes 
									   WHERE socios.soc_titula = '$numero_socio' 
									   AND numero_soc <> '$numero_socio' 
									   AND socios.numero_soc = tar_srvadherentes.socnumero");
			
			if(!$resultadoAdherentes2Nombres)
			echo "Ocurrió un error en la consulta del NOMBRE del ADHERENTE en aportes por tarjeta";
			
			
			
			if(is_array($resultadoAdherentes2Nombres)){
				$i=0;
				foreach($resultadoAdherentes2Nombres as $res2)
				{
					$resultadoAdherentes2[$i]['nombrePersona']=$res2['nombre'];
					$i=$i+1;
				}
			}
		
		    
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
			echo "Ocurrió un error en la consulta de las asistencias";
			
			
			

			echo $GLOBALS['twig']->render('/Atenciones/perfil.html', compact('resultadoTitular', 
																  'resultadoTitularServicios1', 
																  'resultadoTitularServicios2', 
																  'resultadoAdherentes1',
																  'resultadoAdherentes1Nombres',
																  'resultadoAdherentes2',
																  'resultadoAdherentes2Nombres',
																  'asistencias'));	
			
			
		}
	
	
//funcion mostrarFormulario, que debe mostrar el formulario con los datos del asociado seleccionado
//se debe pasar por parametro el la variable 

function mostrarFormulario()
{
	if(!isset($_GET['num_soc']))
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se recibió num_soc como parametro de la función"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
		return;
	}
	
	if(!isset($_GET['cod_servicio']))
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se recibió cod_servicio como parametro de la función"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
		return;
	}
	
	
	$numero_socio = $_GET['num_soc'];
	$cod_servicio = $_GET['cod_servicio'];

	$resultado = $GLOBALS['db']->select("SELECT * FROM socios, persona
										WHERE socios.numero_soc = '$numero_socio'
										AND socios.id_persona=persona.id_persona");

	if($resultado)
	{
		$fecha=getdate();
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
					'cod_serv'	=>	$cod_servicio
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
				
		}	
	}
	else
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"mostrarFormulario",
				'descripcion'	=>"No se encontraron datos para el socio '$numero_socio'"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
		return;
	}
	
	

	echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_formulario.html', compact('persona'));
}
	
	
//funcion generarAtencion, que se ejecuta tras completar el formulario
function generarAtencion()
{
	//variables q no conozco	
	$nroordate;
	$cuenta;
	$idafiliado;
	
	
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
	$nro=$_POST['nro'];		//nro es el numero de asociado
	
	$resultado=$GLOBALS['db']->query("INSERT INTO fme_asistencia (cod_ser,doctitu,numdoc,nombre,fec_pedido,hora_pedido,dessit,profesional)
			VALUES ('$cod_ser','$doctitu','$numdoc','$nombre','$fec_pedido','$hora_pedido','$dessit','$profesional')");

	if(!$resultado)
	{
		$error=[
				'menu'			=>"Atenciones",
				'funcion'		=>"generarAtencion",
				'descripcion'	=>"No se pudo insertar la consulta: INSERT INTO fme_asistencia (doctitu,numdoc,nombre,fec_pedido,hora_pedido,dessit,profesional)
			VALUES ('$doctitu','$numdoc','$nombre','$fec_pedido','$hora_pedido','$dessit','$profesional')"
				];
		echo $GLOBALS['twig']->render('/Atenciones/error.html', compact('error'));	
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

	echo $GLOBALS['twig']->render('/Atenciones/nueva_atencion_finalizar.html', compact('persona'));
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