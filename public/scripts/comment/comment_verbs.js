
var comment_ready = 1;

function one_paragraph(){
    var t = $("#comment-textarea"),
        n = $("#comment-area-response"),
        a = $("#comment-submit"),
        e = $("#page-assistant");
        
    function o(t){
        $(n).text(`${t}`).fadeIn()
    }
    function c(t){
        "1" === t ? a.attr("disabled", !1).css("opacity", "1") : a.attr("disabled", !0).css("opacity", ".3")
    }
    function comment_grow(t){
        t.style.height = '9px',
        t.style.height = t.scrollHeight + "px"
    }
    
    $(t).on("keyup", function(){
        var t = $(this).val();
        comment_grow(this);
        
        !function(t, n){ 
            t >= 1 ? (o("Only one paragraph"), comment_ready=0, c("0")) : (comment_ready=1, o(""), c("1"));
            0 === n.length && (comment_ready=0, c("0"))
        }(null !== t.match(/\n/g) ? t.match(/\n/g).length : "0", t)
    }),
    1 === comment_ready && a.on("click", function(n){
        var a = e.attr("pid"),
            o = e.attr("puid"),
            r = e.attr("uid"),
            i = e.attr("name"),
            s = e.attr("uname"),
            p = t.val();
            
        $.post("depends/profiles/article/verbs.php", {com_pid:a, com_puid:o, com_uid:r, com:p}, function(n){
            "10" === $.trim(n) && (t.val(""), c("0"),
                function(t, n, a){
                    $("#comment_un_list").children("li:first-child").before(`<li id="atc-commentpg-li" class="atc-commentpg-li"><strong><a class="a" href="people.php?up=${n}">${t}.</a></strong><span>${a}</span><br><span>now</span></li>`)
                }(i,s,p)
            )
        }).fail(function(t, n, a){
            console.error(a)
        }),
        n.preventDefault()
    })
}
function share_comment() {
    var t = $(".cmt-area-textarea"),
        n = $("#comment-assistant"),
        a = $("#cmt-area-post"),
        e = n.attr("pid"),
        o = n.attr("puid"),
        c = n.attr("uid"),
        r = n.attr("name"),
        i = n.attr("uname");
    
    function s(t,n=!0,a=".5"){
        $(t).attr("disabled", n).css("opacity",a)
    }
    function p(n, a, e, o){
        $.post("depends/profiles/article/verbs.php", {com_pid:n, com_puid:a, com_uid:e, com:o}, function(n){
            "10" === $.trim(n) && (t.val(""), m(r,o))
        }).fail(function(t, n, a){
            console.error(a),
            m('<span style="color:tomato" class="sm-i">Error. Please retry</span>',"")
        })
    }
    function m(n, a){
        $("#comment_un_list").prepend(`<li id="atc-commentpg-li" class="atc-commentpg-li"><strong><a class="a" href="people.php?up=${i}">${n}.</a></strong><span>${a}</span><br><span>now</span></li>`),
        s(t, !1, 1)
    }
    t.on("keyup", function(n){
        var r = $.trim($(this).val());
        "13" == (n.keyCode ? n.keyCode : n.which) && "" != r && (s(a), s(t), p(e, o, c, r),
        n.preventDefault()),
        r.length > 0 ? s(a, !1, 1) : s(a)
    }),
    a.on("click", function(n){
        n.preventDefault();
        var a = $.trim($(t).val());
        s(t), s(this), p(e, o, c, a)
    })
}

$(document).ready(function(){
    share_comment()
});