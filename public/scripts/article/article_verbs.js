
import Cookies from '/scripts/plugins/cookies/api.js';

const page_assistant = $("span#page-assistant"),
    pid  = page_assistant.attr("pid"); //post id
    // puid = page_assistant.attr("puid"), //poster uid
    // uid  = page_assistant.attr("uid"); //viewer id 
var uid_user = Cookies.get('cookie_user'),
    uid_visitor = Cookies.get('vst');

// VISITOR
    var close_exit = $('.note-small-menu-container-close'),
        small_menu_parent_container = $('.notes-small-menu-container'),
        small_menu_container = $('.nts-host-menu'),
        isUserAllowed = page_assistant.attr('visit'); // boolean: Visitor?
    function small_container_visit(){
        const ele = `
        <div class="login_to_connect">
            <div><img src="/images/support/7.png" alt="Netintui Notes" /></div>
            <p class="nt-ft-calib" message="">Log in to interact with the world on Notes.</p>
            <p class="nt-ft-robt" action="">
                <a href="/o/signin/?pg=article&pid=${pid}" class="a">
                    <button>Log in</button>
                </a>
                <a href="/o/signout/?pg=article&pid=${pid}" class="a">
                    <button>Sign up</button>
                </a>
            </p>
        </div>`;
        small_menu_container.html(ele);
        return true;
    }
// VISITOR - END

