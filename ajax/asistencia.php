<?php
require_once '../modelos/Asistencia.php';

$asistencia = new Asistencia();

$codigo_persona = isset($_POST['codigo_persona'])
    ? limpiarCadena($_POST['codigo_persona'])
    : '';
$iddepartamento = isset($_POST['iddepartamento'])
    ? limpiarCadena($_POST['iddepartamento'])
    : '';

switch ($_GET['op']) {
    case 'registrar_asistencia':
        $result = $asistencia->verificarcodigo_persona($codigo_persona);

        if ($result > 0) {
            date_default_timezone_set('America/Lima');
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');

            $result2 = $asistencia->seleccionarcodigo_persona($codigo_persona);
            $count2 = mysqli_num_rows($result2);

            $par = abs($count2 % 2);

            if ($par == 0) {
                $tipo = 'Entrada';
                $rspta = $asistencia->registrar_entrada($codigo_persona, $tipo);
                //$movimiento = 0;
                echo $rspta
                    ? '<h3><strong>Nombres: </strong> ' .
                        $result['nombre'] .
                        ' ' .
                        $result['apellidos'] .
                        '</h3><div class="alert alert-success"> Ingreso registrado ' .
                        $hora .
                        '</div>'
                    : 'No se pudo registrar el ingreso';
            } else {
                $tipo = 'Salida';
                $rspta = $asistencia->registrar_salida($codigo_persona, $tipo);
                //$movimiento = 1;
                echo $rspta
                    ? '<h3><strong>Nombres: </strong> ' .
                        $result['nombre'] .
                        ' ' .
                        $result['apellidos'] .
                        '</h3><div class="alert alert-danger"> Salida registrada ' .
                        $hora .
                        '</div>'
                    : 'No se pudo registrar la salida';
            }
        } else {
            echo '<div class="alert alert-danger">
                       <i class="icon fa fa-warning"></i> No hay empleado registrado con esa código...!
                         </div>';
        }

        break;
}
?>
