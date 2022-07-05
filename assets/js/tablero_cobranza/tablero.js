const OPERADOR_DESCARGAS = [2,9,13,12];

$(document).ready(function() {
	base_url = $("input#base_url").val();
	vistaTableroCobranza();
	setInterval(function() {
        var param = $("#param").val();
		$("#tp_indicadores_cobranza_"+  param)
					.DataTable()
					.ajax.reload();

	}, 900000);
});
function showModal() {
	document.getElementById('openModal').style.display = 'block';
	configurar_tablero();
}

function CloseModal() {
	document.getElementById('openModal').style.display = 'none';
}

//CARGA LA TABLA GENERAL
function vistaTableroCobranza() {
    //SETTINGS TABLA GENERAL
    $("#param").val("general");

    var ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/tableroCobranza",
        data: function ( d ) {
            d.paramBusqueda = $("#param").val();
        },
	};
	var columnDefs = [
        {
			targets: [0],
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 18px; !important;"
				);
			}
		},
        {
            targets:[3,6],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"background-color: #f9e79f !important; font-size: 22px"
				);
			},

        },
        {
			targets: [8,10],
            className: 'dt-body-right',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important; font-weight: bold;"
				);
			}
		},
        {
            targets: [1,2,4,5],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important; color:#528cbc;"
				);
			}
        },
        {
            targets: [7,9],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important;"
				);
			}
        }
        
	];

	var columns = [
		{
			data: "nombre_apellido"
		},
		{
			data: "acuerdos_alcanzados_actual",
            render: function(data, type, row, meta) {
                $('#desde_2').val(row.desde_2)
                $('#desde_1').val(row.desde_1)
                $('#hasta_2').val(row.hasta_2)
                $('#hasta_1').val(row.hasta_1)

                
                if(data != null){
                    if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                        return '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+row.desde_2+'/'+row.hasta_2+'/'+row.id_operador+'">'+data+'</a>';
                    }else{
                        return '<p class="text-black">'+data+'</p>';
                    }
                }else{
                    return '';
                } 
            }
		},
		{
			data: "acuerdos_cumplidos_actual"
		},
		{
			data: "conversion_quincena_actual",
            render: function(data, type, row, meta) {
                if(data !== null){
                    
                    return Math.round(data) + "%";
                }else{
                    return ""
                }
            }
		},
        {
			data: "acuerdo_quincena_anterior",
            render: function(data, type, row, meta) {
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                    return '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+row.desde_1+'/'+row.hasta_1+'/'+row.id_operador+'">'+data+'</a>';
                }else{
                    return '<p class="text-black">'+data+'</p>';
                }
            }
		},
		{
			data: "acuerdos_cumpl_quincena_anterior",
            render: function(data, type, row, meta) {
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                    return data;
                }else{
                    return '<p class="text-black">'+data+'</p>';
                }
            }
		},
		{
			data: "conversion_quincena_anterior",
            render: function(data, type, row, meta) {
                if(data !== null){
                    return Math.round(data) + "%";
                }else{
                    return ""
                }
            }
		},
        {
            data: "cantidad_quincena_actual",
        },
		{
			data: "suma_acuerdos_quincena_actual",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},
        {
            data: "cantidad_quincena_anterior",
        },
		{
			data: "suma_acuerdos_quincena_anterior",
            render: $.fn.dataTable.render.number( '.', ',', 2) 
            
		}
	];
    /**
     * Funcion anonima : footerCallback
     * Para leer la documentacion ir a : https://datatables.net/examples/advanced_init/footer_callback.html
     */
    var footerCallback = function( tfoot, data, start, end, display ) {
        var api = this.api();
        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        var totalAlc_actual =[]
        $( api.column(1).footer() ).html(
            api.column(1).data().reduce( function ( a, b ) {
                totalAlc_actual.push(intVal(b))
                const reducer = (accumulator, curr) => accumulator + curr;
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {    
                    return   '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+$('#desde_2').val()+'/'+$('#hasta_2').val()+'/0'+'">'+totalAlc_actual.reduce(reducer)+'</a>';
                }else{
                    return   '<p class="text-black">'+$('td#total_acutal_general.dt-body-center')[0].outerText+'</p>';
                }
            }, 0 )
        );

        $( api.column( 2 ).footer() ).html(
            api.column( 2 ).data().reduce( function ( a, b ) {
                return intVal(a) + intVal(b);
            }, 0 )
        );
        
        $( api.column( 4 ).footer() ).html(
            api.column( 4 ).data().reduce( function ( a, b ) {
                return intVal(a) + intVal(b);

            }, 0 )
        );
        var totalAlc_anterior = []
        $( api.column( 4).footer() ).html(
            api.column( 4 ).data().reduce( function ( a, b ) {
                totalAlc_anterior.push(intVal(b))
                
                const reducer = (accumulator, curr) => accumulator + curr;
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                    return   '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+$('#desde_1').val()+'/'+$('#hasta_1').val()+'/0'+'">'+totalAlc_anterior.reduce(reducer)+'</a>'
                    ;                
                }else{
                    totalAlc_anterior.push(intVal(b))
                    const reducer = (accumulator, curr) => accumulator + curr;
                    return   '<p class="text-black">'+totalAlc_anterior.reduce(reducer)+'</p>'
                ;
                }
            }, 0 )
        );

        $( api.column( 5 ).footer() ).html(
            api.column( 5 ).data().reduce( function ( a, b ) {
                return intVal(a) + intVal(b);
            }, 0 )
        );

    }
    var extras = getExtras();
    //Este metodo esta en function.js
	TablaPaginada(
		"tp_indicadores_cobranza_general",
		8,
		"desc",
		"",
		"",
		ajax,
		columns,
		columnDefs,
        null,
        null,
        25,
        footerCallback,
        extras
	);
}

