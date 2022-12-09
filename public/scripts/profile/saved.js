
saved_menu();
function saved_menu() {

    var ellipsis = $('.nts-show-menu-saved'),
        close_exit = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container = $('.nts-host-menu');

    function small_container(title, name, link, pid, vid) {
        const ele = `
            <span id="small-menu-assistant" class="hd" pid="${pid}" uid="${vid}"></span>
            <div class="nts-host-menu-post_details">
                <a class="a">
                    <h1>${title}</h1>
                    <p>${name}</p>
                </a>
            </div>
            <div class="nts-host-menu-post-response">
                <a href="#" class="nts-remove-on-saved"><p><span class="fa-solid fa-bookmark"></span> unsave</p></a>
            </div>`;
        small_menu_container.html(ele);
        return true;
    }
    function removeSaved(this_trigger, pid, uid) {
        var trigger = $('.nts-remove-on-saved');

        trigger.on('click', function(e){
            e.preventDefault();

            var confirmDelete = confirm("Confirm to remove");

            (confirmDelete == true) ? 
                (
                    $.post(`depends/profiles/profiles-activity.php`,{saved_del_pid:pid, saved_del_uid:uid},function(data){
                        '13'==data.trim() ? removeFeedback(this_trigger) : null;
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
            post_id      = $assistant.attr('pid'),
            viewer_id    = $assistant.attr('uid'),
            article_link = $assistant.attr('read'),
            title        = $assistant.attr('title'),
            poster       = $assistant.attr('poster');

        (small_container(title, poster, article_link, post_id, viewer_id) == true) ? small_menu_parent_container.fadeIn() : null;
        removeSaved(this, post_id, viewer_id)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}