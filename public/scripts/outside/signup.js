

function register() {

	const caller = $('.nt-signin > button');
	const loaderImg = '<img src="images/logo/loader.gif" />',
			loaderArrow = '<i class="sm-i fa fa-chevron-down"></i>';
	var notice = $('.signin-notify'),
		loader = $('.signin-notify');
	var act_email = $('.nt-signin > input[type="email"]'),
		act_uname = $('.nt-signin > input[type="text"]'),
		act_pass  = $('.nt-signin > input[type="password"]');

	// Check GET SERVER Variable
	const travel_agent = $('#travel_agent'),
		travel_page = travel_agent.attr('page'),
		travel_id = travel_agent.attr('tid');

	$(caller).on('click', function(e) {

		var $parent  = $('.nt-signin'),
			email    = act_email.val(),
			username = act_uname.val(),
			pass     = act_pass.val();

		// Call loader
		happening();

		if( email === '' || username === '' || pass === '' ) {

			happening(loaderArrow, false, '1');
			notice.html('<span class="error">Enter details</span>');
		} else {

			request(username, email, pass, caller, $parent);
		}
		
		e.preventDefault();
	});

	function request(username, email, pass, $parent) {

		$.post('processors/messenger.php', {email:email, uname:username, welcome:'welcome'}, function(data){

			var data = $.trim(data);

			if(data === '10') {
				create_account(username, email, pass);
			} else {
				var mail_not_sent = `<span class="error"><strong>Unexpected error. Retry</strong></span>`;
				happening(mail_not_sent, false, 1);
			}
			
		});

		function create_account(uname, eml, psk) {
			$.post('processors/signup.php', { un: uname, em: eml, pw: psk }, function (data) {

				var data = $.trim(data);

				sign_up_marshal(data);
			});
		}
		

		function sign_up_marshal(data) {

			if (data === '10') {

				happening(loaderImg, false, '1');
				notice.html(`<span class="success"><strong>Successful! Check mail to confirm account</strong></span>`);
				$('.nt-signin').children('input').val('');
				make_input_goAway();
				// goToSomewhere(`../aquamarine/confirm.php?up=&upi=${username}&upk=`); //confirm page
				// goToSignin(email, username, pass);
			}
			else {
				happening(loaderArrow, false, '1');
				notice.html(`<span class="success">${data}</span>`);
			}
		}
	}
	function happening(msg=loaderImg, cond=true, opas='.5', but=caller) {
		$(but).attr('disabled', cond).css('opacity', opas);
		loader.html(msg);
	}
	function make_input_goAway() {
		$(act_email).hide();
		$(act_uname).hide();
		$(act_pass).hide();
		$(caller).hide();
	}
	function goToSignin(eml, uname, psk) {

		$.ajax({
			type: 'post',
			url: 'processors/signin.php',
			data: {
				clt: eml,
				psw: psk
			},
			cache: false,
			success: function(data) {

				happening();
				notice.html(`<span class="success">${data}</span>`);
				sign_in_marshal(data);
			},
			error: function(_jqXhr, _textStatus, errorThrown) {
				// console.error(errorThrown);
			}
		});

		function sign_in_marshal(the_data) {

			var sin_data = $.trim(the_data);
			if (sin_data === '13') {
				happening();
				travel_func(travel_page, travel_id);
			}
			else {
				happening(loaderArrow, false, '1');
				notice.html(`<span class="success">${data}</span>`);
			}
	
			function travel_func(travel_page, travel_id) {
				if(travel_page == false) {
					goToSomewhere('../../../'); // index.php
				} else {
					goToSomewhere(`../../in/${travel_page}.php?wp=${travel_id}`); // where the user was (article)
				}
			}
		}
	}
	function goToSomewhere(travelPage) {
		setTimeout( $(location).attr('href', travelPage), 1000);
	}
}

$(document).ready(function(){

	register();

});