function editarNota(id){
    
    $("#div-grades-hidden-"+id).attr('class','notice-grades');
    
    $("#button-modify-grade-"+id).attr('class','notice-modify-grade-hidden');
}

function puntuarNota(noticeid, value){
    
     $("#notice-"+noticeid+'>.notice-grades').attr('class','notice-grades-hidden');
    
     $("#notice-"+noticeid).append('<p class="temp-text-grades">Puntuado con un: ' + value + '</p>');
      
}