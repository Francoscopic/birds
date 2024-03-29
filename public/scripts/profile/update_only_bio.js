
var thisForm = $("#change-form"),
    user_office = $(thisForm).attr("office");

function update_only_bio(){
    var o = $("#chg-name"),
        n = $("#chg-about"),
        e = $("#chg-location"),
        t = $(o).val(),
        a = $(n).val(),
        i = $(e).val();
    $.post("../out/diamonds_pages/change.php", {nm:t, bio:a, loc:i, off:user_office}, function(o){
        "10"===o.trim() ? notify("Saved","mediumseagreen") : notify(`${o}`)
    }).fail(function(o, n, e){
        notify("Error encountered. Try again."),
        console.error(e)
    })
}

function update_only_cover_display(o, n) {
    if(""===o.val()) notify("Select your image","tomato");
    else {
        var e = $("form").get(0);
        $.ajax({
            url: `../out/diamonds_pages/change.php?${n}=${user_office}`,
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
            success: function(o){
                "10"===o.trim() ? (
                    notify("Upload success","mediumseagreen"),
                    e.reset()
                    ): notify(`${o}`)
            },
            error: function(o, n, e){
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