function initTableActual(e, dt, node, config){
   
    ajax =  getAjax();
    columns =  getColumnActual();
    columnDefs =  getColumnDefsActual();
    footerCallback = getFooterCallbackActual();
    extras = getExtras();

    TablaPaginada(
        "tp_indicadores_cobranza_actual",
        8,
        "desc",
        "",
        "",
        ajax,
        columns,
        columnDefs,
        null,
        null,
        25,
        footerCallback,
        extras
    );
}

function initTableAnterior(){
   
    ajax =  getAjax();
    columns =  getColumnAnterior();
    columnDefs =  getColumnDefsAnterior();
    footerCallback = getFooterCallbackAnterior();
    extras = getExtras();

    TablaPaginada(
        "tp_indicadores_cobranza_anterior",
        8,
        "desc",
        "",
        "",
        ajax,
        columns,
        columnDefs,
        null,
        null,
        25,
        footerCallback,
        extras
    );
}
//USE ALL
function getAjax(){

    var ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/tableroCobranza",
        data: function ( d ) {
            d.paramBusqueda = $("#param").val();
        }
	};
    
    return ajax;
}

function getExtras(){

    var extras = 
    {
        dom: 'Bfrtip',
        lengthChange: false,
        buttons:{ 
            dom: {
                button: {
                    tag: 'button',
                    className: ''
                }
            },
            buttons: [
                {
                    text: 'GENERAL',
                    className: 'btn  btn-sm btn-success',
                    action: function ( e, dt, node, config ) {
                        var tb_activa ="#tp_indicadores_cobranza_"+ $("#param").val();
                        
                        $("#param").val("general")
    
                        $("#actual").addClass('hidden')
                        $("#anterior").addClass('hidden')
                        $("#general").removeClass('hidden')
                        if(dt!= null){
                            dt.clear();
                            dt.destroy();
                        }
                        $(tb_activa + " tbody").empty();

                        vistaTableroCobranza();
                        
                    }
                },
                {
                    text: 'ACTUAL',
                    className: 'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                        var tb_activa ="#tp_indicadores_cobranza_"+ $("#param").val();
                        $("#param").val("actual")
    
                        $("#general").addClass('hidden')
                        $("#anterior").addClass('hidden')
                        $("#actual").removeClass('hidden')
                        if(dt!= null){
                            dt.clear();
                            dt.destroy();
                        }
                        $(tb_activa + " tbody").empty();

                        initTableActual();
                    }
                },
                {
                    text: 'ANTERIOR',
                    className: 'btn btn-sm btn-warning',
                    action: function ( e, dt, node, config ) {
                        
                        if(dt!= null){
                            dt.clear();
                            dt.destroy();
                            
                        }
                        //2nd empty html
                        var tb_activa ="#tp_indicadores_cobranza_"+ $("#param").val();

                        $("#param").val("anterior")
                        $("#anterior").removeClass('hidden')
                        $("#general").addClass('hidden')
                        $("#actual").addClass('hidden')

                        $(tb_activa + " tbody").empty();

                        initTableAnterior();
                    }
                }
            ]
        },
    };

    return extras;
}
//USE ALL

