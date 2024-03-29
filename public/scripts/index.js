
// import Cookies from './plugins/cookies/api.js';

function nt_small_menu() {

    var ellipsis = $('.nts-show-menu'), 
        isUserAllowed = $(ellipsis).attr('visit'), 
        close_exit = $('.note-small-menu-container-close'), 
        small_menu_parent_container = $('.notes-small-menu-container'), 
        small_menu = $('.nts-host-menu');

    function small_container(link, pid, save_state, like_state, unlike_state) {
        var is_saved = is_done(save_state), 
            is_liked = is_done(like_state), 
            is_unliked = is_done(unlike_state);
        const ele = `
        <span id="small-menu-assistant" class="hd" pid="${pid}"></span>
        <div class="nts-host-menu-post-response">
            <a href="#" class="report-this-note nt-colr-2"><p>Report content</p></a>
            <a href="${link}" class=""><p>Go to article</p></a>
            <a href="#" id="share_link" data-copy-on-click="${link}" class=""><p>Copy article link</p></a>
            <a href="#" class="mute-this-note"><p>Mute this account</p></a>
            <div class="two-clicks">
                <a href="#" class="unlike-this-note">
                    <p>
                        <i class="${is_unliked} fa-thumbs-down" action-icon></i>
                        <span response-text>Dislike</span>
                    </p>
                </a>
                <a class="two-clicks-middle">
                    <p>|</p>
                </a>
                <a href="#" class="save-this-note">
                    <p>
                        <i class="${is_saved} fa-bookmark" action-icon></i>
                        <span response-text>Save</span>
                    </p>
                </a>
            </div>
        </div>`;
        call_menu(ele);
        click_to_copy_link();
        return true;

        function is_done(state) {
            return (state == 1) ? 'fas' : 'far';
        }
    }
    function small_container_visit() {
        const ele = `
        <div class="login_to_connect">
            <p class="nt-ft-calib" message="">Log in to interact with the world on Notes.</p>
            <p class="nt-ft-robt" action="">
                <a href="/o/signin/" class="a">
                    <button>Log in</button>
                </a>
                <a href="/o/signup/" class="a">
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
    function tools_contractor(pid, save_ask, like_ask, unlike_ask, clicker_elem) {

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
                // console.error(n);
            });
        }
        function flip_value(the_value) {
            return (the_value == 1) ? 0 : 1;
        }
        function hide_canvas(elem) {
             var $parent = $(elem).parents('.nts-host');
             $parent.fadeOut();
        }

        // MUTE
        var mute_trigger = $('.mute-this-note');
        $(mute_trigger).on('click', function(e){
            e.preventDefault();

            s(pid, 'mute'),
            close_all_menu(),
            hide_canvas(clicker_elem);
        });

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
                        <h1>Report</h1>
                        <p></p>
                    </a>
                </div>
                <div class="nts-host-menu-post-response">
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="sexual" class="hd" />
                        <p>Sexual content</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="violent or repulsive" class="hd" />
                        <p>Violent or repulsive content</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="hateful or abusive" class="hd" />
                        <p>Hateful or abusive content</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="spam or misleading" class="hd" />
                        <p>Spam or misleading</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="child or abuse" class="hd" />
                        <p>Child abuse</p>
                    </label>
                    <label class="small-report-issue">
                        <input type="radio" name="report_issue" value="others" class="hd" />
                        <p>Others</p>
                    </label>
                    <div class="two-clicks">
                        <a href="#" class="small-report-cancel">
                            <p>
                                <i class="fas fa-times" action-icon></i>
                                <span response-text>Cancel</span>
                            </p>
                        </a>
                        <a class="two-clicks-middle">
                            <p>|</p>
                        </a>
                        <a href="#" class="small-report-send">
                            <p>
                                <i class="fas fa-arrow-right" action-icon></i>
                                <span response-text>Send</span>
                            </p>
                        </a>
                    </div>
                </div>`;
                call_menu(ele);
                return true;
            }
            function report_tools_response(t, ask) {
                var ia = $(t).find('i');

                function e(t, i) {
                    $(t).css('text-decoration', i)
                }
                (ask == true) ? e(t, 'line-through 3px red') : e(t, 'none');
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

        var $assistant = $(this).parents('.nts-host').children('#page-assistant'), post_id = $assistant.attr('pid'), article_link = $assistant.attr('link'), save_state = $assistant.attr('save_state'), like_state = $assistant.attr('like_state'), unlike_state = $assistant.attr('unlike_state');

        (
            small_container(
                article_link, post_id,
                save_state, like_state, unlike_state
            ) == true
        )
            ? (
                small_menu_parent_container.fadeIn(),
                tools_contractor(post_id, save_state,
                    like_state, unlike_state, this)
            ) : null;
    }),

    close_exit.on('click', function (e) {
        e.preventDefault();
        close_all_menu();
    });

    function close_all_menu() {
        small_menu_parent_container.fadeOut();
    }
}

