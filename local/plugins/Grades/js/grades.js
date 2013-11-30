function showStream(){
    
    if($(body).attr('id')=='showstream'){
        
        var noticeReplace = document.getElementsByClassName('notice-current-grade-value');
        
        for (var i = 0; i < noticeReplace.length; ++i) {
            var pepe = noticeReplace[i].text;
            
        }
        
    }
    
}

$(document).ready(function() {
    showStream();
});