//USE TABLE ACTUAL
function getColumnActual() {
    var columns = [
		{
			data: "nombre_apellido"
		},
		{
			data: "acuerdos_alcanzados_actual",
            render: function(data, type, row, meta) {
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                    return '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+row.desde_1+'/'+row.hasta_1+'/'+row.id_operador+'">'+data+'</a>';          
                }else{
                    return '<p class="text-black">'+data+'</p>';
                }
                
            }
		},
		{
			data: "acuerdos_cumplidos_actual"
		},
		{
			data: "conversion_quincena_actual",
            render: function(data, type, row, meta) {
                if(data !== null){
                    
                    return Math.round(data) + "%";
                }else{
                    return ""
                }
            }
		},
        {
			data: "cantidad_quincena_actual_0_40"
		},
		{
			data: "suma_acuerdos_quincena_actual_0_40",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},
        {
			data: "cantidad_quincena_actual_41_90"
		},
		{
			data: "suma_acuerdos_quincena_actual_41_90",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},
        {
			data: "cantidad_quincena_actual_91_120"
		},
		{
			data: "suma_acuerdos_quincena_actual_91_120",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},
        /*{
			data: "cantidad_quincena_actual_120"
		},
		{
			data: "suma_acuerdos_quincena_actual_120",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},*/

	];
    return columns;
}

function getColumnDefsActual(){
    var columnDefs = [
        {
			targets: [0],
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 18px; !important;"
				);
			}
		},
        {
            targets:[3],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"background-color: #f9e79f !important; font-size: 22px"
				);
			},

        },
        {
			targets: [5,7,9/*,11*/],
            className: 'dt-body-right',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important; font-weight: bold;"
				);
			}
		},
        {
            targets: [1,2],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important; color:#528cbc;"
				);
			}
        },
        {
            targets: [4,6,8/*,10*/],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important;"
				);
			}
        }
	];
    return columnDefs;
}

function getFooterCallbackActual(){
    /**
     * Funcion anonima : footerCallback
     * Para leer la documentacion ir a : https://datatables.net/examples/advanced_init/footer_callback.html
     */
     var footerCallback = function( tfoot, data, start, end, display ) {
        var api = this.api();
        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        
        // $( api.column(1 ).footer() ).html(
            //     api.column( 1 ).data().reduce( function ( a, b ) {
                //         return intVal(a) + intVal(b);
                //     }, 0 )
                
                // );
        let totalAlc_tablaActual = []
        $( api.column(1 ).footer() ).html(
            api.column( 1 ).data().reduce( function ( a, b ) {
                totalAlc_tablaActual.push(intVal(b))
                const reducer = (accumulator, curr) => accumulator + curr;
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                    return   '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+data[0]['desde_1']+'/'+data[0]['hasta_1']+'/0'+'">'+totalAlc_tablaActual.reduce(reducer)+'</a>'
                    ;                
                }else{
                    totalAlc_tablaActual.push(intVal(b))
                    const reducer = (accumulator, curr) => accumulator + curr;
                    return   '<p class="text-black">'+totalAlc_tablaActual.reduce(reducer)+'</p>'
                ;
                }
                
            }, 0 )
        );
        $( api.column( 2 ).footer() ).html(
            api.column( 2 ).data().reduce( function ( a, b ) {
                return intVal(a) + intVal(b);
            }, 0 )
        );
        
    }

    return footerCallback;
}

