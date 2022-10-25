
import Cookies from '/scripts/plugins/cookies/api.js';

const page_assistant = $("span#page-assistant"),
    pid  = page_assistant.attr("pid"); //post id

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

function article_verbs(){ // working
    
    const nt_like = $(".article-action label[action-like]").children('input'),
        nt_unlike = $(".article-action label[action-unlike]").children('input'),
        nt_save   = $(".article-action label[action-save]").children('input'),
        nt_follow = $("label.atc-note-subscribe[follow-button]").children('input');

    function e(t){
        var i = $(t).siblings('p'),
            n = i.children('i');
        
        function e(t, i, n){
            $(t).removeClass(i).addClass(n);
        }
        
        $(t).is(":checked") ? e(n, "far", "fas") : e(n, "fas", "far");
    }
    
    function s(t, e){
        $.post("/ajax/verb/article/verbs/",{thePid:t, theReason:e},function(){
            
        }).fail(function(t, i, n){
            console.error(n)
        })
    }
    $(nt_like).on("click", function(){
        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }
        e(this),
        s(pid, "like");
        $(nt_unlike).is(":checked") ? (nt_unlike.prop('checked', false), e(nt_unlike), s(pid, 'unlike')) : null;
    }),
    $(nt_unlike).on("click", function(){
        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }
        e(this),
        s(pid, "unlike");
        $(nt_like).is(":checked") ? (nt_like.prop('checked', false), e(nt_like), s(pid, 'like')) : null;
    }),
    $(nt_save).on("click", function(){
        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }
        e(this);
        s(pid, "save");
    }),
    $(nt_follow).on("click", function(){

        // Check if user is allowed to interact with community
        if( isUserAllowed == true ) {
            (small_container_visit() == true) ? small_menu_parent_container.fadeIn() : null;
            return;
        }

        function responseText(t){
            var i = $(t).siblings('p');
            function ct(c, t, cl='follow_state'){
                $(c).text(t)
            }
            $(t).is(":checked") ? ct(i, "SUBSCRIBED") : ct(i, "SUBSCRIBE");
        }
        function sn(a, b){
            $.post("/ajax/verb/article/follows/",{thePid:a, theReason:b}, function(){
                
            }).fail(function(t, i, n){
                console.error(n)
            })
        }
        responseText(this);
        sn(pid, 'follow')
    }),
    close_exit.on('click', function(e){
        e.preventDefault();
        small_menu_parent_container.fadeOut();
    })
}

$.comment_grow = function comment_grow(t){
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
        function comment_grow(t){
            t.style.height = "8px",
            t.style.height = t.scrollHeight+"px"
        }
        
    t.on("keyup", function(i){
        comment_grow(this);
        
        var o = $.trim($(this).val());
        "13" == (i.keyCode ? i.keyCode : i.which) && "" != o && (c(n), c(t), r(e, s, a, o), i.preventDefault()),
        o.length > 0 ? c(n, !1, 1) : c(n)
    }),
    n.on("click", function(i){
        i.preventDefault();
        var n = $.trim($(t).val());
        c(t),
        c(this),
        r(e, s, a, n)
    })
}


// Monitor where users are sharing articles to: 
// Facebook, Twitter, LinkedIn, or link-copy
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
    share_comment(),
    monitor_out_share(),
    monitor_in_share()

});