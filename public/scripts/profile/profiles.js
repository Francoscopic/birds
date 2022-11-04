
function notes_small_menu() {

    var ellipsis = $('.nts-show-menu-profiles'),
        close_exit = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container = $('.nts-host-menu');
    
    var page_assistant = $('#profile-assistant'),
        if_people      = page_assistant.attr('place');

        alert(if_people);

    function small_container(title, pid) {
        var ele = `
        <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
        <div class="nts-host-menu-post_details">
            <a class="a" href="#">
                <h1>${title}</h1>
            </a>
        </div>
        <div class="nts-host-menu-post-response">
            <a href="#" class="nts-delete-on-profile"><p><span class="fa fa-trash"></span> Delete</p></a>
        </div>`;
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
                $.post(`depends/profiles/profiles-activity.php`,{profile_pid:pid, profile_uid:uid}),
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
        deleteNote(post_id, viewer_id)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

$(document).ready(function(){

    notes_small_menu();
});

