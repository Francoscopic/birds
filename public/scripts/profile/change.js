
var coverIsChanged   = 0,
    displayIsChanged = 0;

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

function save_change() {
    var a = $(".check-submit");
    $(a).on("click", function(a){
        update_account(),
        a.preventDefault()
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