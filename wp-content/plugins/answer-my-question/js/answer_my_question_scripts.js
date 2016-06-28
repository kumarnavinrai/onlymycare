function validateEmail(elementValue){
   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
   return emailPattern.test(elementValue);
}

function isUrl(s) {
    var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
    return regexp.test(s);
}

jQuery(function ($) {
	$(".amq_show_modal").click(function(){
		$("#answer-my-question-modal-bg, #answer-my-question-modal").fadeIn('fast', function(){
			//Bring the modal into the viewport
			var viewportWidth = jQuery(window).width(),
            viewportHeight = jQuery(window).height(),
            $foo = jQuery('#answer-my-question-modal'),
            elWidth = $foo.width(),
            elHeight = $foo.height(),
            elOffset = $foo.offset();
        jQuery(window)
            .scrollTop(elOffset.top + (elHeight/2) - (viewportHeight/2))
            .scrollLeft(elOffset.left + (elWidth/2) - (viewportWidth/2));
		});
		$("#answer-my-question-modal-bg").css({"height":$("body").height() + 100});
		return false;
	});
	
	$("#answer-my-question-modal .close").click(function(){
		$("#answer-my-question-modal-bg, #answer-my-question-modal").fadeOut();
	});
	
	$(document).keyup(function(e) {
		if (e.keyCode == 27) { 
			$("#answer-my-question-modal-bg, #answer-my-question-modal").fadeOut();
			$("body").css("line-height", oldLineHeight); 
		}
	});
	
	$("#answer-my-question-modal .close").click(function(){
		$("#answer-my-question-modal-bg, #answer-my-question-modal").fadeOut();
	});
	
	$("#answer-my-question-modal #send").click(function(){
		var error = false;
		var name = $('#question-form input[name="name"]').val();
		var email = $('#question-form input[name="email"]').val();
		var url = $('#question-form input[name="url"]').val();
		var subject = $('#question-form input[name="subject"]').val();
		var question = $('#question-form textarea[name="question"]').val();
		
		$('#question-form input, #question-form textarea').blur(function(){
			$(this).css({
					"borderTopColor":"#DEDEDE",
					"borderRightColor":"#DEDEDE",
					"borderBottomColor":"#DEDEDE",
					"borderLeftColor":"#DEDEDE"
				});
		});
		
		if(name == ''){
			$('#question-form input[name="name"]').css({"border-color":"red"});
			var error = true;
		}
		
		if(subject == ''){
			$('#question-form input[name="subject"]').css({"border-color":"red"});
			var error = true;
		}
		
		if(url != '' && isUrl(url) === false){
			$('#question-form input[name="url"]').css({"border-color":"red"});
			var error = true;
		}
		
		if(question == ''){
			$('#question-form textarea[name="question"]').css({"border-color":"red"});
			var error = true;
		}
		
		if(validateEmail(email) === false){
			$('#question-form input[name="email"]').css({"border-color":"red"});
			var error = true;
		}
		
		if(error === true){
			return false;
		}
		
		$("#answer-my-question-modal .form-contents, #send-question").hide();
		$("#sending-loader").show();
		$.post($("#post_location").val(), $("#question-form").serialize(), function(obj){
			if(obj.result === true){
				$("#sending-loader").hide();
				$("#message-sent").show();
			}else{
				$("#sending-loader").hide();
				$("#message-sent").text('There was an error trying to send your message. Please contact the site administrator.').show();
			}
		});
		return false;
	});
});
