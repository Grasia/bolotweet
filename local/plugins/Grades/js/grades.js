function showStream(){
    
        if($(document.body).attr('id')=='showstream'){
            
        var noticeReplace = document.getElementsByClassName('notice-current-grade-value');
        
        for (var i = 0; i < noticeReplace.length; ++i) {
            
            var texto = noticeReplace[i].innerHTML;
            var partes = texto.split("<br>");
            
            noticeReplace[i].innerHTML = "Puntuación: " + partes[1] + " por " + partes[0];
            
        }
        
    }
        
    
    
}

$(document).ready(function() {
    showStream();
});