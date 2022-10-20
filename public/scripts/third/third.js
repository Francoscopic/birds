
import Cookies from '/scripts/plugins/cookies/api.js';

// Help & FAQ
function open_drop_down() {

    var $trigger = $('.third-open-section-trigger > div');
    $trigger.on('click', function(){
        var icon_state = $(this).children('button').children('i').attr('class');

        $(this).parent().children('ul').slideToggle(),
        $(this).parent().children('button').children('i').attr('class', change_arrow_boolean(icon_state)),
        $trigger.not(this).parent().children('ul').slideUp(),
        $trigger.not(this).parent().children('button').children('i').attr('class', change_arrow(false));
    });
    function change_arrow(ar_state=true) {
        return (ar_state==true) ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
    }
    function change_arrow_boolean(ar_state) {
        return ($.trim(ar_state) == 'fa-solid fa-chevron-down') ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
    }
}
function help_feedback() {

    const help_container = $('#was-help-article-helpful'),
        front_runner = $('#helpful-frontrunner'),
        hid = $('#helpful-frontrunner').attr('hid'),
        uid_user = Cookies.get('cookie_user'),
        uid_visitor = Cookies.get('vst'),
        uid = (uid_user == null) ? uid_visitor : uid_user;
    var $yes = $('.helpful-yes'),
        $no = $('.helpful-no');

    $yes.on('click', function(e){
        e.preventDefault();
        send_request('yes', 'yes', uid, hid)
    });
    $no.on('click', function(e){
        e.preventDefault();
        no_reactive()
    });

    function send_request(senq, the_msg, uid, hid) {
        $.post('depends/third/third.php', {selection:senq, message:the_msg, uid:uid, hid:hid}, function(){
        }),
        help_response()
    }
    function no_reactive() {
        var $div = `
        <div class="helpful-form">
            <textarea name="helpful-message" placeholder="Tell us more, please"></textarea>
            <button>Send</button>
            <p><br></p>
            <p><a href="#" id="cancel-help-form">Cancel</a></p>
        </div>`;
        help_container.append($div).fadeIn(),
        front_runner.hide(),
        no_reactive_response(),
        no_reactive_user_feedback()
    }
    function help_response() {
        var $message = `
        <div class="help-thank-response">
            <h3>Thank you for the feedback!!</h3>
        </div>`;

        help_container.append($message).fadeIn(),
        $('#cancel-help-form').click(),
        front_runner.hide()
    }
    function no_reactive_response() {
        var $cancel_form = $('#cancel-help-form');
        $cancel_form.on('click', function(e){
            e.preventDefault();
            revert_response();
        });
        function revert_response() {
            front_runner.show();
            $('.helpful-form').remove()
        }
    }
    function no_reactive_user_feedback() {
        var $textarea = $('.helpful-form > textarea'),
            $button = $('.helpful-form > button');
        $button.on('click', function(e){
            e.preventDefault();

            send_request('no', $($textarea).val(), uid, hid);
        })
    }
}
open_drop_down(),
help_feedback()
