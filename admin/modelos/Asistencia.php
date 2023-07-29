<?php
//incluir la conexion de base de datos
require '../config/Conexion.php';
class Asistencia
{
    //implementamos nuestro constructor
    public function __construct()
    {
    }

    //listar registros
    public function listar()
    {
        $sql =
            'SELECT a.idasistencia,a.codigo_persona,a.fecha_hora,a.tipo,a.fecha,u.nombre,u.apellidos,d.nombre as departamento FROM asistencia a INNER JOIN usuarios u INNER JOIN departamento d ON u.iddepartamento=d.iddepartamento WHERE a.codigo_persona=u.codigo_persona';
        return ejecutarConsulta($sql);
    }

    public function listaru($idusuario)
    {
        $sql = "SELECT a.idasistencia,a.codigo_persona,a.fecha_hora,a.tipo,a.fecha,u.nombre,u.apellidos,d.nombre as departamento FROM asistencia a INNER JOIN usuarios u INNER JOIN departamento d ON u.iddepartamento=d.iddepartamento WHERE a.codigo_persona=u.codigo_persona AND u.idusuario='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function listar_asistencia(
        $fecha_inicio,
        $fecha_fin,
        $codigo_persona
    ) {
        $sql = "SELECT a.idasistencia,a.codigo_persona,a.fecha_hora,a.tipo,a.fecha,u.nombre,u.apellidos FROM asistencia a INNER JOIN usuarios u ON  a.codigo_persona=u.codigo_persona WHERE DATE(a.fecha)>='$fecha_inicio' AND DATE(a.fecha)<='$fecha_fin' AND a.codigo_persona='$codigo_persona'";
        return ejecutarConsulta($sql);
    }
}

?>
