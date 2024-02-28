
var write_url = '/ajax/verb/desk/save_draft/';
var submitHandle   = 0,
    coverSelected  = 0,
    validateHandle = 0,
    submitButton   = $("#write-submit");
var privateNote = 0; // 0 == public, 1 == private

function writeCover(){
    var e=$("#cover-response"),
        t=$("#wrt-cover-hero"),
        n=$("#write-cover-input"),
        a=$("#wrt-cover-del"),
        i=$("#write-display-action"),
        o=$(i).parent();
    function r(e){
        var n = e.target.result;
        c(),
        s("#505050","Loaded"),
        $(t).css("background-image",`url(${n})`).slideDown("slow"),
        coverSelected=1,
        submit_handle(1)
    }
    function s(t,n){
        $(e).text(`${n}`)
    }
    function c(){
        o.fadeToggle("slow"),
        a.fadeToggle("slow")
    }
    $(i).on('click',function(e){
        s('#505050','Opening to select...'),
        $(n).click(),
        e.preventDefault()
    }),
    jQuery(function(){
        $(n).change(function(){
            var n = this.files[0],
            a = n.type,
            i = ['image/jpeg','image/png','image/jpg','image/gif'];
            if(a===i[0] || a===i[1] || a===i[2] || a===i[3]) {
                s('#505050','Loading image...');
                var o = new FileReader;
                o.onload = r,
                o.readAsDataURL(n)
            }
            else t.css('background-color','#f0f0f0'),
            e.css('color','tomato').text('Image type is not supported')
        })
    }),
    jQuery(function(){
        $(a).on("click",function(e){
            c(),
            $(n).val(""),
            $(t).css("background-image","url()"),
            coverSelected = 0,
            s("#505050","Removed"),
            e.preventDefault()
        })
    }),
    function(){
        const e=$("#wrt-cover-div"),
        t=$("#write-add-cover-show"),
        n=$("#write-add-cover-hide");

        function i(e=null,a=null){
            1 == e && ($(t).addClass("write-add-cover-span-clicks-active"),
            $(n).removeClass("write-add-cover-span-clicks-active")),
            1 == a && ($(t).removeClass("write-add-cover-span-clicks-active"),
            $(n).addClass("write-add-cover-span-clicks-active"))
        }
        $(t).on("click",function(t){
            $(e).slideDown("ease-in"),
            i(1,null),
            t.preventDefault()
        }),
        $(n).on("click",function(t){
            $(e).slideUp("ease-in"),
            i(null,1),
            0 != coverSelected && $(a).click(),
            t.preventDefault()
        })
    }()
}
function writeTitle(){
    var e=$("#write-title"),
        t=$("#title-response"),
        n=$("#focusForTitle");
    autosize(e);
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
        r = 7,
        ((t=n) >= 1 && t<=r) ?
            (i(`${t} / ${r} paragraphs`), o(a, "#505050"), submit_handle(1)) :
            t>r ?
            (i(`${t} / ${r} paragraphs`, "note-er"), o(a, "tomato"), submit_handle(0)) :
            (i("Strange situation. Please retry", "note-er"), submit_handle(0))
    }),
    k()
}


