

function forgot() {

    // Get user credentials
    // 1. Get credentials
    // 2. Query DB for credentails
    //    o If exist, send confirmation code to email
    //    o Wait for user to confirm (max-time: 10mins)
    // 3. After confirmation of Identity, send to password change page
    // 4. Allow user to login again.
    // 5. Notify user via email of password change.

    const loaderImg = '<img src="..\\images\\logo\\loader.gif" />',
        loaderArrow = '<i class="sm-i fa fa-chevron-down"></i>',
        nt_replace = $('#fg-mobile-notif');
    
    var button = $('#sign-submit'),
        notice = $('#regNotice'),
        loader = $('#nt-loader');
    var act_email = $('#act-email');
    
    start();
    not_me();

    function start() {

        // 1
        button.on('click', function(e) {

            happening();

            var u_email = $(act_email).val();

            if(u_email != '') {

                // 2
                $.ajax({
                    type: 'post',
                    url: '../processors/forgot.php',
                    data: {
                        eml: u_email
                    },
                    cache: false,
                    success: function(data) {
                        forgot_marshal(u_email, data);
                    },
                    error: function(_jqXhr, _textStatus, errorThrown) {
                        // console.error(errorThrown);
                    }
                });
            } else {

                show_feedback('Enter details');
                happening(loaderArrow, false, '1');
            }

            e.preventDefault();
        });
    }

    function forgot_marshal(the_email, the_data) {

        var data = $.trim(the_data);
        if ($.trim(data) === '13') {

            notice.html(`<span class="success calib"><strong>Check mail for PIN</strong></span>`);

            // 3
            picture(the_email);
            check_PIN(nt_replace);

            validate(the_email);
        } else if($.trim(data) === '500') {

            notice.html(`<span class="error calib"><strong>We can\'t find you. <br>Please try again.</strong></span>`);
            happening(loaderArrow, false, '1');
        } else {
            
            notice.html(`<span class="error calib"><strong>${data}</strong></span>`);
            happening(loaderArrow, false, '1');
        }
    }

    function happening(msg=loaderImg, cond=true, opas='.5', but=button) {
        $(act_email).attr('disabled', cond).css('opacity', opas);
        $(but).attr('disabled', cond).css('opacity', opas);
        loader.html(msg);
    }

    function show_feedback(msg, state = 'error') {
        notice.html(`<span class="${state} calib">${msg}</span>`);
    }

    function check_PIN(house) {

        $(house).children('#fg-mobile-notif-house').load("pin.php #nt-pin_check");
        $(house).slideDown();
    }

    function picture(the_email) {

        $.post('../processors/forgot.php',{email:the_email}, function(data) {
            $('#nt-forgot-user').attr('src', `../../../../people/community/profiles/${data}`);
        });
    }

    function not_me() {

        const but = $('#nt-forgot-NotMe');

        but.on('click', function(){

            notice.text('Enter details');
            close_notif();
            happening(loaderArrow, false, '1');
        });
    }

    function close_notif() {
        $(nt_replace).slideUp();
    }
}

function validate(u_email) {

    // 1. Validate PIN is correct.
    // 2. Travel to change page

    const loaderImg = '<img src="..\\images\\logo\\loader.gif" />',
        loaderArrow = '<i class="sm-i fa fa-chevron-down"></i>';

    vali_pin_date();
    function vali_pin_date() {

        // 1.        
        $('#fg-mobile-notif-house').on('click', 'button', function(e) {

            var user_pin = $('#confirm-pin'),
                button = $('#fogt-pin-submit'),
                notice = $('#fogt-Notice'),
                loader = $('#fogt-loader');
            
            pin = user_pin.val();

            happening(user_pin, button);
            n_loader(loader);

            query_db(u_email, pin, user_pin, button, loader, notice);

            e.preventDefault();
        });
    }

    function query_db(the_email, the_pin, user_pin, button, loader, notice) {

        $.post('../processors/forgot.php', {myEmail:the_email, pin:the_pin}, function(data) {

            if($.trim(data) === '13') {
                
                // 2.
                show_feedback(notice, 'Validated', 'success');
                before_travel(the_email, the_pin);
            } else {

                happening(user_pin, button, false, '1');
                n_loader(loader, loaderArrow);
                show_feedback(notice, data);
            }
        });
    }

    function happening(u_input, but, cond=true, opas='.5') {
        $(u_input).attr('disabled', cond).css('opacity', opas);
        $(but).attr('disabled', cond).css('opacity', opas);
    }
    function n_loader(ld, msg=loaderImg) {
        ld.html(msg);
    }
    function show_feedback(not, msg, state = 'error') {
        not.html(`<span class="${state} calib">${msg}</span>`);
    }

    function travel(travelPage) {
        setTimeout( $(location).attr('href', travelPage), 2000);
    }

    function before_travel(email, pin, platform = 'forgot') {

        $.post('../processors/forgot.php', {pin_email:email}, function(key) {

            travel(`change.php?ucc=${key}&cc=${pin}&plt=${platform}`);
        });
    }
}



$(document).ready(function() {

    forgot();

});