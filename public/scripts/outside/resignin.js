
function resignIn() {
        
    const loaderImg = '<img src="images/logo/loader.gif" />',
            loaderArrow = '<i class="sm-i fa fa-chevron-down"></i>';
    const button = $('.nt-signin > button'),
        notice = $('.signin-notify'),
        loader = $('.signin-notify');

    var act_pass = $('.nt-signin > input[type="password"]');


    $(button).on('click', function(e) {   
                
        e.preventDefault();

        var the_email = act_pass.attr('eml'),
            the_pass = $(act_pass).val();

        happening();

        if(the_email != "" && the_pass != "") {
            
            $.ajax({
                type: 'post',
                url: 'processors/signin.php',
                data: {
                    clt: the_email,
                    psw: the_pass
                },
                cache: false,
                success: function(data) {
                    sign_in_marshal(data);

                },
                error: function(_jqXhr, _textStatus, errorThrown) {
                    console.error(errorThrown);
                }
            });
        } else {
            happening(loaderArrow, false, '1');
            show_feedback('Enter password');
        }
    });

    function sign_in_marshal(the_data) {
        var data = $.trim(the_data);
        if ($.trim(data) === '13') {
            login();
            happening(loaderArrow, false, '1');
        }
        else if ($.trim(data) === '80') {
            happening(loaderArrow, false, '1');
            notice.html(`<span class="error calib">Account locked out. <br><a class="a" href="forgot/">Recover</a></span>`);
        }
        else {
            happening(loaderArrow, false, '1');
            show_feedback(data);
        }
    }

    function happening(msg=loaderImg, cond=true, opas='.5', but=button) {
        $(but).attr('disabled', cond).css('opacity', opas);
        loader.html(msg);
    }

    function show_feedback(msg, state = 'error') {
        notice.html(`<span class="${state} calib">${msg}</span>`);
    }

    function goToSomewhere(travelPage) {
        setTimeout( $(location).attr('href', travelPage), 1000);
    }

    function login() {
        show_feedback('Welcome back', 'success');
        goToSomewhere(`../../../index.php`);
    }
}

$(document).ready(function(){

    resignIn();
    
});