$( document ).ready(function() {
    //Cambio en el select principal de los clientes, carga lista de eventos
    $( ".clients" ).change(function() {
        loadEventList($(this).val());
    });
    
    //Agregar dinamicamente las redes sociales de un evento
    $(".add-e a").click(function(event){
        elem = $(this).closest(".social_networks-td").find('.networks:first').clone(true).appendTo(".social_networks-td");
        $(elem).find("select").val("");
        $(elem).find("input").val("");
        $(elem).find("label").remove();
        $(".delete").show();
        $(".delete:first").hide();
    })
    
    //Eliminar dinamicamente una rede social.
    $("body").on('click', '.delete a', function() {
        $(this).closest(".networks").remove();
    })
    
    //Agregar dinamicamente los organizadores de un evento
    $(".add-org a").click(function(event){
        elem = $(this).closest(".organizer-td").find('.organizer:first').clone(true).appendTo(".organizer-td");
        $(elem).find("textarea").text("");
        $(elem).find("input").val("");
        $(".delete-org").show();
        $(".delete-org:first").hide();
        $(elem).find("label").remove();
    })
    
    //Eliminar dinamicamente un organizador.
    $("body").on('click', '.delete-org a', function() {
        $(this).closest(".organizer").remove();
    })
    
    //Agregar dinamicamente las opciones a una pregunta
    $(".add-opt a").click(function(event){
        elem = $(this).closest(".option-td").find('.option:first').clone(true).appendTo(".option-td");
        $(elem).find("textarea").text("");
        $(elem).find("input").val("");
        $(".delete-opt").show();
        $(".delete-opt:first").hide();
    })
    
    //Eliminar dinamicamente las opciones a una pregunta
    $("body").on('click', '.delete-opt a', function() {
        $(this).closest(".option").remove();
    })
    
    //Ocultar/mostrar permisologia dependiendo del tipo de usuario
    $('select[name=type]').change(function(event){
        $userType = $("#userType").val();
        //Si el usuario es de tipo administrador (super user) entonces mostrar el listado de clientes
        //para escoger el cliente asociado
        if ($(this).val()== "Supervisor") {
            if ($userType == "administrador") {
                $('.client_id').show();
            }
            $('.permi').show();
        }else if ($(this).val()== "Administrador"){
            if ($userType == "administrador") {
                $('.client_id').show();
            }
            $('.permi').hide();
        }else if ($(this).val()== "Super Usuario"){
             $('.client_id').hide();
             $('.permi').hide();
        }
    });

    //Mostrar codigo de area segun pais seleccionado
    $('select[name=country]').change(function(event){
        $.post( "./common/phoneCode.php", {"code": $(this).val()}, function( data ) {
           $(".code").val(data);
        });
    });

    //Ocultar campos dependiendo de la categoria del expositor
    $('select[name=category_id]').change(function(event){
        $tipo = types[$(this).val()];
        if ($tipo == "grid"){
            $('.manage-content tr').hide();
            $('.tr_image_path').show();
            $('.tr_actions').show();
            $('.tr_category_id').show();
        }else{
            $('.manage-content tr').show();            
        }
        
    });
    
    //Mostrar el menu para cambiar la contraseña
    $(".top-sb.submenu-holder").click(function(event){
        event.stopPropagation();
        $(".subm").show();
    });
    
    //
    $("body").click(function(event){
        $(".subm").hide();
    });
    
    $(".forgot").click(function(event){
        if (typeof pass !== "undefined" && pass == 1  ){
            $(".error-login").hide();
            $(".succ-login").hide();
        }
        if ($(this).text() != "Cancelar"){
            $("input[name='at-password']").hide();
            $("button[name='login']").html("Restablecer Contraseña");
            $("button[name='login']").attr("name", "forgot");
            $(this).html("Cancelar");
        }else{
            $("input[name='at-password']").show();
            $("button[name='forgot']").html("Ingresar");
            $("button[name='forgot']").attr("name", "login");
            $(this).html("¿Olvidó su contraseña?");
        }
    });
    
    $(".information").click(function(event){
        $(".image_format").toggle("fast");
    });
    
    //On click read
     $("input[type='checkbox']").click(function(event){
        if ($(this).is(':checked')){
           name = $(this).attr("name");
           id   = name.substring(0,1);
           if (name.substring(2) == "create" || name.substring(2) == "delete" || name.substring(2) == "update"){
                aux = id + "_read";
                $("input[name='"+aux+"']").prop('checked', true);
           }
       }
    });
    
    
});

