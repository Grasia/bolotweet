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
    $(".pending-tasks-number").text($(".pending-tasks-number").text() - 1);
    $(".pending-tasks-number").css('display', 'none');
    $(".pending-tasks-number").toggle('fade', 800);
}

function counter(id) {

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

function reducir() {

    $(".pending-tasks-number").text($(".pending-tasks-number").text() - 1);
    $(".pending-tasks-number").css('display', 'none');
    $(".pending-tasks-number").toggle('fade', 800);

}


function showHistorical(id) {

    $("#historical-" + id).toggle('fade', 300);

    if ($("#div-group-task-" + id + " .show-historical").text() == "▸") {

        $("#div-group-task-" + id + " .show-historical").text("▾");
    }

    else {
        $("#div-group-task-" + id + " .show-historical").text("▸");
    }

}

function updateHistorical(graderid, groupid) {

    $('#init-task-group-' + groupid).submit(function(e) {
        e.preventDefault(); // don't submit multiple times
        setTimeout(function() {

            if ($('#div-group-task-' + groupid + ' #historical-' + groupid).css('display') == 'none') {
                
                $('#div-group-task-' + groupid + ' #historical-' + groupid).load("../../local/plugins/Task/scripts/updateHistorical.php", {graderid: graderid, groupid: groupid});

            }

            else {
                $('#div-group-task-' + groupid + ' #historical-' + groupid).fadeOut("fast").load("../../local/plugins/Task/scripts/updateHistorical.php", {graderid: graderid, groupid: groupid}).fadeIn('slow');
            }

        }, 100);
    });

}

function updateTaskStatus(taskid){
    
    
$('#task-' + taskid + ' span:first').fadeOut().text($('#task-'+ taskid+ ' span:first').text() == 'Iniciada' ? 'Cancelada' : 'Iniciada').fadeIn();

    
}