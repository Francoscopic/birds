
function signin() {

    var text_field = $('.nt-email-login > input');
    const the_submit_element = $('.nt-email-login-action');
    const the_feedback = $('.nt-email-login-feedback');

    const loaderImg = '<img src="images/logo/loader.gif" />',
            loaderArrow = '<i class="sm-i fa fa-chevron-down"></i>',
            loader = $('.nt-email-login-feedback');

    // Habits
    border_habit();
    function border_habit() {
        const the_line_element = $('.nt-email-login');
        text_field.on('focusin', function() {
            the_line_element.css('border-bottom-color','dodgerblue');
            validate_fieldState();
        }).on('focusout', function() {
            the_line_element.css('border-bottom-color','#909090');
        });
    }
    submit_habit();
    function submit_habit() {
        text_field.on('keyup', function() {
            validate_fieldState();
        });
    }
    function validate_fieldState() {
        var field_value = text_field.val();
        if( field_value.length > 0 ) {
            the_submit_element.attr('disabled', false).css('color','dodgerblue');
        } else {
            the_submit_element.attr('disabled', true).css('color','rgba(30, 144, 255, 0.3)');
        }
    }


    // Knowledge base
    know_email();
    function know_email() {

        the_submit_element.on('click', function(e) {
            e.preventDefault();

            var email_addr = text_field.val();

            happening();

            if (email_addr != '') {

                $.ajax({
                    type: 'post',
                    url: 'processors/welcome.php',
                    data: {
                        eml: email_addr,
                    },
                    cache: false,
                    success: function(data) {
                        welcome_marshal(data, email_addr);
                    },
                    error: function(_jqXhr, _textStatus, errorThrown) {
                        console.error(errorThrown);
                    }
                });
            }
        });

        function welcome_marshal(the_data, em_addr) {
            var data = $.trim(the_data);
            if ($.trim(data) === '13') {
                happening();
                show_feedback('Welcome back!', 'success');
                send_user_to_signIn(em_addr);
            }
            else if ($.trim(data) === '80') {
                happening(loaderArrow, false, '1');
                show_feedback('You need to Sign Up', '');
                goToSomewhere('signup.php');
            }
            else {
                happening(loaderArrow, false, '1');
                show_feedback(the_data);
            }
        }
        function happening(msg=loaderImg, cond=true, opas='.5', but=the_submit_element) {
            $(but).attr('disabled', cond).css('opacity', opas);
            loader.html(msg);
        }
        function goToSomewhere(travelPage) {
            setTimeout( $(location).attr('href', travelPage), 1000);
        }
        function show_feedback(msg, state = 'error') {
            the_feedback.html(`<span class="${state} calib">${msg}</span>`);
        }
        function send_user_to_signIn(u_email) {

            $.post('processors/welcome.php', {get_eml: u_email}, function(data) {
                goToSomewhere(`resignin.php?up=${data}`);
            });
        }
    }
}


$(document).ready(function() {

    signin();
});