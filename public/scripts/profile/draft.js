

function actions() {

    function draft_delete() {
        var trigger = $('.draft-box-c-delete');

        trigger.on('click', function(e){
            e.preventDefault();

            var $assistant = $(this).parents('.draft-box').children('span#draft-assistant'),
                draft_pid = $assistant.attr('pid'),
                draft_uid = $assistant.attr('uid');

            var confirmDelete = confirm("Confirm delete");
            (confirmDelete == true) ? query_db(draft_pid, draft_uid, $(this)) : false;
        });
        function query_db(pid, uid, ele) {

            $.post('depends/profiles/profiles-activity.php',{draft_pid:pid, draft_uid:uid}, function(data){
                ('13' == data.trim()) ? delete_feedback(ele) : console.error(data);
            }).fail(function(a,b,er){
                console.error(er)
            })
        }
        function delete_feedback(ele) {
            ele.parents('.draft-box').slideUp()
        }
    }
    draft_delete();
}


$(document).ready(function(){

    actions();

});