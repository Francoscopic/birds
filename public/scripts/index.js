
import Cookies from '/scripts/plugins/cookies/api.js';

function article_click() {  // working

    var e = $(".vw-anchor-pages");
    $(e).on("click", function () {

        var e = $(this).siblings("#page-assistant");
        as();

        function as() {
            !(function (en) {
                $.post("/ajax/verb/home/article_click/", { views: '1', note_id: en, viewer_id: '' }, function(){

                }).fail(function(t,e,n){
                    console.error(n)
                })
            })(e.attr("pid"));
        }
    });
}

$.nt_small_menu = function () // working
{

    var ellipsis = $('.nts-show-menu'), 
        isUserAllowed = $(ellipsis).attr('visit'), 
        close_exit = $('.note-small-menu-container-close'), 
        small_menu_parent_container = $('.notes-small-menu-container'), 
        small_menu = $('.nts-host-menu');

    function small_container(title, name, link, pid, save_state, like_state, unlike_state) {
        var is_saved = is_done(save_state), is_liked = is_done(like_state), is_unliked = is_done(unlike_state);
        const ele = `
        <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
        <div class="nts-host-menu-post_details">
            <a class="a" href="${link}">
                <h1>${title}</h1>
                <p>${name}</p>
            </a>
        </div>
        <div class="nts-host-menu-post-response">
            <a href="#" class="save-this-note"><p><i class="${is_saved} fa-bookmark"></i> Save for later</p></a>
            <a href="#" class="report-this-note"><p><i class="far fa-flag"></i> Report content</p></a>
            <div class="two-clicks">
                <a href="#" class="like-this-note">
                    <p>
                        <i class="${is_liked} fa-thumbs-up" action-icon></i>
                        <span response-text>Like</span>
                    </p>
                </a>
                <a>
                    <p>|</p>
                </a>
                <a href="#" class="unlike-this-note">
                    <p>
                        <i class="${is_unliked} fa-thumbs-down" action-icon></i>
                        <span response-text>Dislike</span>
                    </p>
                </a>
            </div>
        </div>`;
        call_menu(ele);
        return true;

        function is_done(state) {
            return (state == 1) ? 'fas' : 'far';
        }
    }
    function small_container_visit() {
        const ele = `
        <div class="login_to_connect">
            <div><img src="/images/support/7.png" alt="Netintui Notes" /></div>
            <p class="nt-ft-calib" message="">Log in to interact with the world on Notes.</p>
            <p class="nt-ft-robt" action="">
                <a href="/signin" class="a">
                    <button>Log in</button>
                </a>
                <a href="/signup" class="a">
                    <button>Sign up</button>
                </a>
            </p>
        </div>`;
        call_menu(ele);
        return true;
    }
    function call_menu(ele) {
        small_menu.empty(),
            small_menu.html(`<div class="nts-host-menu-plate">${ele}</div>`);
    }
    function tools_contractor(pid, save_ask, like_ask, unlike_ask) {

        function tools_response(t, ask) {
            var ia = $(t).find('i');

            function e(t, i, n) {
                $(t).removeClass(i).addClass(n);
            }
            (ask == 1 || ask == true) ? e(ia, "fas", "far") : e(ia, "far", "fas");
        }
        function s(t, e, other = null) {
            $.post("/ajax/verb/home/article_like/", { thePid: t, theReason: e, other: other }, function () {
                // alert(data.message)
            }).fail(function (t, i, n) {
                console.error(n);
            });
        }
        function flip_value(the_value) {
            return (the_value == 1) ? 0 : 1;
        }

        // SAVE
        var save_trigger = $('.save-this-note');
        $(save_trigger).on('click', function (e) {
            e.preventDefault();

            s(pid, 'save'),
                tools_response(this, save_ask),
                save_ask = flip_value(save_ask); // change value
        });
        // LIKE
        var like_trigger = $('.like-this-note');
        $(like_trigger).on('click', function (e) {
            e.preventDefault();

            s(pid, 'like'),
                tools_response(this, like_ask),
                like_ask = flip_value(like_ask); // change value
        });
        // UN-LIKE
        var unlike_trigger = $('.unlike-this-note');
        $(unlike_trigger).on('click', function (e) {
            e.preventDefault();

            s(pid, 'unlike'),
                tools_response(this, unlike_ask),
                unlike_ask = flip_value(unlike_ask); // change value
        });
        // REPORT
        report_contractor();
        function report_contractor() {

            function report_small_container() {
                const ele = `
                <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
                <div class="nts-host-menu-post_details">
                    <a class="a">
                        <h1>Report image or title</h1>
                        <p></p>
                    </a>
                </div>
                <div class="nts-host-menu-post-response">
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="sc" class="hd" />
                        <p><i class="far fa-flag"></i> Sexual content</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="vrc" class="hd" />
                        <p><i class="far fa-flag"></i> Violent or repulsive content</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="hac" class="hd" />
                        <p><i class="far fa-flag"></i> Hateful or abusive content</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="sm" class="hd" />
                        <p><i class="far fa-flag"></i> Spam or misleading</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="ca" class="hd" />
                        <p><i class="far fa-flag"></i> Child abuse</p>
                    </label>
                    <div class="two-clicks">
                        <a href="#" class="small-report-cancel">
                            <p>
                                <i class="fas fa-times" action-icon></i>
                                <span response-text>Cancel</span>
                            </p>
                        </a>
                        <a>
                            <p>|</p>
                        </a>
                        <a href="#" class="small-report-send">
                            <p>
                                <i class="fas fa-arrow-right" action-icon></i>
                                <span response-text>Report</span>
                            </p>
                        </a>
                    </div>
                </div>`;
                call_menu(ele);
                return true;
            }
            function report_tools_response(t, ask) {
                var ia = $(t).find('i');

                function e(t, i, n) {
                    $(t).removeClass(i).addClass(n);
                }
                (ask == true) ? e(ia, "far", "fas") : e(ia, "fas", "far");
            }
            function report_actions() {

                // cancel
                var report_cancel = $('.small-report-cancel');
                $(report_cancel).on('click', function (e) {
                    e.preventDefault();
                    close_exit.click();
                });
                // send
                var report_send = $('.small-report-send');
                $(report_send).on('click', function (e) {
                    e.preventDefault();
                    var selected_report = $('.small-report-issue input:checked').attr('value');
                    (selected_report == '') ? null : (s(pid, 'report', selected_report), close_exit.click());
                });


                // select issue
                var report_selector = $('.small-report-issue input');
                $(report_selector).on('click', function () {

                    var this_checkbox = $(this).is(':checked'), others_checkbox = $(report_selector).not(this).is(':checked'), this_i = $(this).siblings('p'), others_i = $(report_selector).siblings('p');
                    report_tools_response(others_i, others_checkbox),
                        report_tools_response(this_i, this_checkbox);
                });
            }

            // start
            var report_trigger = $('.report-this-note');
            $(report_trigger).on('click', function (e) {
                e.preventDefault();
                (report_small_container() == true) ? (small_menu_parent_container.fadeIn(), report_actions()) : null;
            });
        }
    }

    ellipsis.on('click', function (e) {
        e.preventDefault();

        if (isUserAllowed == true) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }

        var $assistant = $(this).parents('.nts-host').children('#page-assistant'), post_id = $assistant.attr('pid'), article_link = $assistant.attr('link'), title = $assistant.attr('title'), poster = $assistant.attr('poster'), save_state = $assistant.attr('save_state'), like_state = $assistant.attr('like_state'), unlike_state = $assistant.attr('unlike_state');

        (
            small_container(
                title, poster, article_link, post_id,
                save_state, like_state, unlike_state
            ) == true
        )
            ? (
                small_menu_parent_container.fadeIn(),
                tools_contractor(post_id, save_state,
                    like_state, unlike_state)
            ) : null;
    }),

    close_exit.on('click', function (e) {
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    });
}

