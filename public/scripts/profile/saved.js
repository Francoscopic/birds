
saved_menu();
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
                    $.post(`depends/profiles/profiles-activity.php`,{saved_remove:'', saved_del_pid:pid},function(res){
                        '13'==res.trim() ? removeFeedback(this_trigger) : null;
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