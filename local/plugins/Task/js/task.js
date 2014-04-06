function mostrarBox(id) {
   
    $("#div-task-" + id + ">.input_form").toggle('fade', 300);

    $("#div-task-" + id + ">#options-task-" + id).toggle('fade', 300);

    $("#div-task-" + id + " .notice_data-text").focus();
    $("#div-task-" + id + " .notice_data-text").val($("#div-task-" + id + " .notice_data-text").val() + ' ');
}


function showConfirmDialog(id) {

    $("#div-task-" + id + ">#confirm-reject-task-" + id).toggle('fade', 300);

    $("#div-task-" + id + ">#options-task-" + id).toggle('fade', 300);

}

function hideConfirmDialog(id) {

    $("#div-task-" + id + ">#confirm-reject-task-" + id).toggle('fade', 300);

    $("#div-task-" + id + ">#options-task-" + id).toggle('fade', 300);

}

function deleteTask(id) {

    $("#div-task-" + id).toggle('fade', 300);
    $(".pending-tasks-number").text($(".pending-tasks-number").text()-1);
    $(".pending-tasks-number").css('display','none');
    $(".pending-tasks-number").toggle('fade', 800);
}

function counter(id){
    
     limit = 140;

        $("#div-task-" + id + " .notice_data-text").bind('input propertychange', function() {


        if ((limit - $(this).val().length) < 0) {
            $("#div-task-" + id + " .count").addClass('count-negative');
        }

        else {
            $("#div-task-" + id + " .count").removeClass('count-negative');
        }
        
        $("#div-task-" + id + " .count").text(limit - $(this).val().length);

    });

}

function reducir(){
    
$(".pending-tasks-number").text($(".pending-tasks-number").text()-1);
    $(".pending-tasks-number").css('display','none');
    $(".pending-tasks-number").toggle('fade', 800);
    
}