/* Funcion encargada de obtener los eventos asociados a un cliente */
function loadEventList(client){
    var clientId = client;
    if (typeof client === "undefined") clientId = "";
    var eventId  = eventId;
    if (typeof eventId === "undefined"  ) $eventId = "";
    html = "";
    $(".events").html("<option value=''>Cargando...</option>");
    if (typeof clientId !== "undefined"  ){
        $.post( "./backend/events_ajax.php", {"id": clientId}, function( data ) {
            //alert(data);
            data = jQuery.parseJSON(data);
            if (data == ""){
                 html += "<option value=''>No hay eventos asociados</option>";
            }else{
                html += "<option value=''>Seleccionar evento</option>";
                $.each(data, function(idx, obj) {
                   sel = ''; if (eventId == idx) sel = 'selected';
                   html += "<option value='"+ idx+"' "+ sel +">"+obj+"</option>";
                });
            }
            $(".events").html(html);
            if (typeof eventId === "undefined" || eventId == ""){
                //Ocultar todas las secciones menos eventos
                $(".infu li").hide();
                if (clientId != "") $(".infu li.eventos").show();
            }
        });
    }    
}

//Tablas
$(document).ready( function () {
    $('.fireUI-table').DataTable(
        {
            "bLengthChange": false,
            "bInfo": false,
            aoColumnDefs: [
                {
                    bSortable: false,
                    aTargets: [ -1 ]
                }
            ],
            "fnDrawCallback":function(){
                if($(".fireUI-table").find("tr:not(.ui-widget-header)").length<=5){
                    $('.dataTables_wrapper div.dataTables_paginate').hide();
                } else {
                    $('.dataTables_wrapper div.dataTables_paginate').show();
                }
            },
            "language":
            {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });

    $(function() {
        $( "#dialog-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons: {
                "Si": function() {
                    $( this ).dialog( "close" );
                    $('<input />').attr('type', 'hidden')
                        .attr('name', "delete")
                        .attr('value', "1")
                        .appendTo('#form');
                    $("#form").submit();
                },
                "No": function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        $(".dltP").on("click", function() {
            $("#dialog-confirm").dialog("open");
        });
    });

});


//Date picker español
$.datepicker.regional['es'] = {
     closeText: 'Cerrar',
     prevText: '<Ant',
     nextText: 'Sig>',
     currentText: 'Hoy',
     monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
     monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
     dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
     dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
     dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
     weekHeader: 'Sm',
     dateFormat: 'yy-mm-dd',
     firstDay: 1,
     isRTL: false,
     showMonthAfterYear: false,
     yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['es']);
$(function() {
    min =  0;
    max = "";
    if (typeof startDate !== 'undefined') {
        min = startDate;
    }
    if (typeof endDate !== 'undefined') {
        max = endDate;
    }
    $( ".datepicker" ).datepicker({
      showOn: "both",
      buttonImage: "images/calendar.png",
      buttonImageOnly: true,
      buttonText: "Fecha",
      minDate: min,
      maxDate: max,
    });
    $('.timepicker').timepicki(); 
});

$.validator.messages.required   = 'Este campo es obligatorio';
$.validator.messages.email      = "Ingrese una dirección de correo electrónico válida.";
$.validator.messages.number     = "Sólo se permiten valores numéricos.";
$.validator.messages.url        = "La dirección URL no es válida";

jQuery(function($) {
    $.validator.addMethod("phoneNumber", function(value, element) {
        return this.optional(element) || /^[0-9\-\s]+$/i.test(value);
    }, "Ingrese un número de teléfono válido. Los símbolos permitidos son: - y espacio.");

    $.validator.addMethod("phone", function(value, element) {
        return this.optional(element) || /^[0-9\-\s]+$/i.test(value);
    }, "Ingrese un número de teléfono válido. Los símbolos permitidos son: - y espacio.");

    validator = $("#form").validate(
        {
            rules: {
                "email"             : {email: true},
                "contact_email"     : {email: true},
                "contact_phone"     : {phoneNumber: true},
                "phone"             : {phoneNumber:true},
                "website"           : {url:     true},
                "link"              : {url:     true},
                "position"          : {number:  true},
                "url_organizer[]"   : {url:     true},
                "value[]"           : {url:  true},
            }
        } 
    );
})

