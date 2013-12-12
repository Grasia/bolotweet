      
        function modoAuto() {
            
            $( ".notes-personalizado" ).addClass('disabled');
            
        }
                
        function modoManual() {
            
            $( ".notes-personalizado" ).removeClass('disabled');
            
        }
        

$(document).ready(function() {
    
    $("#radio-auto").onclick = alert('clicked');
    $("#radio-manual").onclick = alert('clicked');
    
    
});