function notes_new_menu() { // Working
    var t = $(".note-menu-open"),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu = $('.nts-host-menu');

    function menu_container(data) {
        var dataList = $('#app-assistant'),
            name     = dataList.attr('nm'),
            username = dataList.attr('un'),
            display  = dataList.attr('dsp'),
            theme_check = dataList.attr('thc'),
            theme_state = dataList.attr('ths'),
            theme_icon  = dataList.attr('thi'),
            theme_text  = dataList.attr('tht');

        var ele = `
            <nav id="menu-august-nav" class="menu-august-nav">
                <div class="menu-august-cover ft-sect">
                    <div class="menu-august" give-trans-bck>
                        <div class="menu-august-profile">
                            <a href="/${username}/">
                                <div prof-img>
                                    <img src="${display}" alt="${name}'s display picture" />
                                </div>
                                <div prof-text>
                                    <h1>${name}</h1>
                                    <p>@${username}</p>
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
                                    <a href="/${username}/saved/">
                                        <li>Saved</li>
                                    </a>
                                    <a href="/${username}/history/">
                                        <li>History</li>
                                    </a>
                                    <a href="/${username}/change/">
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
                                    <input type="checkbox" class="hd" name="color_mode" mode="${theme_state}" ${theme_check} />
                                    <div>
                                        <h1><i class="${theme_icon}"></i></h1>
                                        <p>${theme_text}</p>
                                    </div>
                                </label>
                                <a href="/${username}/signout/">
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

    t.on('click', function (e) {
        e.preventDefault();

        call_loader();
        small_menu_parent_container.fadeIn();
        // get_menu('menu');
        menu_container('')
    })
}

function note_light_mode() { // working

    var trigger = $('.note-color-mode').children('input');

    $(trigger).on('click', function() {

        var the_state = $(this).attr('mode'), // 0 is DARK, 1 is LIGHT
            handle2 = $(this).is(':checked'), //checked is LIGHT, unchecked is DARK
            parent = $(this).parent('label');

        handle2 ? 
            mode_response(parent, 'fa-solid fa-moon', 'Dark', 'light', 'lightmode', 'notes') :
            mode_response(parent, 'fa-solid fa-sun', 'Light', 'dark', 'darkmode', 'notes-white')
    });

    function mode_response(parent, icon, text, mode, theme, theme_logo) {

        $(parent).find('i').attr('class', icon),
        $(parent).find('span, p').text(text),
        update_database(mode),
        $('link[watch="theme"]').attr('href',`/stylesheets/${theme}.css`),
        $('.plain-left img').attr('src',`/images/logo/${theme_logo}.png`)
    }
    function update_database(light_or_dark) {
        $.post('/ajax/universe/theme_update/', { state:light_or_dark }, function(data){
            // console.log(data)
        }).fail(function (t, e, n) {
            console.error(n)
        })
    }
}

// COPY LINK
function click_to_copy_link(){

    $('#share_link').copyOnClick({
        // disable/enable the feedback
        confirmShow: false
    }),
    $('#share_link').on('click', function(){
        alert('Copied to clipboard');
    })
}

$(document).ready(function (){

    notes_new_menu(),
    nt_small_menu(),
    click_to_copy_link(),
    lozad().observe();
});