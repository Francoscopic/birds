
function notes_small_menu() {

    var ellipsis = $('.nts-show-menu-profiles'),
        close_exit = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container = $('.nts-host-menu');

    function small_container(title, name, link, pid, vid) {
        const ele = `
        <span id="small-menu-assistant" class="hd" pid="${pid}" uid="${vid}"></span>
        <div class="nts-host-menu-post_details">
            <a class="a" href="${link}">
                <h1>${title}</h1>
                <p>${name}</p>
            </a>
        </div>
        <div class="nts-host-menu-post-response">
            <a href="#" class="nts-delete-on-profile"><p><span class="fa fa-trash"></span> Delete</p></a>
        </div>`;
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
            viewer_id    = $assistant.attr('uid'),
            article_link = $assistant.attr('read'),
            title        = $assistant.attr('title'),
            poster       = $assistant.attr('poster');

        (small_container(title, poster, article_link, post_id, viewer_id) == true) ? small_menu_parent_container.fadeIn() : null;
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

