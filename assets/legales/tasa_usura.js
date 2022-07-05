var datatableUsura = null;
$(document).ready(async function() {
    crearDataTable()
   
    $('#tablaUsura').removeAttr('style');
    
    const fecha = new Date();
    const anioActual = fecha.getFullYear();
    const anioSiguiente= anioActual+1
    $('#slc_anio').append('<option value="'+anioActual+'" selected>'+anioActual+'</option>');
    $('#slc_anio').append('<option value="'+anioSiguiente+'">'+anioSiguiente+'</option>');    
})

$('#txt_monto').click(function (event) {
    $(function(){

        $('#txt_monto').keypress(function(e) {
            if(isNaN(this.value + String.fromCharCode(e.charCode))) 
            return false;
        })
        .on(function(e){
            e.preventDefault();
        });          
    });
});
$('button#btn_registrar_usura').click(function (event) {
if ($('#txt_monto').val() == '') {
    Swal.fire({
        title: 'Atencion!',
        text: 'Debe ingresar un monto para el registro',
    });
    $('#txt_monto').focus();    

}else{
        event.preventDefault();
        $.ajax({
            url: base_url + 'legales/Legales/registrar_usura',
            type:'POST',
            data:{
                mes: $('#slc_mes').val(),
                anio: $('#slc_anio').val(),
                monto: $('#txt_monto').val()
            },
            success: function (res) {    
                if (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro guardado'
                    })
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Problema al registrar'
                    })
                }
                datatableUsura.ajax.reload()
                $('#slc_mes').val($('#slc_mes > option:first').val())
                $('#slc_anio').val($('#slc_anio > option:first').val())
                $('#txt_monto').val('')
            }
        });
    }
});

$('body').on('click','#tablaUsura a[id="editar"]',function(event){    
    const fecha = new Date();
    const anioActual = fecha.getFullYear();
    const anioSiguiente= anioActual+1
    
    $('.id').remove();
    $('.mes').remove();
    $('.anio').remove();
    $('.monto').remove();

    id_usura=$(this).attr('id_usura')
    anio=$(this).attr('anio')
    mes=$(this).attr('mes');
    valor=$(this).attr('valor')
    
    $("#myModal").modal("show");
    $('.modal-body').append('<form method="post">'); 
    $('#id_usura').append('<input class="id form-control" id="id_usu" type="hidden" value="'+id_usura+'" disabled></input>'); 
    $('#act_mes').append('<label class="mes">Mes:</label>'); 
    $('#act_mes').append('<select class="mes form-control" id="mes_act"><option value="Enero">Enero</option><option value="Febrero">Febrero</option><option value="Marzo">Marzo</option><option value="Abril">Abril</option><option value="Mayo">Mayo</option><option value="Junio">Junio</option><option value="Julio">Julio</option><option value="Agosto">Agosto</option><option value="Septiembre">Septiembre</option><option value="Octubre">Octubre</option><option value="Noviembre">Noviembre</option><option value="Diciembre">Diciembre</option></select>'); 
    $('#act_mes').css({'margin-left':'3%'})
    $('#act_anio').append('<label class="anio">Anio:</label>'); 
    if (anioActual == anio) {
        $('#act_anio').append('<select class="anio form-control" id="anio_act"><option value="'+anioActual+'" selected>'+anioActual+'</option><option value="'+anioSiguiente+'">'+anioSiguiente+'</option></select>');    
    }else{
        $('#act_anio').append('<select class="anio form-control" id="anio_act"><option value="'+anioActual+'">'+anioActual+'</option><option value="'+anioSiguiente+'" selected>'+anioSiguiente+'</option></select>');    
    }
    $('#act_monto').append('<label class="monto">Monto:</label>'); 
    $('#act_monto').append('<input class="monto form-control" id="monto_act" type="text" value="'+valor+'"></input>'); 
    $('#monto_act').click(function (event) {
        $(function(){
    
            $('#monto_act').keypress(function(e) {
                if(isNaN(this.value + String.fromCharCode(e.charCode))) 
                return false;
            })
            .on(function(e){
                e.preventDefault();
            });          
        });
    });
    $('.modal-body').append('</form>'); 
    $("#mes_act option[value='"+mes+"']").attr("selected", true);
    

});


function crearDataTable() {
    datatableUsura =  $('#tablaUsura').DataTable({
        // "destroy":true,          
        language: {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "infoThousands": ",",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros"
        },
        "columns": [
            { "data": "fecha_creacion" },
            { "data": "nombre_apellido" },
            { "data": "fecha_update" },
            { "data": "operador_update" },
            { "data": "anio" },
            { "data": "mes" },
            { "data": "valor" },
            { "data": null,
                render: function(data, type, row, meta) {
                    if (meta.row == 0) {
                        return "<a id_usura="+data.id+" anio="+data.anio+" mes="+data.mes+" valor="+data.valor+" id='editar'><button class='btnEditar btn btn-success' type='button'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>";
                    }else{
                        return "";
                    }
                }
            }
        ],
        "order": [[ 0, "desc" ]],
        ajax:{
            type: "POST",
            url: base_url + 'legales/Legales/mostrar_usura'
        }
        
    })
}

$('button#btn_actualizar').click(function (event) {
    $.ajax({
        type: "post",
        url: base_url + 'legales/Legales/actualizar_usura',
        data: {
            id: $("#id_usu").val(),
            mes: $('#mes_act').val(),
            anio: $('#anio_act').val(),
            monto: $('#monto_act').val()
        },
        success: function (response) {
                if (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro actualizado'
                    })
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Problema al actualizar'
                    })
                }

                datatableUsura.ajax.reload()
        }
    });

});