//USE TABLE ANTERIOR

function getColumnAnterior() {
    var columns = [
		{
			data: "nombre_apellido"
		},
		{
			data: "acuerdos_alcanzados_anterior",
            render: function(data, type, row, meta) {
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                    return '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+row.desde_1+'/'+row.hasta_1+'/'+row.id_operador+'">'+data+'</a>';                                
                }else{
                    return '<p class="text-black">'+data+'</p>';            
                }
                
            }
		},
		{
			data: "acuerdos_cumplidos_anterior"
		},
		{
			data: "conversion_quincena_anterior",
            render: function(data, type, row, meta) {
                if(data !== null){
                    
                    return Math.round(data) + "%";
                }else{
                    return ""
                }
            }
		},
        {
			data: "cantidad_quincena_anterior_0_40"
		},
		{
			data: "suma_acuerdos_quincena_anterior_0_40",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},
        {
			data: "cantidad_quincena_anterior_41_90"
		},
		{
			data: "suma_acuerdos_quincena_anterior_41_90",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},
        {
			data: "cantidad_quincena_anterior_91_120"
		},
		{
			data: "suma_acuerdos_quincena_anterior_91_120",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},
        /*{
			data: "cantidad_quincena_anterior_120"
		},
		{
			data: "suma_acuerdos_quincena_anterior_120",
            render : $.fn.dataTable.render.number( '.', ',', 2) 
		},*/

	];
    return columns;
}

function getColumnDefsAnterior(){
    var columnDefs = [
        {
			targets: [0],
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 18px; !important;"
				);
			}
		},
        {
            targets:[3],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"background-color: #f9e79f !important; font-size: 22px"
				);
			},

        },
        {
			targets: [5,7,9/*,11*/],
            className: 'dt-body-right',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important; font-weight: bold;"
				);
			}
		},
        {
            targets: [1,2],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important; color:#528cbc;"
				);
			}
        },
        {
            targets: [4,6,8/*,10*/],
            className: 'dt-body-center',
            createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
                    "font-size: 22px; !important;"
				);
			}
        }
	];
    return columnDefs;
}

function getFooterCallbackAnterior(){
    /**
     * Funcion anonima : footerCallback
     * Para leer la documentacion ir a : https://datatables.net/examples/advanced_init/footer_callback.html
     */
     var footerCallback = function( tfoot, data, start, end, display ) {
        var api = this.api();
        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        // $( api.column(1 ).footer() ).html(
        //     api.column( 1 ).data().reduce( function ( a, b ) {
        //         return intVal(a) + intVal(b);
        //     }, 0 )
        // );
        // console.log(data[0]['desde_1']);

        let totalAlc_tablaAnterior =[]
        $( api.column(1 ).footer() ).html(
            api.column( 1 ).data().reduce( function ( a, b ) {
                totalAlc_tablaAnterior.push(intVal(b))
                const reducer = (accumulator, curr) => accumulator + curr;
                if (jQuery.inArray(parseInt($("#txt_tipo_operador").val()), OPERADOR_DESCARGAS) !== -1) {
                    return   '<a href="'+$("input#base_url").val()+'/'+'api/excel/'+data[0]['desde_1']+'/'+data[0]['hasta_1']+'/0'+'">'+totalAlc_tablaAnterior.reduce(reducer)+'</a>';
                }else{
                    totalAlc_tablaAnterior.push(intVal(b))
                    const reducer = (accumulator, curr) => accumulator + curr;
                    return   '<p class="text-black">'+totalAlc_tablaAnterior.reduce(reducer)+'</p>';
                }
                
            }, 0 )
        );

        $( api.column( 2 ).footer() ).html(
            api.column( 2 ).data().reduce( function ( a, b ) {
                return intVal(a) + intVal(b);
            }, 0 )
        );
        
    }

    return footerCallback;
}

