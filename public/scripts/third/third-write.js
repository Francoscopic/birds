
var write_url = "../out/diamonds_pages/help-write.php";
var submitHandle=0,
    validateHandle=0,
    submitButton=$("#write-submit");

function writeTitle(){
    var e=$("#write-title"),
        t=$("#title-response"),
        n=$("#focusForTitle");
    function a(e,n){
        $(t).css("color",e).text(`${n}`)
    }
    function i(e,t){
        e.css("border-bottom-color",t)
    }
    function k() {
        return $(e).focusin(function () {
            i(n, '#505050');
        }).focusout(function () {
            i(n, '#e0e0e0');
        });
    }
    $(e).keyup(function(){
        !function(e,t,o,r){
            e>100 ? (a("tomato", `${e-o} off`), i(n, "tomato"), submit_handle(0)) :
            100==e ? (a("#909090", "Wedge"), i(n, "#505050"), submit_handle(1)) :
            (a("#909090", `${o-e} left`), i(n, "#505050"), submit_handle(1))
        }($(this).val().length, 0, 100)
    }),
    k()
}
function writeIdea(){
    var e=$("#wrt-parags-edit"),
        t=$("#wrt-editor-err"),
        n=1,
        a=$("#focusForNotes");
    function i(e,n=""){
        t.html(`<span class="${n}">${e}</span>`)
    }
    function o(e,t){
        e.css("border-bottom-color",t)
    }
    function k(){
        return $(e).focusin(function () {
            o(a, '#505050');
        }).focusout(function () {
            o(a, '#e0e0e0');
        });
    }
    autosize(e),
    $(e).on("keyup",function(e){
        var t, r;
        n = $(this).val().split("\n").length,
        r = 10,
        ((t=n) >= 1 && t<=r) ?
            (i(`${t} / ${r} paragraphs`), o(a, "#505050"), submit_handle(1)) :
            t>r ?
            (i(`${t} / ${r} paragraphs`, "note-er"), o(a, "tomato"), submit_handle(0)) :
            (i("Strange situation. Please refresh page", "note-er"), submit_handle(0))
    }),
    k()
}

function preview_note(){
    const e=$("#write-submit"),
        t = $("#write-save-submit"),
        n = $("#write-title"),
        a = $("#wrt-parags-edit"),
        i = $("#focusForTitle"),
        o = $("#focusForNotes"),
        r = $(".write-title-textarea-error");
    var s,
        c,
        l = 2;
    function u(e=null,...t){
        for(var n=0; n<t.length; n++) null != e ? t[n].attr({disabled:e}).css("background-color","transparent") : t[n].fadeToggle("slow")
    }
    (c = e).on("click",function(e){
        u(null,i,o,r,t),
        u(s=l%2==0,n,a),
        function(e,t){
            1 == e ? t.html('<span class="lg-i"><i class="lg-i fa fa-arrow-left"></i> EDIT</span>') : t.html('<span class="lg-i">PREVIEW <i class="lg-i fa fa-arrow-right"></i></span>')
        }(s,c),
        l++,
        e.preventDefault()
    })
}
function submit_handle(e=null){
    var t = $("#wrt-parags-edit").val(),
        n = $("#write-title").val();
    function a(e, a=null){
        (t.length > e && "" != n && null == a) || ('' != n && t.length > e && null==a) ? i(!1,1) : i(!0, .5)
    }
    function i(e,t){
        submitButton.attr("disabled", e).css("opacity",`${t}`)
    }
    "1"==e ? (validateHandle=1, submitHandle=1, a(100)) : (validateHandle=0, submitHandle=0, a(100,0))
}
function saveEditing(){
    var e = $("#write-save-submit");
    function t(t,n){
        $(e).attr("disabled",t).css("opacity",n)
    }
    function n(e){
        if(e.lengthComputable){
            var t = e.total,
            n = e.loaded,
            a = Math.round(100*n/t),
            i = $("#write-save-submit");
            i.text(`Uploading: ${a}%`),
            a >= 100 && i.text("Finessing...")
        }
    }
    function a(e,t="#505050"){
        const n = $("#change-notification"),
            a = $("#change-notification-p");
        n.css("background-color",t).slideDown(),
        $(a).html(`${e}`)
    }
    $(e).on("click",function(i){
        t(!0,".5"); // !0 == true, !1 == false
        var o = function(e,t,k) {
            var n = $("form").get(0),
                a = new FormData(n);
                return a.append("nt",e),
                a.append("ttl",t),
                a.append("ssn",k),
                a
        }($("#wrt-parags-edit").val(), $("#write-title").val(), $('#seb-sections').val());

        $.ajax({
            url: write_url,
            type: "POST",
            data: o,
            xhr:function(){
                var e = $.ajaxSettings.xhr();
                e.upload && e.upload.addEventListener("progress", n, !1);
                return e
            },
            contentType: !1,
            cache: !1,
            processData: !1,
            success: function(n){
                !function(n){
                    ("10" === n.trim()) ?
                    (
                        a("Success","mediumseagreen"),
                        (t(!1,"1"), e.text('SHARE'), $('#help-form')[0].reset()),
                        i="help.php",
                        $(location).attr("href",i)
                    ) :
                    (
                        a(n),
                        t(!1,"1"),
                        e.text(n)
                        // e.text("Retry")
                    );
                    var i
                }(n)
            },
            error: function(n,i,o){
                a("Funny error. Retry.","tomato"),
                t(!1,"1"),
                e.text("SUBMIT"),
                console.error(o)
            }
        }),
        i.preventDefault()
    })
}

writeTitle(),
writeIdea(),
preview_note(),
saveEditing();
