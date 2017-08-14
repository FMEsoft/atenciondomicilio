<?php




//Si la variable funcion no tiene ningun valor, se redirecciona al inicio------------
	if(!isset($_GET['funcion']))
	{
		require_once '../../vendor/autoload.php';

		$loader = new Twig_Loader_Filesystem('../views');

		$twig = new Twig_Environment($loader, []);
		
		echo $twig->render('/Inicio/inicio.html');
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
	
//funcion nuevaAtencion (MODIFICAR BLANQUITO) debe mostrar todos los asociados titulares
function nuevaAtencion()
	{
		require_once '../../vendor/autoload.php';
		
		

		$loader = new Twig_Loader_Filesystem('../views');

		$twig = new Twig_Environment($loader, []);
		
		//---------------CONSULTA DE PRUEBA----------------
		
		//$resultado = $GLOBALS['db']->select('SELECT * FROM fme_asistencia WHERE doctitu = 04191748');
		/*if($resultado)
		{
				foreach($resultado as $res)
					$pers =[
					echo "IDNUM: " . $res['idnum'] . " " ;
					echo "CODSER: " . $res['cod_ser'] . " ";
					echo "NROORDATE: " . $res['nroordate'] . " ";
					echo "DOCTITU: " . $res['doctitu'] . " ";
					echo "NUMDOC: " . $res['numdoc'] . " ";
					echo "NOMBRE: " . $res['nombre'] . " ";
					];
		}
		else
			echo "algo va mal";
		
		//---------------FIN CONSULTA DE PRUEBA---------------

		echo $twig->render('/Atenciones/nueva_atencion_1.html',compact('pers'));	
		
	}*/

		$resultado = $GLOBALS['db']->select('SELECT socios.beneficio,socios.soc_titula,socios.numero_soc,persona.sexo,persona.nombre,persona.numdoc 
									FROM socios, persona 
									WHERE socios.soc_titula=socios.numero_soc 
									AND persona.id_persona=socios.id_persona;');

		if($resultado)
		{
			foreach($resultado as $res)
				{
					$asociado =[
						];				
				}
		}
		else
			echo "error al mostrar el listado de asociados";

	
		//---------------FIN CONSULTA DE PRUEBA---------------
		echo $twig->render('/Atenciones/nueva_atencion_1.html', compact('asociado','resultado'));
}
		
		
//funcion verMAS, el cual debe realizar la consulta del asociado seleccionado para mostrar toda su informacion
function verMas()	
	{
	require_once '../../vendor/autoload.php';
	

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);
	

	
	//NOTA: Para que el ejemplo funcione tienen que asociarle un adherente en la tabla socios, ya que en los datos de ejemplo todos son titulares
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
	
	
	

	echo $twig->render('/Atenciones/perfil.html', compact('resultadoTitular', 
														  'resultadoTitularServicios1', 
														  'resultadoTitularServicios2', 
														  'resultadoAdherentes1',
														  'resultadoAdherentes1Nombres',
														  'resultadoAdherentes2',
														  'resultadoAdherentes2Nombres'));	
	
	
}
	
	
//funcion mostrarFormulario, que debe mostrar el formulario con los datos del asociado seleccionado
//se debe pasar por parametro el la variable 

function mostrarFormulario()
	{
	require_once '../../vendor/autoload.php';
	

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);
	
	
	$numero_socio = $_GET['num_soc']
	$resultado = $GLOBALS['db']->select("SELECT * FROM socios, persona 
							  WHERE soc_titula = '$numero_socio' 
							  AND socios.id_persona = persona.id_persona");
	
	
	if($resultado)
	{
		$fecha=getdate();
            foreach($resultado as $res)
				$persona =[
					'fecha' 	=>	$fecha,
					'doc' 		=>	$res['documento'],
					'nombre'	=>	$res['nombre'],
					'dom'		=>	$res['domicilio'],
					'tel'		=>	$res['telefono']
					
				];
	}
	else
		echo "error";
	
	//---------------FIN CONSULTA DE PRUEBA---------------

	echo $twig->render('/Atenciones/nueva_atencion_formulario.html', compact('persona'));
}
	
	
//funcion generarAtencion, que se ejecuta tras completar el formulario
function generarAtencion()
	{
	require_once '../../vendor/autoload.php';
	

	$loader = new Twig_Loader_Filesystem('../views');

	$twig = new Twig_Environment($loader, []);
	
	
	//---------------Generar PDF -------------------------------
	
	require('../../fpdf/fpdf.php');
	
	$pdf=new FPDF('P', 'pt', 'A4');
	$pdf->AddPage();
	
	//Texto de Título
	$pdf->SetXY(60, 25);
	$pdf->MultiCell(65, 5, utf8_decode('Atencion domiciliaria'), 0, 'C');
	 
	
	//De aqui en adelante se colocan distintos métodos
	//para diseñar el formato.
	 
	//Fecha
	$pdf->SetFont('Arial','', 12);
	$pdf->SetXY(145,60);
	$pdf->Cell(15, 8, 'FECHA:', 0, 'L');
	$pdf->Line(163, 65.5, 185, 65.5);
	 
	//Nombre //Apellidos //DNI //TELEFONO
	$pdf->SetXY(25, 80);
	$pdf->Cell(20, 8, 'NOMBRE(S):', 0, 'L');
	$pdf->Line(52, 85.5, 120, 85.5);
	//*****
	$pdf->SetXY(25,100);
	$pdf->Cell(19, 8, 'APELLIDOS:', 0, 'L');
	$pdf->Line(52, 105.5, 180, 105.5);
	//*****
	$pdf->SetXY(25, 120);
	$pdf->Cell(10, 8, 'DNI:', 0, 'L');
	$pdf->Line(35, 125.5, 90, 125.5);
	//*****
	$pdf->SetXY(110, 120);
	$pdf->Cell(10, 8, utf8_decode('TELÉFONO:'), 0, 'L');
	$pdf->Line(135, 125.5, 170, 125.5);
	 
	//LICENCIATURA  //CARGO   //CÓDIGO POSTAL
	$pdf->SetXY(25, 140);
	$pdf->Cell(10, 8, 'LINCECIATURA EN:', 0, 'L');
	$pdf->Line(27, 154, 65, 154);
	//*****
	$pdf->SetXY(80, 140);
	$pdf->Cell(10, 8, 'CARGO:', 0, 'L');
	$pdf->Line(75, 154, 105, 154);
	//*****
	$pdf->SetXY(125, 140);
	$pdf->Cell(10, 8, utf8_decode('CÓDIGO POSTAL:'), 0, 'L');
	$pdf->Line(120, 154, 170, 154);
	 
	$pdf->Output(); //Salida al navegador
	
	
	
	$pdf->SetFont('Arial','B',15);
	$pdf->Cell(0,20,'Solicitud de asistencia','0','0','C');
	
	$pdf->Ln();	//salto de linea
	
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(10,150,'Fecha de solicitud: ','0','0','L');
	$pdf->Ln();
	
	$pdf->Cell(10,150,'Hora de solicitud: ','0','0','L');
	
	$pdf->Ln();
	
	$pdf->Cell(10,150,'Telefono de contacto: ','0','0','L');
	
	$pdf->Ln();
	
	$pdf->Cell(10,150,'Situacion: ','0','0','L');
	
	$pdf->Ln();
	
	$pdf->Cell(10,150,'Profesional:  ','0','0','L');
	
	$pdf->Ln();
	
	$pdf->Cell(10,150,'Domicilio: ','0','0','L');
	
	$pdf->Ln();
	
	$pdf->Cell(10,150,'Fecha de asistencia: ','0','0','L');
	
	$pdf->Ln();
	
	$pdf->Cell(10,150,'Hora de asistencia: ','0','0','L');
	
	
	$pdf->output('nombre_pdf.pdf','I');
	
	
	echo $twig->render('/Atenciones/new1.html');		
}




	
//llamada a la funcion con el parametro pasado por la url.	
	$_GET['funcion']();
//luego de que se ejecutó la funcion, se cierra la bd
	$db->cerrar_sesion();	
?>