function article_verbs(){
    
    const nt_like = $(".article-action label[action-like]").children('input'),
        nt_unlike = $(".article-action label[action-unlike]").children('input'),
        nt_save = $(".article-action label[action-save]").children('input'),
        nt_follow = $("label.atc-note-subscribe[follow-button]").children('input');

    function e(t){
        var i = $(t).siblings('p'),
            n = i.children('i');
        
        function e(t, i, n){
            $(t).removeClass(i).addClass(n);
        }
        
        $(t).is(":checked") ? e(n, "far", "fas") : e(n, "fas", "far");
    }
    
    function s(t, i, n, e){
        $.post("depends/profiles/article/verbs.php",{thePid:t, thePUid:i, theUid:n, theReason:e},function(){

        }).fail(function(t, i, n){
            // console.error(n)
        })
    }
    $(nt_like).on("click", function(){
        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }
        e(this),
        s(pid, puid, uid, "like");
        $(nt_unlike).is(":checked") ? (nt_unlike.prop('checked', false), e(nt_unlike), s(pid, puid, uid, 'unlike')) : null;
    }),
    $(nt_unlike).on("click", function(){
        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }
        e(this),
        s(pid, puid, uid, "unlike");
        $(nt_like).is(":checked") ? (nt_like.prop('checked', false), e(nt_like), s(pid, puid, uid, 'like')) : null;
    }),
    $(nt_save).on("click", function(){
        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }
        e(this);
        s(pid, puid, uid, "note");
    }),
    $(nt_follow).on("click", function(){

        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }

        // continue
        var t, i;
        function responseText(t){
            var i = $(t).siblings('p');
            function ct(c, t){
                $(c).text(t)
            }
            $(t).is(":checked") ? ct(i, "SUBSCRIBED") : ct(i, "SUBSCRIBE");
        }
        function sn(a, b){
            $.post("depends/profiles/people/verbs.php",{publisher_uid:a, customer_uid:b}, function(){

            }).fail(function(t, i, n){
                // console.error(n)
            })
        }
        responseText(this);
        t = puid,
        i = uid,
        sn(t, i)
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

function subscribe(){
    const t = $("#subscribe-anc"),
        i = $("#unsubscribe-anc");
        
        function n(t, i, n, e){
            t.html(`<li id="" style="color:${n}" class="nao-option-li rad50 calib" title="${e}">\n <span class=""><i class="${i}"></i> <span class="nao-option-li-text"> ${e}</span></span></li>`)
        }
        function e(t, i){
            $.post("depends/profiles/people/verbs.php",{publisher_uid:t,customer_uid:i},function(){

            }).fail(function(t, i, n){
                console.error(n)
            })
        }
        t.on("click", function(i){
            n(t, "sm-i fas fa-bell", "#505050", "SUBSCRIBED"),
            e(puid, uid),
            i.preventDefault()
        }),
        i.on("click", function(t){
            n(i, "sm-i far fa-bell", "dodgerblue", "SUBSCRIBE"),
            e(puid, uid),
            t.preventDefault()
        })
}

function comment_grow(t){
    t.style.height = "8px",
    t.style.height = t.scrollHeight+"px"
}
function share_comment(){
    var t = $(".cmt-area-textarea"),
        i = $("#comment-assistant"),
        n = $("#cmt-area-post"),
        e = i.attr("pid"),
        s = i.attr("puid"),
        a = i.attr("uid"),
        o = i.attr("name");
        
        function c(t, i=!0, n=".5"){
            $(t).attr("disabled", i).css("opacity",n)
        }
        function r(i, e, s, a){
            $.post("depends/profiles/article/verbs.php",{com_pid:i,com_puid:e,com_uid:s,com:a},function(i){
                "10" === $.trim(i) && (t.val(""), c(n), u(o, a))
            }).fail(function(t, i, n){
                console.error(n),
                u('<span style="color:tomato" class="sm-i">Error. Please retry</span>',"")}
            )
        }
        function u(i, n){
            $("#comment_un_list").prepend(`<li id="article-note-comment-park" class="nu-li ft-sect">\n <a class="a" href="comments.php?wp=${e}">\n                                <strong>${i}</strong>\n                                <span>${n}</span>\n                            </a> \n                        </li>`),
            c(t, !1, 1)
        }
        
        t.on("keyup",function(i){
            var o = $.trim($(this).val());
            "13" == (i.keyCode ? i.keyCode : i.which) && "" != o && (c(n), c(t), r(e, s, a, o), i.preventDefault()),
            o.length > 0 ? c(n, !1, 1) : c(n)
        }),
        n.on("click",function(i){
            i.preventDefault();
            var n = $.trim($(t).val());
            c(t),
            c(this),
            r(e, s, a, n)
        })
}


// Monitor where users are sharing articles to: Facebook, Twitter, LinkedIn, or link-copy
function monitor_out_share() { // working

    const $trigger_facebook = $('.sharer-facebook'),
        $trigger_twitter    = $('.sharer-twitter'),
        $trigger_linkedin   = $('.sharer-linkedin'),
        $trigger_copylink   = $('.sharer-copylink');

    function take_action(t, n){
        $.post("/ajax/verb/article/share/", {outshare_pid:t, outshare_media:n, outShare:''},function(){
            
        }).fail(function(t, i, n){
            console.error(n)
        })
    }

    $($trigger_facebook).on("click", function(){
        take_action(pid, 'facebook');
    }),
    $($trigger_twitter).on("click", function(){
        take_action(pid, 'twitter');
    }),
    $($trigger_linkedin).on("click", function(){
        take_action(pid, 'linkedin');
    }),
    $($trigger_copylink).on("click", function(){
        take_action(pid, 'link');
    })

    click_to_copy_link();
    function click_to_copy_link(){

        $('#share_link').copyOnClick({
            // disable/enable the feedback
            confirmShow: false
        }),
        $('#share_link').on('click', function(){
            alert('Copied to clipboard');
        })
    }
}
function monitor_in_share() { // working

    const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });
    function take_action(t, n){
        $.post("/ajax/verb/article/views/", {inshare_pid:t, inshare_media:n, inShare:''},function(){
        }).fail(function(t, i, n){
            console.error(n)
        })
    }

    const $trigger_media = params.media || 'inhouse';
    take_action(pid, $trigger_media);
}

$(document).ready(() => {

    article_verbs(),
    subscribe(),
    share_comment(),
    monitor_out_share(),
    monitor_in_share()

});