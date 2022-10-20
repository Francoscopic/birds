

function signIn() {
        
    const loaderImg = '<img src="/images/logo/loader.gif" width="15" height="15" />',
        loaderArrow = '<i class="sm-i fa fa-chevron-down"></i>';
    const button = $('.nt-signin > button'),
        notice = $('.signin-notify'),
        loader = $('.signin-notify');

    var act_email = $('.nt-signin > input[type="email"]'),
        act_pass = $('.nt-signin > input[type="password"]'),
        travel_page;


    $(button).on('click', function(e) {     
        
        e.preventDefault();

        var the_email = $(act_email).val(),
            the_pass = $(act_pass).val();

        happening();

        if(the_email != "" && the_pass != "") {
            
            $.post('/processor/signin/login/', {clt:the_email, psw:the_pass}, function(data){

                sign_in_marshal(data);
            }).fail(function(_jqXhr, _textStatus, errorThrown){
                console.error(_jqXhr.responseText);
            })
        } else {
            happening(loaderArrow, false, '1');
            show_feedback('Enter details');
        }
    });

    function sign_in_marshal(the_data) {
        var data = $.trim(the_data.status);
        if ($.trim(data) === '40') {
            login();
            happening(loaderArrow, false, '1');
        } else {
            happening(loaderArrow, false, '1');
            show_feedback(the_data.message);
        }
    }

    function happening(msg=loaderImg, cond=true, opas='.5', but=button) {
        $(but).attr('disabled', cond).css('opacity', opas);
        loader.html(msg);
    }

    function show_feedback(msg, state = 'error') {
        notice.html(`<span class="${state} calib"><strong>${msg}</strong></span>`);
    }

    function goToSomewhere(travelPage) {
        setTimeout( $(location).attr('href', travelPage), 1000);
    }

    function travel_func(travel_page) {
        if(travel_page == undefined || travel_page == null) {
            goToSomewhere('/'); // index.php
        } else {
            goToSomewhere(`${travel_page}`); // where the user was
        }
    }

    function login() {
        show_feedback('Welcome to Notes', 'success');
        travel_func(travel_page);
    }
}

$(document).ready(function(){

    signIn();
    
});