function tools(){

    function save_draft(){
        var trigger = $('.feature-tools-draft'),
            nt_draft_id = Math.round(Math.exp(Math.random() * 20));

        $(trigger).on('click', (e) => {
            e.preventDefault();

            // get current values of work
            var nt_title = $("#write-title").val(),
                nt_body = $("#wrt-parags-edit").val();

            (nt_title.trim()=='' && nt_body.trim()=='') ? alert('Nothing to save') : (save_draft_now(nt_title, nt_body));
        });

        function save_draft_now(nt_title, nt_body) {
            $.post(write_url, { desk_save_draft:'', desk_save_pid:nt_draft_id, desk_save_title:nt_title, desk_save_body:nt_body }, (res) => {
                (500 === res.status) ? alert(`${res.message}`) : alert(`${res.message}`)
            }).fail((a, b, c) => {
                console.error('Error occured')
            });
        }
    }
    save_draft();

    // Convenience
    function fix_tools_to_top(){
        var the_tools_header = $('.write-header-features-tools > ul'),
            the_tools_closer = $('.feature-tools-header-close > p'),
            the_handle = 0;

        $(window).on('scroll', function(){
            if( ($(window).scrollTop() >= 100) ) {
                if(the_handle == 0) tools_header_response(the_tools_header, the_tools_closer, 'show');
                return
            }
            if(the_handle == 1 || the_handle == 2) tools_header_response(the_tools_header, the_tools_closer, 'hide');
        });

        function tools_header_response(a, b, c='show') {
            if( c == 'show' ) {
                $(a).addClass('sticky-header-tools').fadeIn(),
                $(b).removeClass('hd').fadeIn(),
                $(b).children('i').removeClass('fa-plus').addClass('fa-close'),
                the_handle = 1;
                return
            }
            if( c == 'fold' ) {
                $(a).removeClass('sticky-header-tools'),
                $(b).children('i').removeClass('fa-close').addClass('fa-plus'),
                the_handle = 2;
                return
            }
            if( c == 'hide' ) {
                $(a).removeClass('sticky-header-tools'),
                $(b).addClass('hd').fadeOut(),
                the_handle = 0;
                return
            }
            // float
            $(a).removeClass('sticky-header-tools'),
            the_handle = 0
        }

        /*
        function close_tools_header(handle=1){
            $(the_tools_closer).on('click',function(e){
                e.preventDefault();

                if( the_handle == 1 ) {
                    tools_header_response(the_tools_header, the_tools_closer, 'fold');
                    return
                }
                tools_header_response(the_tools_header, the_tools_closer, 'show')
            })
        }
        close_tools_header(); */
    }
    fix_tools_to_top();
}

//Outsider
function select_privacy(){

    var featureHandle = $('a.feature-tools-selector');

    function a(handle) {
        // return the type of Article: Public/Private
        var theButton      = $(handle).children('p'),
            theButtonPublic  = $('.write-selector-public'),
            theButtonPrivate = $('.write-selector-private');

        (theButton.attr('type') == 'public')
            ? ( c(theButtonPublic, theButtonPrivate), privateNote=0)
            : ( c(theButtonPrivate, theButtonPublic), privateNote=1);
    }

    function c(but1, but2) {
        // Make the transition to appropriate button.
        $(but1).addClass('feature-tools-selector-active'),
        $(but2).removeClass('feature-tools-selector-active')
    }

    selectType_Master();
    function selectType_Master() {
        $(featureHandle).on('click', function (e) {
            e.preventDefault();

            a(this);
        });
    }
}
function preview_note(){
    const e=$("#write-submit"),
        t = $("#write-save-submit"),
        n = $("#write-title"),
        a = $("#wrt-parags-edit"),
        i = $("#focusForTitle"),
        o = $("#focusForNotes"),
        r = $(".write-title-textarea-error"),
        ab = $('.wrt-select-photos-but');
    var s,
        c,
        l = 2;
    function u(e=null,...t){
        for(var n=0;n<t.length;n++) null != e ? t[n].attr({disabled:e}).css("background-color","transparent") : t[n].fadeToggle("slow")
    }
    (c = e).on("click",function(e){
        u(null,i,o,r,t,ab),
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
    function a(e,a=null){
        (t.length > e && "" != n && 1 == coverSelected && null == a) ? i(!1,1) : i(!0, .5)
    }
    function i(e,t){
        submitButton.attr("disabled", e).css("opacity",`${t}`)
    }
    "1"==e ? (validateHandle=1,submitHandle=1,a(100)) : (validateHandle=0,submitHandle=0,a(100,0))
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
                a.append('prv',k),
                a.append('mydesk',''),
                a
        }($("#wrt-parags-edit").val(), $("#write-title").val(), privateNote);

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
            success: function(res){
                '200' == res.status ?
                    (a(res.message, 'mediumseagreen'), t(!0, '.5'), i='/', $(location).attr('href',i)) :
                    (a(n), t(!1, '1'), e.text(res.message));
                    var i
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


$(document).ready(function(){

    writeTitle(),
    writeCover(),
    writeIdea(),
    preview_note(),
    saveEditing(),
    select_privacy();

    tools();
});