function notes_new_menu() { // Working
    var t = $(".note-menu-open"),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu = $('.nts-host-menu');

    function menu_container(data) {

        var ele = `
            <nav id="menu-august-nav" class="menu-august-nav">
                <div class="menu-august-cover ft-sect">
                    <div class="menu-august" give-trans-bck>
                        <div class="menu-august-profile">
                            <a href="/${data.username}/">
                                <div prof-img>
                                    <img src="${data.display}" alt="${data.name}'s display picture" />
                                </div>
                                <div prof-text>
                                    <h1>${data.name}</h1>
                                    <p>@${data.username}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="menu-august" give-botm-space>
                        <div class="menu-august-profile">
                            <div class="menu-august-pages">
                                <ul give-und>
                                    <a href="/">
                                        <li>Home</li>
                                    </a>
                                    <a href="/${data.username}/">
                                        <li>Profile</li>
                                    </a>
                                    <a href="/${data.username}/saved/">
                                        <li>Saved</li>
                                    </a>
                                    <a href="/${data.username}/history/">
                                        <li>History</li>
                                    </a>
                                    <a href="/${data.username}/change/">
                                        <li>Settings</li>
                                    </a>
                                </ul>
                                <ul give-un>
                                    <a href="/support/">
                                        <li>Help & FAQ</li>
                                    </a>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="menu-august">
                        <div class="menu-august-profile">
                            <div class="menu-august-profile-mixt">
                                <label class="note-color-mode">
                                    <input type="checkbox" class="hd" name="color_mode" mode="${data.theme_state}" ${data.theme_check} />
                                    <div>
                                        <h1><i class="${data.theme_icon}"></i></h1>
                                        <p>${data.theme_text}</p>
                                    </div>
                                </label>
                                <a href="/${data.username}/signout/">
                                    <div>
                                        <h1><i class="fa-solid fa-right-from-bracket"></i></h1>
                                        <p>Log out</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>`;
        call_menu(ele),
        note_light_mode()
    }
    function get_menu(reason = 'menu') {

        $.post('/ajax/universe/menu/', {reason:reason}, function(data){

            menu_container(data.content.menu)
        }).fail(function(a,b,c){
            console.error(c)
        })
    }
    function call_loader() {
        var ele = 
        `<div class="nts-secretary">
            <img src="/images/logo/loader.gif" alt="page loader" />
        </div>`;
        call_menu(ele)
    }
    function call_menu(ele) {
        small_menu.empty(),
        small_menu.html(ele)
    }

    t.click(function (t) {
        t.preventDefault();

        call_loader();
        small_menu_parent_container.fadeIn();
        get_menu('menu');
    })
}

