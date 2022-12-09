
function notes_small_menu() {

    var ellipsis                    = $('.nts-show-menu-profiles'),
        close_exit                  = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container        = $('.nts-host-menu');
    
    var page_assistant = $('#profile-assistant'),
        if_people      = page_assistant.attr('place');

    function small_container(title, pid) {
        var ele = `
        <section class="nts-host-menu-plate">
            <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
            <div class="nts-host-menu-post_details">
                <a class="a" href="#">
                    <h1>${title}</h1>
                </a>
            </div>
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
    function deleteNote(pid, uid) {
        var trigger = $('.nts-delete-on-profile');

        trigger.on('click', function(e){
            e.preventDefault();

           var confirmDelete = confirm("Confirm delete");

           (confirmDelete == true) ? 
           (
                $.post(`depends/profiles/profiles-activity.php`,{profile_pid:pid}),
                close_exit.click()
            ) : 
            (
                close_exit.click()
            )
        })
    }

    ellipsis.on('click', function(e){
        e.preventDefault();

        var $assistant   = $(this).parents('.nts-host').children('#page-assistant'), 
            post_id      = $assistant.attr('pid'),
            title        = $assistant.attr('title');

        (small_container(title, post_id) == true) ? small_menu_parent_container.fadeIn() : null;
        deleteNote(post_id)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

// below: WORKING
function history_small_menu() {
    var ellipsis = $('.nts-show-menu-history'),
        close_exit = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container = $('.nts-host-menu');

    function small_container(title, name, pid) {
        const ele = `
        <section class="nts-host-menu-plate">
            <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
            <div class="nts-host-menu-post_details">
                <a class="a">
                    <h1>${title}</h1>
                    <p>${name}</p>
                </a>
            </div>
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
            post_id      = $assistant.attr('pid'),
            title        = $assistant.attr('title'),
            poster       = $assistant.attr('poster');

        (small_container(title, poster, post_id) == true) ? small_menu_parent_container.fadeIn() : null;
        removeHistory(this, post_id)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

$(document).ready(function(){

    notes_small_menu(),
    history_small_menu()
});

