$( document ).ready(function() {
    //Cambio en el select principal de los clientes, carga lista de eventos
    $( ".clients" ).change(function() {
        loadEventList($(this).val());
    });
    
    $('.dcjq-parent').click(function() {
        alert('a');
       $(this).find(".dcjq-icon").css('background', 'url(../images/expand.png) no-repeat bottom !important');
    });
    
    
    //Agregar dinamicamente las redes sociales de un evento
    $(".add-e a").click(function(event){
        elem = $(this).closest(".social_networks-td").find('.networks:first').clone().appendTo(".social_networks-td");
        $(elem).find("select").val("");
        $(elem).find("input").val("");
        $(".delete").show();
        $(".delete:first").hide();
    })
    
    //Eliminar dinamicamente una rede social.
    $("body").on('click', '.delete a', function() {
        $(this).closest(".networks").remove();
    })
    
    //Agregar dinamicamente los organizadores de un evento
    $(".add-org a").click(function(event){
        elem = $(this).closest(".organizer-td").find('.organizer:first').clone().appendTo(".organizer-td");
        $(elem).find("textarea").text("");
        $(elem).find("input").val("");
        $(".delete-org").show();
        $(".delete-org:first").hide();
    })
    
    //Eliminar dinamicamente un organizador.
    $("body").on('click', '.delete-org a', function() {
        $(this).closest(".organizer").remove();
    })
    
    
    //Agregar dinamicamente las opciones a una pregunta
    $(".add-opt a").click(function(event){
        elem = $(this).closest(".option-td").find('.option:first').clone().appendTo(".option-td");
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
        if ($(this).val()== "cliente"){
            $('.client_id').show();
            $('.permi').show();
        }else{
             $('.client_id').hide();
             $('.permi').hide();
        }
        
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
    
    
});

/* Funcion encargada de obtener los eventos asociados a un cliente */
function loadEventList(client){
    var clientId = client;
    if (typeof client !== "undefined") clientId = clientId;
    var eventId  = eventId;
    if (typeof eventId === "undefined"  ) $eventId = "";
    html = "";
    $(".events").html("<option value=''>Cargando...</option>");
    if (typeof clientId !== "undefined"  ){
        $.post( "./backend/events-ajax.php", {"id": clientId}, function( data ) {
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
    $( ".datepicker" ).datepicker({
      showOn: "both",
      buttonImage: "images/calendar.png",
      buttonImageOnly: true,
      buttonText: "Fecha"
    });
    $('.timepicker').timepicki(); 
});