function note_light_mode() { // working

    var trigger = $('.note-color-mode').children('input');

    $(trigger).on('click', function() {

        var the_state = $(this).attr('mode'), //1 is DARK, 0 is LIGHT
            handle2 = $(this).is(':checked'), //checked is DARK, unchecked is LIGHT
            parent = $(this).parent('label');

        handle2 ? 
            mode_response(parent, 'fa-solid fa-sun', 'Light', 'dark', 'darkmode', 'notes-white') : 
            mode_response(parent, 'fa-solid fa-moon', 'Dark', 'light', 'lightmode', 'notes')
    });

    function mode_response(parent, icon, text, mode, theme, theme_logo) {

        $(parent).find('i').attr('class', icon),
        $(parent).find('span, p').text(text),
        update_database(mode),
        $('link[watch="theme"]').attr('href',`/stylesheets/${theme}.css`),
        $('.plain-left img').attr('src',`/images/logo/${theme_logo}.png`)
    }
    function update_database(light_or_dark) {
        $.post('/ajax/universe/theme_update/', { state:light_or_dark, uid:'' }, function(data){
            console.log(data)
        }).fail(function (t, e, n) {
            console.error(n)
        })
    }
}

$(document).ready(function () {

    (function () {
        var e = $("#note-display-dark"),
            n = $("#note-display-light"),
            i = $("#page-assistant").attr("uid"),
            o = $("#note-display-dark-about"),
            c = $("#note-display-light-about"),
            l = $(".note-menu-close"),
            a = $("#display-request-level").attr("level");
        function s(t) {
            r(t, "2" == a ? "" : "pages/in/");
        }
        function r(t, e = "") {
            $.post(`${e}depends/includes/index_changes.php`, { state: t, uid: i }, function () {}).fail(function (t, e, n) {
                // console.error(n);
            });
        }
        e.on("click", function () {
            o.attr("class", "bcg-e trn3-color"), c.attr("class", ""), t(), l.click(), s("dark");
        }),
        n.on("click", function () {
            c.attr("class", "bcg-e trn3-color"), o.attr("class", ""), t(), l.click(), s("light");
        });
    })(),
    notes_new_menu(),
    article_click(),
    $.nt_small_menu(),
    note_light_mode(),
    lozad().observe();
});
