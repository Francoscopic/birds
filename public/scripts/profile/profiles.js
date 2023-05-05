

// Working
function saved_menu() {
    
    var ellipsis                    = $('.nts-show-menu-saved'),
        close_exit                  = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container        = $('.nts-host-menu');

    function small_container(pid) {
        const ele = `
        <section class="nts-host-menu-plate">
            <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
            <div class="nts-host-menu-post-response">
                <a href="#" class="nts-remove-on-saved"><p><span class="fa-solid fa-bookmark"></span> Click to remove bookmark</p></a>
            </div>
        </section>`;
        small_menu_container.html(ele);
        return true;
    }
    function removeSaved(this_trigger, pid) {
        var trigger = $('.nts-remove-on-saved');

        trigger.on('click', function(e){
            e.preventDefault();

            var confirmDelete = confirm("Confirm to remove");

            (confirmDelete == true) ? 
                (
                    removeFeedback(this_trigger),
                    $.post('/ajax/verb/saved/remove_saved/', {saved_remove:'', saved_remove_pid:pid}, function(){
                        
                    }).fail(function(a,b,er){console.error(er)}),
                    close_exit.click()
                ) : 
                (
                    close_exit.click()
                )
        });
        function removeFeedback(trigger) {
            $(trigger).parents('.nts-host').fadeOut();
        }
    }

    ellipsis.on('click', function(e){
        e.preventDefault();

        var $assistant   = $(this).parents('.nts-host').children('#page-assistant'), 
            post_id      = $assistant.attr('pid');

        (small_container(post_id) == true) ? small_menu_parent_container.fadeIn() : null;
        removeSaved(this, post_id)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

// Working
function draft_verbs() {

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

// Working
function profile_notes_small_menu() {

    var ellipsis                    = $('.nts-show-menu-profiles'),
        close_exit                  = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container        = $('.nts-host-menu');
    
    var page_assistant = $('#profile-assistant'),
        if_people      = page_assistant.attr('place');

    function small_container(pid) {
        var ele = `
        <section class="nts-host-menu-plate">
            <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
            <div class="nts-host-menu-post-response">
                <a href="#" class="nts-delete-on-profile"><p><span class="fa-solid fa-eye"></span> Hide this article</p></a>
            </div>
        </section>`;
        if(if_people == 'people') {
            ele = `
            <div class="nts-host-menu-post_details">
                <a class="a">
                    <h1>Coming soon..</h1>
                </a>
            </div>
            <div class="nts-host-menu-post-response">
                
            </div>`;
        }
        small_menu_container.html(ele);
        return true;
    }
    function deleteNote(the_canvas, pid) {
        var trigger = $('.nts-delete-on-profile');

        trigger.on('click', function(e){
            e.preventDefault();

           var confirmDelete = confirm("Confirm to continue");

           (confirmDelete == true) ? 
           (
                $.post('/ajax/verb/profile/hide_article/',{hide_article:'',profile_pid:pid}).fail( function(a,b,er){console.error(er)} ),
                removeFeedback(the_canvas),
                close_exit.click()
            ) : 
            (
                close_exit.click()
            )
        });
        function removeFeedback(canvas) {
            $(canvas).parents('.nts-host-verb-author').prepend(`<a class="a"><p>[Removed]</p></a>`);
        }
    }

    ellipsis.on('click', function(e){
        e.preventDefault();

        var $assistant   = $(this).parents('.nts-host').children('#page-assistant'), 
            post_id      = $assistant.attr('pid');

        (small_container(post_id) == true) ? small_menu_parent_container.fadeIn() : null;
        deleteNote(this, post_id)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

// Working
function history_small_menu() {
    var ellipsis = $('.nts-show-menu-history'),
        close_exit = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container = $('.nts-host-menu');

    function small_container(pid) {
        const ele = `
        <section class="nts-host-menu-plate">
            <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
            <div class="nts-host-menu-post-response">
                <a href="#" class="nts-remove-on-history"><p><span class="fa-solid fa-trash-can"></span> Remove article from history</p></a>
            </div>
        </section>`;
        small_menu_container.html(ele);
        return true;
    }
    function removeHistory(this_trigger, pid) {
        var trigger = $('.nts-remove-on-history');

        trigger.on('click', function(e){
            e.preventDefault();

            var confirmDelete = confirm("Confirm to remove");

            (confirmDelete == true) ? 
                (
                    $.post('/ajax/verb/article/views/',{removeVisit:'',remove_pid:pid},function(){
                        
                    }).fail( function(a,b,er){console.error(er)} ),
                    removeFeedback(this_trigger),
                    close_exit.click()
                ) : 
                (
                    close_exit.click()
                )
        });
        function removeFeedback(trigger) {
            $(trigger).parents('.nts-host').fadeOut();
        }
    }

    ellipsis.on('click', function(e){
        e.preventDefault();

        var $assistant   = $(this).parents('.nts-host').children('#page-assistant'), 
            post_id      = $assistant.attr('pid');

        (small_container(post_id) == true) ? small_menu_parent_container.fadeIn() : null;
        removeHistory(this, post_id)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

$(document).ready(function(){

    profile_notes_small_menu(),
    history_small_menu(),
    draft_verbs(),
    saved_menu()
});

