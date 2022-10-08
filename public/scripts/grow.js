
function growProfile(){
    var o = 0;
    const e = $("#grow-profile"),
        n = $("#grow-saved"),
        t = $("#page-assistant").attr("uid"),
        r = $("#notes-container"),
        a = $("#grow-notif");
    
    function s(e, n){
        $.post("depends/grow/grow.php", {grow_page:e, uid:t, start:o, muid:""},
        
        function(o){
            $.trim(o).length<10 ? 
            (i(n, "msg", 'Oops. No more notes. <a href="write.php">Share</a> yours today.'), c(n)) : 
            (r.append(o), lozad().observe(), article_click(), c(n))
        }).fail(function(o, e, t){
            i(n, "msg", "An error occured. Try again."),
            c(n),
            console.error(t)
        })
    }
    function i(o, e="do", n=""){
        "msg" === e && a.fadeIn().html(`<span class="note-e">${n}</span>`),
        o.html('<em class="sm-i">Loading...</em>')
    }
    function c(o){
        setTimeout(function(){
            o.html('more <i class="fa fa-arrow-right trn3"></i>'),
            a.fadeOut()
        },2e3)
    }
    $(e).on("click", function(n){
        o += 9,
        i(e),
        s("profile", e),
        n.preventDefault()
    }),
    $(n).on("click", function(e){
        o += 9,
        i(n),
        s("saved", n),
        e.preventDefault()
    })
}
growProfile();


function infinite_home() {

    var trigger = $('.infinite-home'),
        uid = $("#page-assistant").attr("uid"),
        position = 15;

    $(trigger).on('click', function(e){
        e.preventDefault();

        trigger_response(this); //immediate response
        request_more()
    });

    function request_more() {
        $.post('pages/in/depends/grow/grow.php', {grow_home:'home', uid:uid, start:position}, function(data){

            ($.trim(data).length < 10) ? trigger_empty_content(trigger) : serve_more_content(data);
        }).fail(function(o, e, n){
            trigger_response(trigger, 'Error. Please, retry', false, '1'),
            console.error(n)
        }),
        position += 15;
    }
    function serve_more_content(content) {
        var container = $('.nts-host-parent');
        container.append(content);
        trigger_response(trigger, 'Find more <i class="fa fa-chevron-right"></i>', false, '1'); //react back
        //other necessary actions
        lozad().observe(),
        article_click_home(), notes_small_menu()
    }
    function trigger_response(trig, trigText='<i>loading...</i>', trigState=true, trigOpas='.5') {

        $(trig).attr('disabled',trigState).css('opacity',trigOpas),
        $(trig).html(`${trigText}`);
    }
    function trigger_empty_content(trig, msg='That\'s all for now') {

        $(trig).attr('disabled',true).css('opacity','1'),
        $(trig).addClass('nt-infinitescroll-inactive').text(msg);
    }
}
infinite_home();