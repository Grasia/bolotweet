var SN = { // StatusNet
         
    U: {
 
    FormGrade: function(form) {
            $.ajax({
                type: 'POST',
                dataType: 'xml',
                url: form.attr('action'),
                data: form.serialize() + '&ajax=1',
                beforeSend: function(xhr) {
                    form
                        .addClass(SN.C.S.Processing)
                        .find('.submit')
                            .addClass(SN.C.S.Disabled)
                            .attr(SN.C.S.Disabled, SN.C.S.Disabled);
                },
                error: function (xhr, textStatus, errorThrown) {
                    alert(errorThrown || textStatus);
                },
                success: function(data, textStatus) {
                    if (typeof($('p', data)[0]) != 'undefined') {
                      form_new = document._importNode($('p', data)[0], true);
                 
                    
                      form.parent().parent().find('.notice-current-grade-value').replaceWith(form_new);
                    
                      form.parent().parent().find('.notice-current-grade-empty').replaceWith(form_new);
                      form
                        .removeClass(SN.C.S.Processing)
                        .find('.submit')
                            .removeClass(SN.C.S.Disabled)
                            .removeAttr(SN.C.S.Disabled);
                    }
                    else {
                    alert('form replacement failed');
                    }
                }
            });
        },
                
                 NoticeFavor: function() { 
                     $('.form_grade').live('click', function() { SN.U.FormGrade($(this)); return false; });
        }
        
    },
        
       Init: {
           
          
        Notices: function() {
            if ($('body.user_in').length > 0) {
                SN.U.NoticeFavor();
            }

        }
        
        }
        
        };
        
    
$(document).ready(function(){
   
    if ($('#content .notices').length > 0) {
        SN.Init.Notices();
    }
            
            });