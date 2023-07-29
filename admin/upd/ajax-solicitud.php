<?php 

require_once "../modelos/Solicitud.php";

if (strlen(session_id())<1) 

	session_start();



$solicitud=new Solicitud(); 





$idsolicitud=isset($_POST["idsolicitud"])? limpiarCadena($_POST["idsolicitud"]):"";

$iddestino=isset($_POST["iddestino"])? limpiarCadena($_POST["iddestino"]):"";

$idusuario=$_SESSION["idusuario"];

$fecha_hora=isset($_POST["fecha_hora"])? limpiarCadena($_POST["fecha_hora"]):"";
$cambioestado=isset($_POST["index"])? limpiarCadena($_POST["index"]):"";





switch ($_GET["op"]) {

	case 'guardaryeditar':

	if (empty($idingreso)) {	

		$rspta = $solicitud->ultimo_ticket();						

		$numcon=$rspta->fetch_object();	

		$rspta=$solicitud->insertar($iddestino,$idusuario,$fecha_hora,$numcon->serie,$_POST["idarticulo"],$_POST["cantidad"]);

		$solicitud->actualiza_ticket();

		echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";

	}

	break;


case 'flujo':

		$rspta=$solicitud->cambioEstado($cambioestado,$idsolicitud);

		echo $rspta ? "Solicitud ha cambiado correctamente " : "No se pudo hacer el cambio";

	break;

case 'flujoDetalle':
  		$comentario=$_POST['comentario'];
		$rspta=$solicitud->cambioEstadoDetalle($cambioestado,$idsolicitud,$idusuario,$comentario);		

		echo $rspta ? "Solicitud ha cambiado correctamente " : "No se pudo hacer el cambio";

	break;	
	

	case 'entregar':

		$rspta=$solicitud->entregar($idsolicitud);

		echo $rspta ? "Solicitud entregado correctamente" : "No se pudo hacer la entrega";

	break;





	case 'mostrar':

		$id=$_GET['idsol'];

		$rspta=$solicitud->mostrar($id);

		echo json_encode($rspta);

		break;

	case 'listarDetalle':

		//recibimos el idingreso
		$id=$_GET['idsol'];		

		$rspta=$solicitud->listarDetalle($id);		

		echo ' <thead style="background-color:#A9D0F5">                

        <th>Material</th>

        <th>Cantidad</th>

        <th>Estado</th>

       </thead>';

       require_once "../modelos/Departamento.php";
        $departamentos=new Departamento(); 

		while ($reg=$rspta->fetch_assoc()) {			
		$status='';
		$rsdepartamento = $departamentos->regresaRolDepartamento($_SESSION['departamento'] );
	    $rgdepartamento=$rsdepartamento->fetch_object();        	        

 


			if($reg['estado']=='Pendiente' && ($rgdepartamento->nombre=='EMBARQUES' || $rgdepartamento->nombre=='ADMIN' ) ) {
				$status='<button class="btn btn-warning btn-xs" onclick="cambioestadodetalle(1,'.$id.','.$reg['iddetalle_solicitud'].')"><i class="fa fa-exclamation-triangle"></i> Pendiente de Enviar</button>';
				
			}
			else{
				if($reg['estado']=='Enviado' && ( $rgdepartamento->nombre=='RECIBO' || $rgdepartamento->nombre=='ADMIN' ) ) {
					$status='<button class="btn btn-primary btn-xs" onclick="cambioestadodetalle(2,'.$id.','.$reg['iddetalle_solicitud'].')"><i class="fa fa-check"></i> Enviado(En transito a planta Valeo)</button>';
				
				}
				else{
					if($reg['estado']=='Recibido' && ( $rgdepartamento->nombre=='HABILITACION'  || $rgdepartamento->nombre=='ADMIN' ) ) {
						$status='<button class="btn btn-danger btn-xs" onclick="cambioestadodetalle(3,'.$id.','.$reg['iddetalle_solicitud'].')"><i class="fa fa-check"></i> Pendiente enviar a Linea</button>';
				
					}
					else{
						if($reg['estado']=='Entregado'){
							$status='<label class="btn-success btn-xs" ><i class="fa fa-check"></i> Completado</label>';
				
						}

						else{ 



						}
					}
				}
			}			




		/*if ($reg['estado']=='Pendiente' && $rgdepartamento->nombre=='HABILITACION'  ) {

						$status='<button class="btn btn-warning btn-xs" onclick="cambioestadodetalle(1,'.$id.','.$reg['iddetalle_solicitud'].')"><i class="fa fa-exclamation-triangle"></i> Pendiente de Enviar</button>'; 

					}else{

						if ($reg['estado']=='Entregado'){

							$status='<button class="btn btn-success btn-xs"><i class="fa fa-check"></i> Entregado</button>';

							}
						else{

								$status='<button class="btn btn-warning btn-xs" ><i class="fa fa-exclamation-triangle"></i> Pendiente de Enviar</button>'; 
							}

					}			
*/

			echo '<tr class="filas">			

			<td>'.$reg['nombre'].'</td>			

			<td>'.$reg['cantidad'].'</td>

			<td>'.$status.'</td>						

			</tr>';				

		}



		break;

 

    case 'listar':


    	if (isset($_GET['sel'])){  $rspta=$solicitud->listarop($_GET['sel']); }
    	else {  $rspta=$solicitud->listar();   }		

		$data=Array();

		require_once "../modelos/Departamento.php";
        $departamentos=new Departamento(); 
        
		while ($reg=$rspta->fetch_object()) { 


//			($reg->estado=='Pendiente de Enviar')?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>' : '<button class="btn btn-success btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>',

			$rsdepartamento = $departamentos->regresaRolDepartamento($_SESSION['departamento'] );
	        $rgdepartamento=$rsdepartamento->fetch_object();        	        

/*			if($reg->estado=='Pendiente' && $rgdepartamento->nombre=='EMBARQUES'){				
				$estado='<button class="btn btn-warning btn-xs" onclick="cambioestado(1,'.$reg->idsolicitud.')"><i class="fa fa-exclamation-triangle"></i> Pendiente de Enviar</button>';
				$view='<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
			}
			else{
				if($reg->estado=='Enviado' && $rgdepartamento->nombre=='RECIBO' ){
					$estado='<button class="btn btn-primary btn-xs" onclick="cambioestado(2,'.$reg->idsolicitud.')"><i class="fa fa-check"></i> Enviado(En transito a planta Valeo)</button>';
					$view='<button class="btn btn-primary btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
				}
				else{
					if($reg->estado=='Recibido' && $rgdepartamento->nombre=='HABILITACION' ){
						$estado='<button class="btn btn-danger btn-xs" ><i class="fa fa-check"></i> Pendiente enviar a Linea</button>';
						$view='<button class="btn btn-danger btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
					}
					else{
						if($reg->estado=='Entregado'){
							$estado='<label class="btn-success btn-xs" ><i class="fa fa-check"></i> Completado</label>';
							$view='<button class="btn btn-success btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
						}

						else{  */
 
							if($reg->estado=='Pendiente'){				
								$estado='<button class="btn btn-warning btn-xs" ><i class="fa fa-exclamation-triangle"></i> Pendiente de Enviar</button>';
								$view='<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
								}
							if($reg->estado=='Enviado'){
								$estado='<button class="btn btn-primary btn-xs" ><i class="fa fa-check"></i> Enviado(En transito a planta Valeo)</button>';
								$view='<button class="btn btn-primary btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
								}
							if($reg->estado=='Recibido'){
								$estado='<button class="btn btn-danger btn-xs" ><i class="fa fa-check"></i> Pendiente enviar a Linea</button>';
								$view='<button class="btn btn-danger btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
								}
							if($reg->estado=='Entregado'){
								$estado='<label class="btn-success btn-xs" ><i class="fa fa-check"></i> Completado</label>';
								$view='<button class="btn btn-success btn-xs" onclick="mostrar('.$reg->idsolicitud.')"><i class="fa fa-eye"></i></button>';
								}
/*						}
					}
				}
			
			}

*/
			$rssolicitud=$solicitud->revisaStatus($reg->idsolicitud);
			$rgsol=$rssolicitud->fetch_object();

			if( $rgsol->cantidad == 0 ) { $rgcambioestado=$solicitud->cambioestado(3,$reg->idsolicitud); }
			
			
			$data[]=array(        	

			"0"=>$view,

            "1"=>$reg->num_comprobante,

            "2"=>$reg->fecha_hora,

            "3"=>$reg->iddestino,

            "4"=>$reg->idusuario,            

            "5"=>$estado

              );

		}

		$results=array(

             "sEcho"=>1,//info para datatables

             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable

             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar

             "aaData"=>$data); 

		echo json_encode($results);

		break;



		case 'selectDestino':

			require_once "../modelos/Destino.php";

			$persona = new Destino();



			$rspta = $persona->listar();

			echo "<option value='-1'> Seleccionar una categoria </option>";

			while ($reg = $rspta->fetch_object()) {

				echo '<option value='.$reg->iddestino.'>'.$reg->nombre. ' '. $reg->descripcion     .'</option>';

			}

			break; 



		case 'listarMateriales':

			require_once "../modelos/Material.php";

			$articulo=new Material();



				$rspta=$articulo->listarActivos();

		$data=Array();



		while ($reg=$rspta->fetch_object()) {

			$data[]=array(

            //"0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idmaterial. ',\' '.$reg->nombre.'\') "><span class="fa fa-plus"></span></button>',
			  "0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idmaterial. ',\''.$reg->nombre.'\' ,\''.$reg->bloque.'\' ,'.$reg->stock.') "><span class="fa fa-plus"></span></button>',
            //"0"=>'<button class="btn btn-warning" onclick=" agregarDetalle('.$reg->idmaterial. ',\' '.$reg->nombre. ',\' '.$reg->bloque. ',\' '.$reg->stock.'\') "><span class="fa fa-plus"></span></button>

            "1"=>$reg->nombre,

            "2"=>$reg->bloque,                        

            "3"=>$reg->stock,

              );

		}

		$results=array(

             "sEcho"=>1,//info para datatables

             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable

             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar

             "aaData"=>$data); 

		echo json_encode($results);



				break;

}

 ?>