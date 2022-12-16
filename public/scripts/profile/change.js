
var coverIsChanged   = 0,
    displayIsChanged = 0;

var thisForm = $("#change-form"),
    user_office = $(thisForm).attr("office");

function change_cover() {
    $("#change-notification");
    var a = $("#profile-me-cover"),
        e = $("#cover-trigger"),
        n = $("#change-cover");
    function t(n){
        var t = n.target.result.trim();
        return $(a).attr("style", `background-image:url(${t})`),
            $(e).html('<i class="sm-i note-e fa fa-camera"></i>'),!1
    }
    $(e).on("click", function(a){  
        $(this).html('<span class="p">...</span>'),
        $(n).click(),
        a.preventDefault()
    }),
    
    jQuery(function(){
        $(n).change(function(a) {
            var n = this.files[0].type,
            r = ["image/jpeg", "image/png", "image/jpg"];
            if(n===r[0] || n===r[1] || n===r[2]) {
                var c=new FileReader;
                return c.onload=t,
                    c.readAsDataURL(this.files[0]),
                    coverIsChanged = 1,
                    !1
            }
            alert("Select a picture"),
            $(e).html('<i class="sm-i note-e fa fa-camera"></i>'),
            coverIsChanged=0 === coverIsChanged ? 0 : 1,
            a.preventDefault()
        })
    })
}

function change_display() {
    $("#change-notification");
    var a = $("#profile-me-display"),
        e = $("#display-trigger"),
        n = $("#change-display");
    
    function t(n){
        var t = n.target.result.trim();
        return $(a).attr("src", `${t}`),
        $(e).html('<i class="sm-i note-e fa fa-camera"></i>'),
        !1
    }
    $(e).on("click", function(a){
        $(this).html('<span class="p">...</span>'),
        $(n).click(),
        a.preventDefault()
    }),
    jQuery(function(){
        $(n).change(function(a){
            var n = this.files[0].type,
            r = ["image/jpeg", "image/png", "image/jpg"];
            if(n===r[0] || n===r[1] || n===r[2]){
                var c = new FileReader;
                return c.onload = t,
                c.readAsDataURL(this.files[0]),
                displayIsChanged = 1,
                !1
            }
            alert("Select a picture"),
            $(e).html('<i class="sm-i note-e fa fa-camera"></i>'),
            displayIsChanged=0 === displayIsChanged ? 0 : 1,
            a.preventDefault()
        })
    })
}

function update_account() {
    var a = $("#chg-name"),
        e = $("#chg-about"),
        n = $("#chg-location"),
        t = $("#change-cover"),
        r = $("#change-display"),
        c = $(a).val(),
        i = $(e).val(),
        o = $(n).val(),
        l = $(a).attr("cur_value"),
        s = $(e).attr("value"),
        p = $(n).attr("cur_value"),
        f = $("#name-error"),
        d = $("#bio-error"),
        u = $("#location-error");
    function h() {
        var a = /[^a-zA-Z0-9 ]/g.test(c),
            e = /[^a-zA-Z0-9\.\,\!\?\@\#\&\+\-\_\:\;\n\'\" ]/g.test(i),
            n = /[^a-zA-Z\, ]/g.test(o),
            t = i.length,
            r = 1;
        return a && (f.html('<span class="note-er">Special characters</span>'), r=0),
        t > 140 && (d.html('<span class="note-er">Make it really brief</span>'), r=0),
        e && (d.html('<span class="note-er">Special characters</span>'), r=0),
        n && (u.html('<span class="note-er">Special characters</span>'), r=0),
        1===r && (makeErrEmpty(f), makeErrEmpty(d), makeErrEmpty(u), !0)
    }
    function g() {
        return 0===coverIsChanged && 0===displayIsChanged ? 1 : 
            1===coverIsChanged && 0===displayIsChanged ? 2 : 
            0===coverIsChanged && 1===displayIsChanged ? 3 : 
            1===coverIsChanged && 1===displayIsChanged ? 4 : 
            void 0
    }
    c===l && i===s && o===p ? 1===g() ? notify("You haven't made any changes","tomato") : 
    2===g() ? update_only_cover_display(t, "coveroff") : 
    3===g() ? update_only_cover_display(r,"dispoff") : 
    4===g() && (update_only_cover_display(t,"coveroff"), update_only_cover_display(r,"dispoff")) : 
    (
        h(), !0===h() && (1===g() ? update_only_bio() : 
        2===g() ? (update_only_bio(), update_only_cover_display(t,"coveroff")) : 
        3===g() ? (update_only_bio(), update_only_cover_display(r,"dispoff")): 
        4===g()&&(
                    update_only_bio(), 
                    update_only_cover_display(t,"coveroff"), 
                    update_only_cover_display(r,"dispoff")
                )
            )
    )
}

function update_only_bio() {
    var o = $("#chg-name"),
        n = $("#chg-about"),
        e = $("#chg-location"),
        t = $(o).val(),
        a = $(n).val(),
        i = $(e).val();
    $.post("/ajax/verb/change/on_bio/", {change_on_bio:'', change_name:t, change_bio:a, change_loc:i}, function(res){
        res.status===500 ? notify(`${res.message}`) : notify(`${res.message}`, 'mediumseagreen')
    }).fail(function(o, n, e) {
        notify("Error encountered. Try again."),
        console.error(e)
    })
}

function update_only_cover_display(o, n) {
    if(""===o.val()) notify("Select your image", "tomato");
    else {
        var e = $("form").get(0);
        $.ajax({
            // url: `../out/diamonds_pages/change.php?${n}=${user_office}`,
            url: '/ajax/verb/change/on_cover/',
            type: "POST",
            data: new FormData(e),
            xhr: function(){
                var o = new window.XMLHttpRequest;
                return o.upload.addEventListener("progress", t, !1),
                o
            },
            contentType: !1,
            cache: !1,
            processData: !1,
            success: function(res) {
                // "10"===o.trim() ? (
                //         notify("Upload success","mediumseagreen"),
                //         e.reset()
                //     ): notify(`${o}`)
                res.status===500 ? notify(`${res.message}`) : notify(`${res.message}`, 'mediumseagreen')
            },
            error: function(o, n, e) {
                notify(e),
                console.error(e)
            }
        })
    }
    function t(o){
        if(o.lengthComputable) {
            var n = o.total,
            e = o.loaded,
            t = Math.round(100*e/n);
            notify(`Uploading: ${t}%`,"darkorange"),
            t >= 100 && notify("Finessing...","darkorange")
        }
    }
}
function notify(o, n="#505050") {
    var e = $("#change-notification"),
        t = $("#change-notification-p");
    e.css("background-color", n),
    $(t).html(`${o}`)
}

function save_change() {
    var a = $(".check-submit");
    $(a).on("click", function(e){
        update_account(),
        e.preventDefault()
    })
}
function cancel_change() {
    var a = $(".cancel-change");
    $(a).on("click", function(a){
        !0===confirm("Confirm cancel") && travel("profiles.php"),
        a.preventDefault()
    })
}
function makeErrEmpty(a) {
    $(a).html("")
}
function travel(a) {
    $(location).attr("href",a)
}

$(document).ready(function(){

    change_cover(),
    change_display(),
    update_account(),
    save_change(),
    cancel_change()
});