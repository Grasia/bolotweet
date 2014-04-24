function changeComboTag(groupid) {

    var tag = $('#notes-combo-hashtag').find(":selected").text();
    var userid = $('#notes-combo-user').find(":selected").text();
    var grade = $('#notes-combo-grade').find(":selected").text();

    $('#notes-combo-user').load("../../local/plugins/NotesPDF/scripts/updateBoxUser.php", {groupid: groupid, grade: grade, tag: tag}, function() {

        if (userid !== 'Todos') {
            $("#notes-combo-user option[value=" + userid + "]").attr("selected", "selected");
        }

    });

    $('#notes-combo-grade').load("../../local/plugins/NotesPDF/scripts/updateBoxGrade.php", {groupid: groupid, userid: userid, tag: tag}, function() {

        if (grade !== 'Todos') {
            $("#notes-combo-grade option[value=" + grade + "]").attr("selected", "selected");
        }
    });


}

function changeComboUser(groupid) {

    var tag = $('#notes-combo-hashtag').find(":selected").text();
    var userid = $('#notes-combo-user').find(":selected").text();
    var grade = $('#notes-combo-grade').find(":selected").text();

    $('#notes-combo-hashtag').load("../../local/plugins/NotesPDF/scripts/updateBoxTags.php", {groupid: groupid, userid: userid, grade: grade}, function() {

        if (tag !== 'Todos') {
            $("#notes-combo-hashtag option[value=" + tag + "]").attr("selected", "selected");
        }
    });



    $('#notes-combo-grade').load("../../local/plugins/NotesPDF/scripts/updateBoxGrade.php", {groupid: groupid, userid: userid, tag: tag}, function() {

        if (grade !== 'Todos') {
            $("#notes-combo-grade option[value=" + grade + "]").attr("selected", "selected");
            //alert();
        }
    });


}

function changeComboGrade(groupid) {

    var tag = $('#notes-combo-hashtag').find(":selected").text();
    var userid = $('#notes-combo-user').find(":selected").text();
    var grade = $('#notes-combo-grade').find(":selected").text();

    $('#notes-combo-user').load("../../local/plugins/NotesPDF/scripts/updateBoxUser.php", {groupid: groupid, grade: grade, tag: tag}, function() {
       
        if (userid !== 'Todos') {
            $("#notes-combo-user option[value=" + userid + "]").attr("selected", "selected");
        }
    });


    $('#notes-combo-hashtag').load("../../local/plugins/NotesPDF/scripts/updateBoxTags.php", {groupid: groupid, userid: userid, grade: grade}, function() {

        if (tag !== 'Todos') {
            $("#notes-combo-hashtag option[value=" + tag + "]").attr("selected", "selected");
        }
    });


}





