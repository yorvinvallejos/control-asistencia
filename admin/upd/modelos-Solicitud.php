<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class solicitud{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar registro
public function insertar($iddestino,$idusuario,$fecha_hora,$numcon,$idarticulo,$cantidad){
	
	date_default_timezone_set('America/Mexico_City');
	$fechacreada=date('Y-m-d H:i:s');

	$sql="INSERT INTO solicitud (iddestino,idusuario,num_comprobante,fecha_hora,estado) VALUES ('$iddestino','$idusuario','$numcon','$fechacreada','Pendiente')";
	 
	 $idingresonew=ejecutarConsulta_retornarID($sql);
	 $num_elementos=0;
	 $sw=true; 


	 while ($num_elementos < count($idarticulo)) {


	 	$sql_detalle="INSERT INTO detalle_solicitud (idsolicitud,idmaterial,cantidad,estado) VALUES('$idingresonew','$idarticulo[$num_elementos]','$cantidad[$num_elementos]','Pendiente')";	 	
	 	
	 	ejecutarConsulta($sql_detalle) or $sw=false;

	 	$num_elementos=$num_elementos+1;
	 }
	 return $sw;
}

public function entregar($idsolicitud){
	$sql="UPDATE solicitud SET estado='Entregado' WHERE idsolicitud='$idsolicitud'";
	return ejecutarConsulta($sql);
}


public function cantidadEstado($solicitud){

	if( $solicitud == 0 ) { $estado='Pendiente'; }
	if( $solicitud == 1 ) { $estado='Enviado'; }
	if( $solicitud == 2 ) { $estado='Recibido'; }
	if( $solicitud == 3 ) { $estado='Entregado'; }	

	$sql="SELECT COUNT(*) as cantidad FROM solicitud WHERE estado='$estado'";
	return ejecutarConsulta($sql);
}


public function cambioestado($index,$idsolicitud){
		
	if( $index == 1 ) { $estado='Enviado'; }
	if( $index == 2 ) { $estado='Recibido'; }
	if( $index == 3 ) { $estado='Entregado'; }	

	$sql="UPDATE solicitud SET estado='$estado' WHERE idsolicitud='$idsolicitud'";	
	 
	return ejecutarConsulta($sql);
}

public function cambioEstadoDetalle($index,$idsolicitud,$sesion,$comentario){	
	
	if( $index == 0 ) { $estado='Pendiente'; }
	if( $index == 1 ) { $estado='Enviado'; }
	if( $index == 2 ) { $estado='Recibido'; }
	if( $index == 3 ) { $estado='Entregado'; }	


	date_default_timezone_set('America/Mexico_City');
	$fechacreada=date('Y-m-d H:i:s');

	$sql="UPDATE detalle_solicitud SET estado='$estado',fecha_hora='$fechacreada',idusuario='$sesion',comentario='$comentario' WHERE iddetalle_solicitud='$idsolicitud'";
	return ejecutarConsulta($sql);
}



//metodo para mostrar registros
public function mostrar($idsolicitud){
	
	$sql="SELECT  a.fecha_hora as fecha, a.idsolicitud, b.descripcion , a.num_comprobante, b.nombre as iddestino, a. estado FROM solicitud a INNER JOIN destinos b WHERE a.iddestino=b.iddestino and idsolicitud='$idsolicitud'";	
	return ejecutarConsultaSimpleFila($sql);
		
}

public function listarDetalle($idsolicitud){
	$sql="SELECT b.nombre,a.estado,a.cantidad,b.nombre as idmaterial, a.iddetalle_solicitud as iddetalle_solicitud FROM `detalle_solicitud` a INNER JOIN materiales b WHERE  a.idmaterial=b.idmaterial and a.idsolicitud= '$idsolicitud'";			
	return ejecutarConsulta($sql);
}

//listar registros
public function listar(){
	$sql="SELECT a.idsolicitud, b.nombre as iddestino, c.nombre as idusuario, a.num_comprobante as num_comprobante, a.fecha_hora as fecha_hora, a.estado as estado  FROM `solicitud` a  Inner join destinos b Inner join usuarios c where a.iddestino = b.iddestino and a.idusuario=c.idusuario  ORDER BY a.num_comprobante DESC";

	return ejecutarConsulta($sql);
}


public function listarop($index){
	if( $index == 0 ) { $estado='Pendiente'; }
	if( $index == 1 ) { $estado='Enviado'; }
	if( $index == 2 ) { $estado='Recibido'; }
	if( $index == 3 ) { $estado='Entregado'; }	

	$sql="SELECT a.idsolicitud, b.nombre as iddestino, c.nombre as idusuario, a.num_comprobante as num_comprobante, a.fecha_hora as fecha_hora, a.estado as estado  FROM `solicitud` a  Inner join destinos b Inner join usuarios c where a.iddestino = b.iddestino and a.idusuario=c.idusuario and a.estado='$estado'  ORDER BY a.num_comprobante DESC";

	return ejecutarConsulta($sql);
}


public function listarActivos(){
	$sql="SELECT a.idmaterial,a.idcategoria,c.nombre as categoria, a.nombre,a.stock,a.descripcion,a.imagen FROM materiales a INNER JOIN Categorias c ON a.idcategoria=c.idcategoria WHERE a.estado='1'";	
	return ejecutarConsulta($sql);
}


public function ultimo_ticket(){

	$sql="SELECT serie FROM tickets";
	return ejecutarConsulta($sql);
}

public function actualiza_ticket(){

	$sql="UPDATE tickets SET serie = serie +1";
	return ejecutarConsulta($sql);
}

public function revisaStatus($idsolicitud){
	
	$sql="SELECT  COUNT(*) as cantidad  FROM detalle_solicitud  WHERE idsolicitud='$idsolicitud' and estado<>'Entregado'";
	return ejecutarConsulta($sql);
}


}

 ?>
