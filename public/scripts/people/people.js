
function growPeople() {
    var o = 0;
    const e = $("#grow-notes"),
        n = $("#page-assistant"),
        t = n.attr("people_uid"),
        i = n.attr("uid"),
        s = $(".people-notes-container"),
        r = $("#grow-notif");
    function a(o = "do", n = "") {
        "msg" === o && r.fadeIn().html(`<span class="note-e">${n}</span>`), e.html('<em class="sm-i">Loading...</em>');
    }
    function c() {
        setTimeout(function () {
            e.html('more <i class="fa fa-arrow-right trn3"></i>'), r.fadeOut();
        }, 5e3);
    }
    $(e).on("click", function (e) {
        (o += 9),
            a(),
            (function (o, e, n) {
                $.post("depends/grow/grow.php", { grow_people: o, uid: e, start: n, muid: i }, function (o) {
                    $.trim(o).length < 10
                        ? (a("msg", 'Oops. No more notes. <a href="pages/write.php">Share</a> yours today.'), c())
                        : (s.append(o),
                          lozad().observe(),
                          c());
                }).fail(function (o, e, n) {
                    console.error(n);
                });
            })("grow", t, o),
            e.preventDefault();
    });
}
$(document).ready(function () {
    growPeople();
});
