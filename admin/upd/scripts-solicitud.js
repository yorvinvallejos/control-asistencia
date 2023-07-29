var tabla;

//funcion que se ejecuta al inicio
function init(){
   mostrarform(false);
   listar();

   $("#formulario").on("submit",function(e){
   	guardaryeditar(e);
   });

   //cargamos los items al select destino
    $.post("../ajax/solicitud.php?op=selectDestino", function(r){
   	$("#iddestino").html(r);
   	$('#iddestino').selectpicker('refresh');
   });

}

//funcion limpiar
function limpiar(){

	$("#iddestino").val(""); 
	$("#iddestinos").val("");
	$("#idsolicitud").val("");
	$("#fecha").val("");
	$("#numerocomprobante").val("");
	$("fecha_hora").val("");	
	$(".filas").remove();
	
}

//funcion mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag){
		$("#formularioregistros").show();		
		$("#btnagregar").hide();
		listarMateriales();
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		detalles=0;
		$("#btnAgregarArt").show();
		$("#btnRequerimientos").hide();
		$("#fecha").hide();
		$("#destino").hide();
		$("#fecha_hora").show();
		$("#iddestinos").show();
		$("#listadoregistros").hide();
		//obtenemos la fecha actual
	var now = new Date();
	var day =("0"+now.getDate()).slice(-2);
	var month=("0"+(now.getMonth()+1)).slice(-2);
	var today=now.getFullYear()+"-"+(month)+"-"+(day);
	$("#fecha_hora").val(today);
		

	}else{		
		$("#btnRequerimientos").show();
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

//cancelar form
function cancelarform(){
	limpiar();
	mostrarform(false);
}

//funcion listar
function listar(){
	tabla=$('#tbllistado').dataTable({
		"aProcessing": true,//activamos el procedimiento del datatable
		"aServerSide": true,//paginacion y filrado realizados por el server
		dom: 'Bfrtip',//definimos los elementos del control de la tabla
		buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
		],
		"ajax":
		{
			url:'../ajax/solicitud.php?op=listar',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);

			}
		},
		"bDestroy":true,
		"iDisplayLength":10,//paginacion
		"order":[[0,"desc"]]//ordenar (columna, orden)
	}).DataTable();
}



function listarMateriales(){
	tabla=$('#tblarticulos').dataTable({
		"aProcessing": true,//activamos el procedimiento del datatable
		"aServerSide": true,//paginacion y filrado realizados por el server
		dom: 'Bfrtip',//definimos los elementos del control de la tabla
		buttons: [

		],
		"ajax":
		{
			url:'../ajax/solicitud.php?op=listarMateriales',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
		"iDisplayLength":10,//paginacion
		"order":[[0,"desc"]]//ordenar (columna, orden)
	}).DataTable();
}
//funcion para guardaryeditar
function guardaryeditar(e){
     e.preventDefault();//no se activara la accion predeterminada 
     
     var formData=new FormData($("#formulario")[0]);

     $.ajax({
     	url: "../ajax/solicitud.php?op=guardaryeditar",
     	type: "POST",
     	data: formData,
     	contentType: false,
     	processData: false,

     	success: function(datos){  
     		bootbox.alert(datos); 
     		mostrarform(false);
     		listar();
     	}
     });

     limpiar();
}

function mostrar(idsolicitud){
	 $("#getCodeModal").modal('show');
	 
	$.post("../ajax/solicitud.php?op=mostrar&idsol="+idsolicitud,
		function(data,status)
		{
			data=JSON.parse(data);		
			$("#destinov").val(data.iddestino+ " "+ data.descripcion );
			$("#fecha_horav").val(data.fecha);			
			$("#numerocomprobantev").val(data.num_comprobante );
		});

	$.post("../ajax/solicitud.php?op=listarDetalle&idsol="+idsolicitud,function(r){
		$("#detallesv").html(r);
	});

}


//funcion para desactivar
function entregar(idsolicitud){
	bootbox.confirm("¿Esta seguro de entregar esta solicitud?", function(result){
		if (result) {
			$.post("../ajax/solicitud.php?op=entregar", {idsolicitud : idsolicitud}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}


//funcion para cambiar status
function cambioestado(index,idsolicitud){
	bootbox.confirm("¿Esta seguro de actualizar esta solicitud?", function(result){
		if (result) {
			$.post("../ajax/solicitud.php?op=flujo", {index : index, idsolicitud : idsolicitud}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}


function alerta() 
{
var mensaje;
var opcion = prompt("Introduzca su nombre:", "Aner Barrena");
 
if (opcion == null || opcion == "") {
        mensaje = "Has cancelado o introducido el nombre vacío";
        } else {
            alert(mensaje = "Hola " + opcion);
            }
}





//funcion para cambiar status del detalle
function cambioestadodetalle(index,idmaster,idsolicitud){
									
	 bootbox.prompt("¿Esta seguro de actualizar esta solicitud?", function(result){
		if (result) {
			$.post("../ajax/solicitud.php?op=flujoDetalle", {index : index, idsolicitud : idsolicitud,comentario:result}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
			$("#getCodeModal").modal('show');
		}else{
			bootbox.alert('debes ingresar un comentario');
		}
		$.post("../ajax/solicitud.php?op=listarDetalle&idsol="+idmaster,function(r){
		$("#detallesv").html(r);
		tabla.ajax.reload();
	});
	})
}

//declaramos variables necesarias para trabajar con las compras y sus detalles
var detalles=0;
var cont=0;
		

$("#btnGuardar").hide();

function agregarDetalle(idarticulo,articulo,bloque,cantidad){
	//function agregarDetalle(idarticulo,articulo){
	//var cantidad=1;

	if (idarticulo!="") {		
		var fila='<tr class="filas" id="fila'+cont+'">'+
        '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')">X</button></td>'+
        '<td><input type="hidden" name="bloque[]" value="'+idarticulo+'">'+bloque+'</td>'+
        '<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td>'+
        '<td><input type="number" name="cantidad[]" id="cantidad[]"  value="'+cantidad+'" readonly></td>'+    
        '<td><input type="text" name="comentario[]" id="comentario[]" value=""></td>'+            
		'</tr>';
		cont++;
		detalles++;
		$('#detalles').append(fila);
			evaluar();

	}else{
		alert("error al ingresar el detalle, revisar las datos del articulo ");
	}
}


function evaluar(){

	if (detalles>0) 
	{
		$("#btnGuardar").show();
		$("#fecha_hora").show();
		$("#iddestinos").show();			
		$("#fecha").hide();
		$("#destino").hide();
		
	}
	else
	{
		
		$("#btnGuardar").hide();
		cont=0;
	}
}

function eliminarDetalle(indice){
$("#fila"+indice).remove();
detalles=detalles-1;

}

init();