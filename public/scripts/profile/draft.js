

function actions() {

    function draft_delete() {
        var trigger = $('.draft-box-c-delete');

        trigger.on('click', function(e){
            e.preventDefault();

            var $assistant = $(this).parents('.draft-box').children('span#draft-assistant'),
                draft_pid = $assistant.attr('pid');

            var confirmDelete = confirm("Confirm delete");
            (confirmDelete == true) ? (query_db(draft_pid), delete_feedback(this)) : null;
        });
        function query_db(pid) {
            $.post('/ajax/verb/draft/delete_draft/',{draft_delete:'',draft_pid:pid}, function(){
                
            }).fail(function(a,b,er){
                console.error(er)
            })
        }
        function delete_feedback(ele) {
            $(ele).parents('.draft-box').slideUp()
        }
    }
    draft_delete();
}


$(document).ready(function(){

    actions();

});