function editarNota(id){
    
    $("#div-grades-hidden-"+id).attr('class','notice-grades');
    
    $("#button-modify-grade-"+id).attr('class','notice-modify-grade-hidden');
}

function puntuarNota(noticeid, value){
    
     $("#notice-"+noticeid+'>.notice-grades').remove();
    
     $("#notice-"+noticeid).append('<p class="temp-text-grades">Puntuado con un: ' + value + '</p>');
      
}