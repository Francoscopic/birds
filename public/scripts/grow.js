
import $ from 'jquery'

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

function infinite_home() // working
{
    var trigger = $('.infinite-home'),
        position = 15;

    $(trigger).on('click', function(e){
        e.preventDefault();

        trigger_response(this); //immediate response
        request_more()
    });

    function request_more() {
        $.post('/ajax/universe/infinite_scroll/', {grow_home:'home', uid:'', start:position}, function(data){

            ((data.content.notes).length < 1) ? trigger_empty_content(trigger) : serve_more_content(data.content.notes, data);
        }).fail(function(o, e, n){
            trigger_response(trigger, 'Error. Please, retry', false, '1'),
            console.error(o.reponseText);
        }),
        position += 15;
    }
    function serve_more_content(content, content_parent) {
        var container = $('.nts-host-parent');
        var more_content = '',
            iterator;

        for (let i = 0; i < content.length; i++) {
            iterator = content[i];
            const if_img = iterator.note_is_img ? '<div class="nts-host-display-type nt-ui-rad4 ft-sect"><span>photo</span></div>' : '';
            more_content += `
            <div class="nts-host relative">
                <span id="page-assistant" class="hd" pid="${iterator.pid}" read="${iterator.post_url}" title="${iterator.title}" poster="${iterator.poster_name}" save_state="${iterator.save}" like_state="${iterator.like}" unlike_state="${iterator.unlike}"></span>
                <a href="${iterator.post_url}" class="nts-host-anchor a">
                    <div class="nts-host-display lozad bck relative" data-background-image="${iterator.cover}">
                        ${if_img}
                        <div class="nts-host-display-filter"></div>
                    </div>
                    <div class="nts-host-verb ft-sect">
                        <p>
                            <strong title="Paragraphs">${iterator.paragraphs}</strong><span class=""> paragraphs</span>
                        </p>
                    </div>
                    <div id="nts-host-title" class="nts-host-title">
                        <p class="trn3-color">${iterator.if_view, iterator.title}</p>
                    </div>
                </a>
                <div class="nts-host-verb-author ft-sect">
                    <a href="${iterator.profile_url}" class="a">
                        <p>${iterator.poster_name}</p>
                    </a>
                    <a href="#" class="a">
                        <button class="nts-show-menu no-bod" visit="${content_parent.content.profile.visitor_state}"><i class="lg-i fa fa-ellipsis-v"></i></button>
                    </a>
                </div>
            </div>`;
        }

        container.append(more_content);
        trigger_response(trigger, 'Find more <i class="fa fa-chevron-right"></i>', false, '1'); //react back

        //other necessary actions
        lozad().observe(),
        $.nt_small_menu()
    }
    function trigger_response(trig, trigText='<img src="/images/logo/loader.gif" at="loader" width="15" height="15" />', trigState=true, trigOpas='.7') {

        $(trig).attr('disabled',trigState).css('opacity',trigOpas),
        $(trig).html(`${trigText}`);
    }
    function trigger_empty_content(trig, msg='That\'s all for now') {

        $(trig).attr('disabled',true).css('opacity','1'),
        $(trig).addClass('nt-infinitescroll-inactive').text(msg);
    }
}

infinite_home(),
growProfile()