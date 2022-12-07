
function subscribe() {
    var s = $(".unsub-assistant, .page-sub-assistant"),
        e = s.attr("puid"),
        n = s.attr("uid"),
        i = $(".followpeople-unsubs-input, .people-subs-label > input");
    var isUserAllowed = $(i).attr('visit'),
        close_exit = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container = $('.nts-host-menu');
    $(i).on("click", function(){

        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }

        var s, i;
            (function (s) {
                var e = $(s),
                    n = $(s).next("span"),
                    i = $(n).children("i");
                function a(s, e, n, i, a) {
                    $(s).removeClass(e).addClass(n), $(i).children("span").text(a);
                }
                $(e).is(":checked") ? a(i, "far", "fas", n, "SUBSCRIBED") : a(i, "fas", "far", n, "SUBSCRIBE");
            })(this),
            (s = e),
            (i = n),
            $.post("depends/profiles/people/verbs.php", { publisher_uid: s, customer_uid: i }, function () {}).fail(function (s, e, n) {
                console.error(n);
            });
    });

    function small_container_visit(){
        const ele = `
        <div class="login_to_connect">
            <div><img src="../../public/images/7.png" alt="Netintui Notes" /></div>
            <p class="nt-ft-calib" message="">Log in to interact with the world on Notes.</p>
            <p class="nt-ft-robt" action="">
                <a href="../out/aquamarine/signin.php" class="a">
                    <button>Log in</button>
                </a>
                <a href="../out/aquamarine/signup.php" class="a">
                    <button>Sign up</button>
                </a>
            </p>
        </div>`;
        small_menu_container.html(ele);
        return true;
    }
    $(close_exit).on('click', function(e){
        e.preventDefault();
        
        small_menu_parent_container.fadeOut();
    })
}
subscribe();
