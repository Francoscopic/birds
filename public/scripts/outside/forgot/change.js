

function change() {

    // 1. Confirm unempty fields
    // 2. Certify equality of fields
    // 3. Send to server

    const button = $('#chg-psk-submit'),
        loaderImg = '<img src="../images/logo/loader.gif" />',
        loaderArrow = '<i class="sm-i fa fa-chevron-down"></i>';
    
    var assistant = $('#chg-assistant'),
        key = assistant.attr('ucc'),
        code = assistant.attr('cc'),
        channel = assistant.attr('pth');
    
    var notice = $('#chg-Notice'),
        loader = $('#chg-loader');

    var psk = $('#chg-psk'),
        repsk = $('#chg-re-psk');
    
    button.on('click', function(e) {

        happening();

        var psw = psk.val(),
            re_psw = repsk.val();
        
        // 1.
        if(psw === '' || re_psw === '') {

            show_feedback('Empty fields');
            happening(loaderArrow, false, '1');
        } else if( !(psw == re_psw) ) { // 2.
            show_feedback('Password mismatch');
            happening(loaderArrow, false, '1');
        } else {

            save(psw, re_psw);
        }

        e.preventDefault();
    });

    function happening(msg=loaderImg, cond=true, opas='.5') {
        $(psk).attr('disabled', cond).css('opacity', opas);
        $(repsk).attr('disabled', cond).css('opacity', opas);
        $(button).attr('disabled', cond).css('opacity', opas);
        loader.html(msg);
    }

    function show_feedback(msg, state = 'error') {
        notice.html(`<span class="${state} calib">${msg}</span>`);
    }

    function travel(travelPage) {
        setTimeout( $(location).attr('href', travelPage), 5000);
    }

    function save(psw, repsw) {

        $.post('../processors/change.php', {key:key, pin:code, path:channel, u_psw:psw, u_repsw:repsw}, function(data) {

            if($.trim(data) === '13') {
                show_feedback('Password changed', 'success');
                goToSignin(key, psw);
            } else {
                show_feedback(data);
                happening(loaderArrow, false, '1');
            }
        });
    }

    function goToSignin(the_key, the_pass) {

        $.post('../processors/change.php', {eml_request_code: the_key}, function(email) {
            signInRequest(email, the_pass);
        }).fail(function(_jqXhr, _textStatus, errorThrown) {
            // console.error(errorThrown);
            happening();
        });

        function signInRequest(eml, psk) {

            $.ajax({
                type: 'post',
                url: '../processors/signin.php',
                data: {
                    clt: eml,
                    psw: psk
                },
                cache: false,
                success: function(data) {
    
                    happening();
                    sign_in_marshal(data);
                },
                error: function(_jqXhr, _textStatus, errorThrown) {
                    console.error(errorThrown);
                }
            });
        }

		function sign_in_marshal(the_data) {

			var sin_data = $.trim(the_data);
			if ($.trim(sin_data) === '13') {
				happening();
				travel_func(false);
			}
			else {
                show_feedback(data, 'error');
			}
	
			function travel_func(travel_page, travel_id='') {
				if(travel_page == false) {
					goToSomewhere('../../../../'); // index.php
				} else {
					goToSomewhere(`../../in/${travel_page}.php?wp=${travel_id}`); // where the user was (article)
				}
			}
			function goToSomewhere(travelPage) {
				setTimeout( $(location).attr('href', travelPage), 1000);
			}
		}
	}
}

$(document).ready(function(){

    change();

});