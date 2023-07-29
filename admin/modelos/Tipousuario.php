<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Tipousuario{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar regiustro
public function insertar($nombre,$descripcion,$idusuario){
		date_default_timezone_set('America/Mexico_City');
	$fechacreada=date('Y-m-d H:i:s');
	$sql="INSERT INTO tipousuario (nombre,descripcion,fechacreada,idusuario) VALUES ('$nombre','$descripcion','$fechacreada','$idusuario')";
	return ejecutarConsulta($sql);
}

public function editar($idtipousuario,$nombre,$descripcion,$idusuario){
	$sql="UPDATE tipousuario SET nombre='$nombre',descripcion='$descripcion',idusuario='$idusuario' 
	WHERE idtipousuario='$idtipousuario'";
	return ejecutarConsulta($sql);
}
public function desactivar($idtipousuario){
	$sql="UPDATE tipousuario SET fechacreada='0' WHERE idtipousuario='$idtipousuario'";
	return ejecutarConsulta($sql);
}
public function activar($idtipousuario){
	$sql="UPDATE tipousuario SET fechacreada='1' WHERE idtipousuario='$idtipousuario'";
	return ejecutarConsulta($sql);
}

//metodo para mostrar registros
public function mostrar($idtipousuario){
	$sql="SELECT * FROM tipousuario WHERE idtipousuario='$idtipousuario'";
	return ejecutarConsultaSimpleFila($sql);
}

//listar registros
public function listar(){
	$sql="SELECT * FROM tipousuario";
	return ejecutarConsulta($sql);
}
//listar y mostrar en selct
public function select(){
	$sql="SELECT * FROM tipousuario";
	return ejecutarConsulta($sql);
}
}

 ?>
