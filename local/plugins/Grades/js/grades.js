function editarNota(id) {

    $("#div-grades-hidden-" + id).attr('class', 'notice-grades');

    $("#button-modify-grade-" + id).attr('class', 'notice-modify-grade-hidden');
}

function puntuarNota(noticeid, value) {

    $("#notice-" + noticeid + '>.notice-grades').attr('class', 'notice-grades-hidden');

    $("#notice-" + noticeid).append('<p class="temp-text-grades">Puntuado con un: ' + value + '</p>');

}

function mostrarPuntuacion(noticeid){
    
    
    if($("#notice-" + noticeid + '>.div-with-grades-hidden').length){
        
        $("#notice-" + noticeid + '>.div-with-grades-hidden').attr('class', 'div-with-grades');
    }
    
    else {
             
        $("#notice-" + noticeid + '>.div-with-grades').attr('class', 'div-with-grades-hidden');
        
    }
}