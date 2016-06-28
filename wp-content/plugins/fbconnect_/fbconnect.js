function show_feed(template_id,data,callback,user_message_prompt,user_message_txt){
		user_message = {value: user_message_txt};
		FB.Connect.showFeedDialog(template_id, data,null, null, null , FB.RequireConnect.promptConnect,callback);
}
var fb_refreshURL = unescape(window.location.pathname);

function fb_add_urlParam(url,param){
	if (param != ""){
		pos = url.indexOf("?",0);
		if (pos == -1) {
			return url + "?" + param;
		}else{
			return url + "&" +param;
		}
	}else{
		return url;
	}

}

function set_fb_redirect_login_url(url){
	fb_redirect_login_url = url;
}

function login_facebook(){
	if (fb_track_events){
		var trackvalue; 
		if (fb_status=="connected"){
			trackvalue = 0;
		}else if(fb_status=="notConnected") {
			trackvalue = 1;
		}else{
			trackvalue = 2;
		}
		if (typeof(pageTracker)!=='undefined'){
			pageTracker._trackEvent('Facebook','login', fb_pageurl,trackvalue);
		}else if(typeof(_gaq)!=='undefined'){
			_gaq.push(['_trackEvent', 'Facebook','login', fb_pageurl,trackvalue]);
		}
	}
	
	response = FB.getAuthResponse();
	if (response && response.signedRequest!="") {
        //sessionFacebook+="&session="+JSON.stringify(response.session);
		sessionFacebook = "signed_request="+response.signedRequest;
		if (fb_canvas!="web"){
			document.location = fb_add_urlParam(fb_pageurl,sessionFacebook);
		}else{
	        document.location = fb_pageurl;
		}
	}   
	/*if(url!=null && url!=""){
		document.location = fb_add_urlParam(url,sessionFacebook);;	
	}else{
		if (fb_signed_request!=""){
			top.location = fb_canvas_url;
		}else{
			//window.location.reload(true);
			url = document.location;
			//document.location = url.replace("?fbconnect_action=postlogout", "")+sessionFacebook;
			document.location = fb_add_urlParam(fb_pageurl,sessionFacebook);
		}
	}*/

}

function login_facebookjs(urlredirect){
	 FB.login(function(response) {
		   var trackvalue;
		   if (response.authResponse) {
		   		if (typeof(fb_redirect_login_url) != "undefined" && fb_redirect_login_url!=""){
					document.location = fb_redirect_login_url;	
				}else{
				   trackvalue = "login";
				   if (urlredirect==""){
					   urlredirect = fb_pageurl;
				   }
					
				   fb_signed_request = response.authResponse.signedRequest;
				   fb_userid = response.authResponse.userID;
				   sessionFacebook = "signed_request="+response.authResponse.signedRequest;
					if (fb_canvas!="web"){
						document.location = fb_add_urlParam(urlredirect,sessionFacebook);
					}else{
				        document.location = urlredirect;
					}
				}
		   } else {
			   trackvalue = "cancellogin";
		     //alert('User cancelled login or did not fully authorize.');
		   }
			if (fb_track_events){
				if (typeof(pageTracker)!=='undefined'){
					pageTracker._trackEvent('Facebook',trackvalue, fb_pageurl);
				}else if(typeof(_gaq)!=='undefined'){
					_gaq.push(['_trackEvent', 'Facebook',trackvalue, fb_pageurl]);
				}
			}
		 }, {scope: fb_requestperms});
}

function login_fan(pageid,postid,thick){
	 jQuery('#fanboxlike').fadeOut('slow');
	 FB.login(function(response) {
		   var trackvalue;
		   if (response.authResponse) {
			   fb_signed_request = response.authResponse.signedRequest;
			   fb_userid = response.authResponse.userID;
			   
			   sessionFacebook = "signed_request="+response.authResponse.signedRequest;
				/*if (fb_canvas!="web"){
					document.location = fb_add_urlParam(fb_pageurl,sessionFacebook);
				}else{
			        document.location = fb_pageurl;
				}*/
			   //document.location = fb_add_urlParam(fb_pageurl,sessionFacebook);
			   isfanfbpage(pageid,postid,fb_userid,sessionFacebook,thick);
				
		   } else {
			   jQuery('#fanboxlike').fadeIn('slow');
		   }
		 }, {scope: fb_requestperms});
}

function login_facebook2(reload){
	FB.login(function(response) {
		   var trackvalue;
		   if (response.authResponse) {
			   trackvalue = "login";
			   fb_netid="facebook";

				sessionFacebook = "signed_request="+response.authResponse.signedRequest;
				fb_signed_request = response.authResponse.signedRequest;
				fb_userid = response.authResponse.userID;
				jQuery('.fbconnect_widget_divclass').fadeOut('fast');
				var fbimgparams = "";
				if (typeof(window["maxlastusers"]) != "undefined"){
					fbimgparams = '&maxlastusers='+maxlastusers+'&avatarsize='+avatarsize;
				}
				
				fbimgparams = '&fbclientuser='+FB.getUserID();
				fbimgparams = fbimgparams + "&" + sessionFacebook;
				href = fb_add_urlParam(fb_ajax_url,"useajax="+sjws_useajaxcontent);
				href = fb_add_urlParam(href,"fbajaxlogin=facebook");
				href = fb_add_urlParam(href,"refreshpage=false");
				href = fb_add_urlParam(href,fbimgparams);
				href = fb_add_urlParam(href,"smpchanneltype="+fb_chaneltype);
				href = fb_add_urlParam(href,"fbpostid="+fb_postid);
				
				jQuery('.fbconnect_widget_divclass').load(href,function(){
					if (typeof(fb_redirect_login_url) != "undefined" && fb_redirect_login_url!=""){
							if(typeof(fb_redirect_login_url_thick) != "undefined" && fb_redirect_login_url_thick=="thickbox"){
								tb_show('', fb_redirect_login_url, null);
							}else if(typeof(fb_redirect_login_url_thick) != "undefined" && fb_redirect_login_url_thick=="jscallback"){
								fb_redirect_login_url(wp_userid,fb_isNewUser,fb_user_terms); 
							}else{
								document.location = fb_redirect_login_url;
							}	
					}else if (fb_show_reg_form=="on" && (fb_isNewUser || (fb_reg_form_terms=="y" && fb_user_terms!="y"))){
						alert(fb_reg_form_terms+fb_user_terms);
						if(fb_regform_url==""){
							tb_show("Registration", fb_root_siteurl+ "?fbconnect_action=register&height=390&width=435", "");
						}else{
							document.location = fb_root_siteurl + fb_regform_url;
						}
					}else if (reload){
							document.location = fb_pageurl;
					}
					if (!reload){
						jQuery('.fbconnect_commentsloginclass .fbTabs').remove();
						fb_showTab('fbFirst');
						jQuery(".fbconnect_commentslogin").show();
						jQuery(this).fadeIn('slow');
						jQuery("#commentform #author").val(wp_username);
						jQuery("#commentform #email").val(wp_useremail);
						jQuery("#fbconnectcheckpublish").addClass("fbconnect_sharewith_facebook");
						jQuery("#fbconnectcheckpublish").show();
					}
				});
				
		   } else {
			   trackvalue = "cancellogin";
		     //alert('User cancelled login or did not fully authorize.');
		   }
		   if (fb_track_events){
				if (typeof(pageTracker)!=='undefined'){
					pageTracker._trackEvent('Facebook',trackvalue, fb_pageurl);
				}else if(typeof(_gaq)!=='undefined'){
					_gaq.push(['_trackEvent', 'Facebook',trackvalue, fb_pageurl]);
				}
			}
		 }, {scope: fb_requestperms});	
}

function fb_checkerrormsg(data,dataType){

	jQuery(fb_errormsgcontainer).hide("fast");
	var errormsg;
	if (dataType=="html"){
  		errormsg = jQuery(data).filter('#errormsg');
  	}else{
  		errormsg = data["errormsg"];	
  	}

	if (typeof(errormsg)!=='undefined' && errormsg.length!=0){
		if(fb_errormsgcontainer!=""){
			jQuery(fb_errormsgcontainer).html(errormsg);
			jQuery(fb_errormsgcontainer).show("slow");
		}else{
			alert(errormsg.html());
		}
		return false;
	}else{
		var infomsg
		if (dataType=="html"){
			infomsg = jQuery(data).filter('#infomsg');
		}else{
			infomsg = data["infomsg"];
		}
		if (typeof(infomsg)!=='undefined' && infomsg.length!=0){
			if(fb_errormsgcontainer!=""){
				jQuery(fb_errormsgcontainer).html(infomsg);
				jQuery(fb_errormsgcontainer).show("slow");
			}else{
				alert(infomsg.html());
			}
		}
		return true;
	}
}

function fb_msgdialog(title,dialoghtml,showclose,showacept,callback,width,height){
	jQuery("#TB_ajaxContent").html("");
	var botonera = "";
	if (width=="" || typeof(width) == "undefined"){
		width = 400;
	}
	if (height=="" || typeof(height) == "undefined"){
		height = 190;
	}
	if (callback=="" || typeof(callback) == "undefined"){
		callback = "fb_callbackdialogmsg";
	}

	if (showclose || showacept){
		botonera = '<div class="fbdialogbuttons" id="fbdialogbuttons">';
		if (showacept){
			botonera += '<input type="button" onclick="tb_remove();'+callback+'(1);" value="Confirm" name="fbbuttonconfirm" class="wsbutton button">';
		}
		if (showclose){
			botonera += '<input type="button" onclick="tb_remove();'+callback+'(0);" value="Close" name="fbbuttoncancel" class="wsbuttonSecondary button">';
		}
		botonera += '</div>';
	}
	dialoghtml = "<div id=\"fbmsgdialoghtml\" style=\"display:none;\"><div class=\"msgdialogcontent\"><div class=\"msgdialogbody\">"+dialoghtml+"</div>"+botonera+"</div></div>";
	jQuery("#fbmsgdialoghtml").remove();
	jQuery("body").append(dialoghtml);
	fb_scrollto();
		
	if (title==""){
		tb_show(title, "#TB_inline?height="+height+"&width="+width+"&inlineId=fbmsgdialoghtml&modal=true", "");
	}else{
		tb_show(title, "#TB_inline?height="+height+"&width="+width+"&inlineId=fbmsgdialoghtml", "");
	}
	jQuery("#TB_window").css({top: '50px'});
	jQuery("#TB_window").css({marginTop: '0px'});
}
function fb_callbackdialogmsg(response){
	//document.location = fb_pageurl;
}
function fb_refreshloginwp(reload){
	jQuery('.fbconnect_widget_divclass').fadeOut('fast');
	var fbimgparams = "";
	var realredirect = false;
	
	if (typeof(window["maxlastusers"]) != "undefined"){
		fbimgparams = '&maxlastusers='+maxlastusers+'&avatarsize='+avatarsize;
	}
	
	fbimgparams = '&fbclientuser='+FB.getUserID();
	fbimgparams = fbimgparams + "&" + sessionFacebook;
	href = fb_add_urlParam(fb_ajax_url,"useajax="+sjws_useajaxcontent);
	href = fb_add_urlParam(href,"fbajaxlogin=wordpress");
	href = fb_add_urlParam(href,"refreshpage=false");
	href = fb_add_urlParam(href,fbimgparams);
	href = fb_add_urlParam(href,"smpchanneltype="+fb_chaneltype);
	href = fb_add_urlParam(href,"fbpostid="+fb_postid);

	jQuery.ajax({
		type: "POST",
		url: href,
		data: jQuery("#fb_loginform").serialize()
		,
		  complete:function() {
			  
		  },
		  error:function() {
			  alert("error ajax");
		  },
		  success: function(data) {
		  		successcall = fb_checkerrormsg(data,"html");
		  		if (successcall){
		  			jQuery('.fbconnect_widget_divclass').html(data);
					tb_remove();
						
					if (typeof(fb_redirect_login_url) != "undefined" && fb_redirect_login_url!=""){
						if(typeof(fb_redirect_login_url_thick) != "undefined" && fb_redirect_login_url_thick=="thickbox"){
							tb_show('', fb_redirect_login_url, null);
						}else if(typeof(fb_redirect_login_url_thick) != "undefined" && fb_redirect_login_url_thick=="jscallback"){
							fb_redirect_login_url(wp_userid,fb_isNewUser,fb_user_terms); 
						}else{
							realredirect = true;
							document.location = fb_redirect_login_url;
						}	
					}else if (fb_show_reg_form=="on" && fb_reg_form_terms=="y" && fb_user_terms!="y" ){
						if(fb_regform_url==""){
							tb_show("Registration", fb_root_siteurl+ "?fbconnect_action=register&height=390&width=435", "");
						}else{
							realredirect = true;
							document.location = fb_root_siteurl + fb_regform_url;
						}
					}else if(reload){
						realredirect = true;
						//document.location = fb_pageurl;
						document.location = fb_pageurl;
					}
					
				}
				if (!realredirect){
					jQuery('.fbconnect_commentsloginclass .fbTabs').remove();
					fb_showTab('fbFirst');
					jQuery(".fbconnect_commentslogin").show();
					jQuery("#commentform #author").val(wp_username);
					jQuery("#commentform #email").val(wp_useremail);
					jQuery("#fbconnectcheckpublish").addClass("fbconnect_sharewith_facebook");
					jQuery("#fbconnectcheckpublish").show();
					jQuery(".fbconnect_widget_divclass").fadeIn('slow');
				}
		  }
		}
		  );
	return false;
}


function fb_registeruser(reload){
	var fbimgparams = "";
	if (typeof(window["maxlastusers"]) != "undefined"){
		fbimgparams = '&maxlastusers='+maxlastusers+'&avatarsize='+avatarsize;
	}
	
	fbimgparams = '&fbclientuser='+FB.getUserID();
	fbimgparams = fbimgparams + "&" + sessionFacebook;
	href = fb_add_urlParam(fb_ajax_url,"useajax="+sjws_useajaxcontent);
	href = fb_add_urlParam(href,"fbajaxlogin="+fb_netid);
	href = fb_add_urlParam(href,"refreshpage=false");
	href = fb_add_urlParam(href,fbimgparams);
	href = fb_add_urlParam(href,"smpchanneltype="+fb_chaneltype);
	href = fb_add_urlParam(href,"fbpostid="+fb_postid);
	
	jQuery.ajax({
		type: "POST",
		url: href,
		data: jQuery("#fbregisterform").serialize()
		,
		  complete:function() {
			  
		  },
		  error:function() {
			  alert("error ajax");
		  },
		  success: function(data) {
		  		successcall = fb_checkerrormsg(data,"html");
		  		if (successcall){
					fb_msgdialog(" ",data,true,false);
					document.location = fb_pageurl; 
				}
		  }
		}
		  );
	return false;
}

function fb_loadaccesstab(taburl){
	jQuery(document).ready(function($) {
		jQuery('#fbaccesstable').html("Loading...");	
		$('#fbaccesstable').load(taburl,function(){
				//alert('cargado');
			});
	});
}

function fb_init_user(userid,username,useremail){
	jQuery(document).ready(function($) {
		wp_userid = userid;
		wp_username = username;
		wp_useremail = useremail;
	});
}

function logout_facebook(){
	//window.location = fb_add_urlParam(fb_pageurl,"fbconnect_action=logout&fbclientuser=0");

	if (typeof(FB)!=='undefined' && FB.getUserID()!=0){
		FB.logout(function(result) {
			window.location = fb_add_urlParam(fb_pageurl,"fbconnect_action=logout&fbclientuser=0");
		});	
	}else{
		window.location = fb_add_urlParam(fb_pageurl,"fbconnect_action=logout&fbclientuser=0");
	}
	/*FB.logout(function(result) { 
		jQuery('.fbconnect_widget_divclass').fadeOut('slow');
		jQuery('.fbconnect_widget_divclass').load(fb_ajax_url+'?fbajaxlogout=true&fbconnect_action=logout&maxlastusers='+maxlastusers+'&avatarsize='+avatarsize,function(){
			jQuery('.fbconnect_commentsloginclass .fbTabs').remove();
			jQuery(this).fadeIn('slow');
			fb_showTab('fbFirst');
			jQuery("#fbconnectcheckpublish").hide();
			jQuery(".logged-in-as").hide();
		});
	
	});
	*/

	
}
function fb_view_moresiteinfo(thehref,domain,pos){
		var activity= '<iframe src="http://www.facebook.com/plugins/activity.php?site='+domain+'&amp;width=250&amp;height=270&amp;header=true&amp;colorscheme=dark&amp;border_color=#000000" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:250px; height:270px;margin-right:10px;"></iframe>';
		var recommendations= '<iframe src="http://www.facebook.com/plugins/recommendations.php?site='+domain+'&amp;width=250&amp;height=270&amp;header=true&amp;colorscheme=dark&amp;border_color=#000000" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:250px; height:270px"></iframe>';
		jQuery('#moreinfo'+pos).replaceWith("<br/>"+activity+recommendations);
}

function fb_links_info(){
	var pos=0;
	jQuery(document).ready(function($) {
 //$('.entry a').each(function () {	
  $('a').each(function () {
    // options
	var thehref = $(this).attr("href");
	var domain = thehref.split(/\/+/g)[1]; 
	pos++;
	//$(this).append('<div class="bubbleInfo"><span class="trigger"> AAA </span> <div class="popup"><div id="fb_din_like"><fb:like href="'+thehref+'"/>-</fb:like></div>"</div></div>');
	var activity= '<iframe src="http://www.facebook.com/plugins/activity.php?site='+domain+'&amp;width=250&amp;height=270&amp;header=true&amp;colorscheme=dark&amp;border_color=#000000" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:250px; height:270px;margin-right:10px;"></iframe>';
	var recommendations= '<iframe src="http://www.facebook.com/plugins/recommendations.php?site='+domain+'&amp;width=250&amp;height=270&amp;header=true&amp;colorscheme=dark&amp;border_color=#000000" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:250px; height:270px"></iframe>';
	var like='<iframe src="http://www.facebook.com/plugins/like.php?href='+thehref+'&amp;layout=standard&amp;show_faces=true&amp;width=480&amp;action=like&amp;colorscheme=dark" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:480px; height:70px"></iframe>';
	$(this).append('<span class="bubbleInfo"><span class="trigger"></span> <div class="popup"><div class="borderpopup"></div><div class="popupIn"><div id="fb_din_like">'+like+'<br/><a href="#" onmouseover="javascript:fb_view_moresiteinfo(\''+thehref+'\',\''+domain+'\',\''+pos+'\');">More site info ['+domain+']</a><div id="moreinfo'+pos+'"></div><div class="poweredpop"><a href="http://www.sociable.es">Powered by Sociable!</a></div></div></div></div></span>');
    var distance = 11;
	var distancex = 20;
    var time = 250;
    var hideDelay = 500;

    var hideDelayTimer = null;

    // tracker
    var beingShown = false;
    var shown = false;
    
    var trigger = $('.trigger', this);
	//var trigger = this;

    //var popup = $('#popupid').css('opacity', 0);
	var popup = $('.popup', this).css('opacity', 0);


    $([this, popup.get(0)]).mouseover(function () {

	    
    // set the mouseover and mouseout on both element
    //$([trigger, popup.get(0)]).mouseover(function () {
		//alert("dentro");
      // stops the hide event if we move from the trigger to the popup element
      if (hideDelayTimer) clearTimeout(hideDelayTimer);

      // don't trigger the animation again if we're being shown, or already visible
      if (beingShown || shown) {
        return;
      } else {
        beingShown = true;

        // reset position of popup box
        popup.css({
           top: '-80px',
		  left: '-80px',
          display: 'block' // brings the popup back in to view
        })

        // (we're using chaining on the popup) now animate it's opacity and position
        .animate({
          top: distance + 'px',
		  left: '-'+distancex+'px',
          opacity: 1
        }, time, 'swing', function() {
          // once the animation is complete, set the tracker variables
          beingShown = false;
          shown = true;
        });
      }
    }).mouseout(function () {
      // reset the timer if we get fired again - avoids double animations
      if (hideDelayTimer) clearTimeout(hideDelayTimer);
      
      // store the timer so that it can be cleared in the mouseover if required
      hideDelayTimer = setTimeout(function () {
        hideDelayTimer = null;
        popup.animate({
          top: distance + 'px',
  		  left: '-'+distancex+'px',
          opacity: 0
        }, time, 'swing', function () {
          // once the animate is complete, set the tracker variables
          shown = false;
          // hide the popup entirely after the effect (opacity alone doesn't do the job)
          popup.css('display', 'none');
        });
      }, hideDelay);
    });
  });
});
}
function fb_links_info2(){
	jQuery(document).ready(function($) {
		$('a').hover(
  function () {
  	var thehref = $(this).attr("href");
  	//alert($(this).attr("href"));
    $(this).append($("<div id='fb_din_like'><fb:like href='"+thehref+"'/>-</fb:like></div>"));
	
			FB.XFBML.parse();


  }, 
  function () {
    //$("#fb_din_like").remove();
  }
);

	});
}

var sjws_useajaxcontent = true;
//////////////////////////////////////////////////////////////////////////////////
function fb_links_canvas(procesarnodo,callback){
	if( procesarnodo!=""){
		procesarnodo = procesarnodo +" ";
	}else{
		procesarnodo = "";
	}

	//jQuery(document).ready(function($) {
		//if (fb_signed_request!=""){
		//$('a').unbind('click');

		jQuery(procesarnodo+'a').click(
		function () {
			var thehref = jQuery(this).attr("href");
			var domain = thehref.split(/\/+/g)[1];
			
			var target=jQuery(this).attr("target");
			var classnode=jQuery(this).attr("class");
			//if (target!="_blank" && thehref.indexOf(domain)>-1 && thehref.indexOf("#")==-1 && thehref.indexOf("channel.php")==-1 && thehref!=""){
			var classnoprocess = true;

			if ( ( typeof(classnode) != "undefined" && classnode!="" ) && (classnode.indexOf("thickbox")!=-1 || classnode.indexOf("noprocesslink")!=-1) ){
				classnoprocess = false;
			}

			if ( classnoprocess && target!="_blank" && thehref.indexOf(domain)>-1 && thehref!="#" && thehref.indexOf("channel.php")==-1 && thehref!=""){
				response = cargaCapaAjax(thehref,"",false,"",callback,jQuery(this));
				return false;
			}else{
			}
		}
		);
		jQuery(procesarnodo+'form').submit(
		function () {
			var thehref = jQuery(this).attr("action");
			var domain = thehref.split(/\/+/g)[1];
			
			var classnode=jQuery(this).attr("class");
			var target=jQuery(this).attr("target");
			var classnoprocess = true;

			if ( typeof(target) == "undefined" ){
				target = "";
			}
			if ( ( typeof(classnode) != "undefined" && classnode!="" ) && (classnode.indexOf("thickbox")!=-1 || classnode.indexOf("noprocesslink")!=-1) ){
				classnoprocess = false;
			}
			 
			if (classnoprocess && target!="_blank" && thehref.indexOf(domain)>-1){
				response = cargaCapaAjax(thehref,jQuery(this).serialize(),false,"",callback,jQuery(this));
				return false;
			}
		}
		);
		//}

		jQuery("#fbthickbox_container").fadeOut(500,function () {
			if (typeof(FB)!=='undefined'){
				FB.Canvas.setAutoResize(100);
			}
		});
	//});
}

function cargaCapaAjax(href,data,usethick,procesarnodo,callback,domobj,title,dataType,changescroll) {
	
	if (typeof(changescroll) == "undefined" || changescroll==""){
		changescroll = true;
	}
	
	if (typeof(dataType) == "undefined" || dataType==""){
		dataType = "html";
	}
	if (typeof(procesarnodo) == "undefined" || procesarnodo==""){
		procesarnodo="#bodycontainer";
		changescroll = true;
	}
	if (typeof(title) == "undefined" || title==""){
		title = "";
	}
	href = fb_add_urlParam(href,sessionFacebook);
	href = fb_add_urlParam(href,"useajax="+sjws_useajaxcontent);
	href = fb_add_urlParam(href,"smpchanneltype="+fb_chaneltype);
	if (usethick){
		fb_scrollto();
		tb_show(title, href, null);
		jQuery("#TB_window").css({top: '50px'});
		jQuery("#TB_window").css({marginTop: '0px'});
	
	/*}else if (usethick || href.indexOf(".jpg")>-1 || href.indexOf(".jpeg")>-1 || href.indexOf(".png")>-1 || href.indexOf(".gif")>-1){
		alert("1");
		previewImage(href);*/
	}else{
		if (typeof(FB)!=='undefined'){
			FB.Canvas.setAutoResize(false);
		}
		jQuery("#fbthickbox_container").fadeIn(500, function () {
		jQuery.ajax({
			type: "POST",
			url: href,
			data: data ,
			cache: false,
			dataType: dataType,
			error: function(msg){
				alert("No ha sido posible procesar la petición. Por favor, inténtalo más tarde.");	
				jQuery("#fbthickbox_container").fadeOut(500);
				
			},
			success: function(msg){
				successcall = fb_checkerrormsg(msg,dataType);
		  		if (successcall){
					jQuery(procesarnodo).html(msg);
					fb_post_load();
					fb_links_canvas(procesarnodo);
					jQuery("#bodycontainer").trigger("fbHandleCargaAjax", [msg]);
				}
				if (changescroll){
						fb_scrollto();
				}
				if (typeof(callback) != "undefined" && callback!=""){
					jQuery("#fbthickbox_container").fadeOut(500,function () {
						callback(domobj,msg,successcall);
					});
				}else{
					jQuery("#fbthickbox_container").fadeOut(500);
				}
				}
			});
		});
	}
}

function fb_scrollto(scrolltotagid){
	
	if (typeof(scrolltotagid) == "undefined" || scrolltotagid==""){
		scrolltotagid = "bodycontainer";
	}

	if(jQuery("#"+scrolltotagid).length && fb_chaneltype!='canvas' && fb_chaneltype!='tab') {
		document.getElementById(scrolltotagid).scrollIntoView();
	}else{
		FB.XFBML.parse();
		FB.Canvas.scrollTo(0,0);
	}
}
/*function fb_links_canvas(procesarnodo){
jQuery(document).ready(function($) {

	if (fb_signed_request!=""){
		$('a').click(
		  function () {
		  	var thehref = $(this).attr("href");
		  	var domain = thehref.split(/\/+/g)[1];
		  	
		  	if (thehref.indexOf(domain)>-1){
		  		//alert($(this).attr("href")+"?signed_request="+fb_signed_request);
		  		if (thehref.indexOf('?')>-1){
		  			thehref = thehref+"&signed_request="+fb_signed_request;
		  		}else{
		  			thehref = thehref+"?signed_request="+fb_signed_request;
		  		}
		  		$(this).attr("href",thehref);
		  	}
		  }
		);
		$('form').submit(
				  function () {
				  	var thehref = $(this).attr("action");
				  	var domain = thehref.split(/\/+/g)[1]; 
				  	if (thehref.indexOf(domain)>-1){
				  		if (thehref.indexOf('?')>-1){
				  			thehref = thehref+"&signed_request="+fb_signed_request;
				  		}else{
				  			thehref = thehref+"?signed_request="+fb_signed_request;
				  		}
				  		$(this).attr("action",thehref); 
				  	}
				  }
		);
	}
	
});
}
*/

function login_facebook3(urlajax){
	jQuery(document).ready(function($) {
		$('.fbconnect_login_div').load(urlajax+'?checklogin=true&refreshpage=fbconnect_refresh');
	});
}

function login_facebookForm(){
	jQuery(document).ready(function($) {
		$('#fbconnect_reload2').show(); 
		var fbstatusform = $('#fbstatusform');	
		$('#fbresponse').load(fbstatusform[0].action+'?checklogin=true&login_mode=themeform');
	});
}

function login_facebookNoRegForm(){
	jQuery(document).ready(function($) {
		$('#fbconnect_reload2').show(); 
		$('#fbloginbutton').hide(); 
		var fbstatusform = $('#fbstatusform');	
		$('#fbresponse').load(fbstatusform[0].action+'?checklogin=true&login_mode=themeform&hide_regform=true');
	});
}

function verify(url, text){
		if (text=='')
			text='Are you sure you want to delete this comment?';
		if (confirm(text)){
			document.location = url;
		}
		return void(0);
	}
// setup everything when document is ready
var fb_statusperms = false;	

function facebook_prompt_permission(permission, callbackFunc) {
    //check is user already granted for this permission or not
    FB.Facebook.apiClient.users_hasAppPermission(permission,
     function(result) {
        // prompt offline permission
        if (result == 0) {
            // render the permission dialog
            FB.Connect.showPermissionDialog(permission, callbackFunc,true);
        } else {
            // permission already granted.
			fb_statusperms = true;
            callbackFunc(true);
        }
    });
}

function callback_perms(){
	
	window.location.reload()
}

function fb_showTab(tabName){
	jQuery(".fbtabdiv").hide();
	jQuery(".fbtablink").removeClass("selected");
	jQuery("#"+tabName).show();	
	jQuery("#"+tabName+'A').addClass("selected");	
	return false;
}

function fb_showTabComments(tabName){
	document.getElementById("fbFirstCommentsA").className = '';
	document.getElementById("fbSecondCommentsA").className = '';
	
	document.getElementById("fbFirstComments").style.visibility = 'hidden';
	document.getElementById("fbSecondComments").style.visibility = 'hidden';
	document.getElementById("fbFirstComments").style.display = 'none';
	document.getElementById("fbSecondComments").style.display = 'none';
	document.getElementById(tabName).style.visibility = 'visible';
	document.getElementById(tabName).style.display = 'block';
	document.getElementById(tabName+'A').className = 'selected';
	return false;
}

function fb_show(idname){
	document.getElementById(idname).style.visibility = 'visible';
	document.getElementById(idname).style.display = 'block';
}
function fb_hide(idname){
	document.getElementById(idname).style.visibility = 'hidden';
	document.getElementById(idname).style.display = 'none';
}	
function fb_showComments(tabName){
	document.getElementById("fbAllFriendsComments").style.visibility = 'hidden';
	document.getElementById("fbAllComments").style.visibility = 'hidden';
	document.getElementById("fbAllFriendsComments").style.display = 'none';
	document.getElementById("fbAllComments").style.display = 'none';
	document.getElementById("fbAllFriendsCommentsA").className = '';
	document.getElementById("fbAllCommentsA").className = '';
	document.getElementById(tabName).style.visibility = 'visible';
	document.getElementById(tabName).style.display = 'block';
	document.getElementById(tabName+'A').className = 'selected';
	return false;
}
function pinnedChange(){
	if (document.getElementById('fbconnect_widget_div').className == "") {
		document.getElementById('fbconnect_widget_div').className = "pinned";
	}else{
		document.getElementById('fbconnect_widget_div').className = "";
	}
}

function showCommentsLogin(){
	var comment_form = document.getElementById('commentform');
	if (!comment_form) {
		return;
	}

	commentslogin = document.getElementById('fbconnect_commentslogin');
	var firstChild = comment_form.firstChild;
    comment_form.insertBefore(commentslogin, firstChild);
	//comment_form.appendChild(commentslogin);
}

function login_thickbox(urllike){
	//alert("LOGIN "+urllike);
	var urlthick = urllike;	
		
	if(urllike.indexOf("?") != -1){
			urlthick = urllike + "&fbconnect_action=register&height=400&width=370";
	}else{
			urlthick = urllike + "?fbconnect_action=register&height=400&width=370";			
	}
	tb_show('Registro', urlthick, null); 
}

function isfanfbpage(pageid,postid,uid,sessionFacebook,thick){
	FB.api( {
   		method: 'pages.isFan',
   		page_id: pageid,
		uid: uid }, 
       function(result) { 
			if (result){
				//document.location = fb_add_urlParam(fb_pageurl,sessionFacebook);
				if(thick){
					tb_remove();
				}else{
					jQuery('#fbaccesstable').html("Loading...");
					fb_loadaccesstab(fb_root_siteurl+"/?fbconnect_action=tab&"+sessionFacebook+"&postid="+postid+"&getcontent=true");
				}
			}else{
				if(thick){
					imgLoader = new Image();
					imgLoader.src = tb_pathToImage;
					tb_show("", fb_root_siteurl+"/?fbconnect_action=tab&height="+fb_heightthick+"&width="+fb_widththick+"&"+sessionFacebook+"&postid="+postid+"&modal=true", "");
					jQuery("#TB_window").css({'margin-top':'0px',top: '<?php echo $topthick;?>px','border':'0'});
					jQuery("#TB_overlay").css({'background-color': '#FFFFFF'});
				}else{
					jQuery('#fbaccesstable').html("Loading...");
					fb_loadaccesstab(fb_root_siteurl+"/?fbconnect_action=tab&"+sessionFacebook+"&postid="+postid);
				}
			}

			 });
}

function customHandleSessionResponse(responseInit){

}

var urllike = "";

function post_fb_user_action(url,action,objecttype)
{
    FB.api('/me/' + action + 
                '?'+objecttype+'='+url,'post',
                function(response) {
        if (!response || response.error) {
                alert('Error occured');
        } else {
            alert('Post was successful! Action ID: ' + response.id);
            }
    });
}

//From ga_social_tracking.js
function fb_extractParamFromUri(uri, paramName) {
	  if (!uri) {
	    return;
	  }
	  
	  var query = decodeURI(uri);
	  
	  // Find url param.
	  paramName += '=';
	  var params = query.split('&');
	  for (var i = 0, param; param = params[i]; ++i) {
 
	    if (param.indexOf(paramName) === 0) {
	      return unescape(param.split('=')[1]);
	    }
	  }
	  return;
}
	
function fb_windowopen(pageurl,title){
	newWindow = window.open(pageurl,title,"status,menubar,height=500,width=640,scrollbars=1");
	newWindow.focus( );	
}

function fbshare_tuenti(pageurl){
	newWindow = window.open("http://www.tuenti.com/share?url="+pageurl,"ShareTuenti","status,menubar,height=500,width=640");
	newWindow.focus( );	
}

function fbshare_twitter(status){
	newWindow = window.open("http://twitter.com/home?status="+status,"ShareTwitter","status,menubar,height=500,width=640");
	newWindow.focus( );	
}

function fbshare_linkedin(articleUrl,articleTitle,articleSummary,articleSource){
	newWindow = window.open("http://www.linkedin.com/shareArticle?mini=true&url="+articleUrl+"&title="+articleTitle+"&summary="+articleSummary+"&source="+articleSource,"ShareLinkedin","status,menubar,height=500,width=640");
	newWindow.focus( );	
}

function fbshare_facebook(name,caption,description,picture,link,display,redirect){
 		FB.ui(
				   {
				     method: 'feed',
				     name: name,
				     caption: caption,
				     description: description,
				     picture: picture,
				     link: link,
					 display: display,
					 redirect_uri: redirect
				   }
				 );
}

function fbshare_facebook_login(name,caption,description,picture,link,display,redirect){
	if (fb_status=="connected"){
		fbshare_facebook(name,caption,description,picture,link,display,redirect);
	}else{
		FB.login(function(response) {
			   if (response.authResponse) {
				   fbshare_facebook(name,caption,description,picture,link,display,redirect);
			   } else {
				   
			   }
			 }, {scope: fb_requestperms});
	}
}

function fbshare_global(){
	jQuery('#commentform #submit').click(function() {
		comment = jQuery('#commentform #comment').val();
		var checkSend = jQuery('#sendToFacebook').attr("checked");
		if (checkSend=="checked" && fb_userid!="" && fb_userid!="0"){
			if (fb_netid=="facebook"){
				fbshare_facebook(fb_pagetitle,fb_caption,fb_bodypost,fb_postimgurl,fb_pageurl,"popup",fb_closepopup_url);
			}else if (fb_netid=="google"){
				//fb_windowopen("https://m.google.com/app/plus/x/?v=compose&hideloc=1&content="+encodeURI(comment+" "+fb_pagetitle+" "+fb_pageurl),"googleplusshare");
				fb_windowopen("https://plusone.google.com/_/+1/confirm?hl=en&url="+encodeURI(fb_pageurl),"googleplusshare");
			
			}else if(fb_netid=="twitter"){
				fb_windowopen("http://twitter.com/intent/tweet?status="+encodeURI(fb_pagetitle+" "+fb_pageurl),"twittershare");
			}
		}
	});	
}
			 
function fb_registeruserthick(){
	if(urllike.indexOf("?") != -1){
			urlthick = fb_root_siteurl + "&fbconnect_action=register&height=400&width=370";
	}else{
			urlthick = fb_root_siteurl + "?fbconnect_action=register&height=400&width=370";			
	}
	tb_show('Registro', urlthick, null); 
}

function fb_refreshlogininfo(netid,userid,isNewUser,terms){
	fb_netid = netid;
	fb_userid = userid;

	tb_remove();

	if (typeof(fb_redirect_login_url) != "undefined" && fb_redirect_login_url!=""){
		if(typeof(fb_redirect_login_url_thick) != "undefined" && fb_redirect_login_url_thick=="thickbox"){
			tb_show('', fb_redirect_login_url, null);
		}else if(typeof(fb_redirect_login_url_thick) != "undefined" && fb_redirect_login_url_thick=="jscallback"){
			fb_redirect_login_url(userid,isNewUser,terms);
		}else{
			document.location = fb_redirect_login_url;
		}	
	}else if (fb_show_reg_form=="on" && (isNewUser || (fb_reg_form_terms=='y' && terms!='y'))){
		if(fb_regform_url==""){
			tb_show("Registration", fb_root_siteurl+ "?fbconnect_action=register&height=390&width=435", "");
		}else{
			document.location = fb_root_siteurl + fb_regform_url;
		}
	}else if (fb_loginreload){		
		document.location = fb_pageurl; 
	}else{
		jQuery('.fbconnect_widget_divclass').fadeOut('fast');
		var fbimgparams = "";
		if (typeof(window["maxlastusers"]) != "undefined"){
			fbimgparams = '&maxlastusers='+maxlastusers+'&avatarsize='+avatarsize;
		}
		

		href = fb_ajax_url+'?fbajaxlogin='+fb_netid+'&refreshpage=false&fbclientuser='+userid;
		href = fb_add_urlParam(href,"smpchanneltype="+fb_chaneltype);
		href = fb_add_urlParam(href,"fbpostid="+fb_postid);
		href2 = fb_add_urlParam(href,"showcommentform=true");
		
		jQuery.get(href2, function(data) {
		  var padre = jQuery("#respond").parent();
		  jQuery("#respond").remove();
		  padre.prepend(data);
		  fbshare_global();
		  jQuery("#commentform #author").val(wp_username);
		  jQuery("#commentform #email").val(wp_useremail);
		  jQuery("#fbconnectcheckpublish").addClass("fbconnect_sharewith_"+fb_netid);
		  jQuery("#fbconnectcheckpublish").show();
		},'html');
		
		jQuery('.fbconnect_widget_divclass').load(href,function(){
			jQuery('.fbconnect_commentsloginclass .fbTabs').remove();
			fb_showTab('fbFirst');
			jQuery(".fbconnect_commentslogin").show();
			jQuery(this).fadeIn('slow');
		});
		
		/*jQuery('.fbconnect_widget_divclass').load(fb_ajax_url+'?fbajaxlogin=true&refreshpage=false'+fbimgparams,function(){
			jQuery('.fbconnect_commentsloginclass .fbTabs').remove();
			fb_showTab('fbFirst');
			jQuery(".fbconnect_commentslogin").show();
			jQuery(this).fadeIn('slow');
			jQuery("#commentform #author").val(wp_username);
			jQuery("#commentform #email").val(wp_useremail);
			jQuery("#fbconnectcheckpublish").addClass("fbconnect_sharewith_"+fb_netid);
			jQuery("#fbconnectcheckpublish").show();
			
		});*/
	}
}

function fb_callParentWindow(netid,fbuserid){
    window.opener.fb_refreshlogininfo();
    return false;
}

function fbInviteFriendsCallback(response){
	//alert("callback");
}

function fbInviteFriends(msg) {
        FB.ui({method: 'apprequests',
          message: msg
        }, fbInviteFriendsCallback);
}

function fbMustLoggin(){
	if (wp_userid=="" || wp_userid=="0"){
		var thehref = jQuery(this).attr("href");
		fb_redirect_login_url = thehref;
		urlthick = fb_plugin_url+"/fbconnect_loginpopup.php?height=330&width=540";
		tb_show('Login', urlthick, null);
		return false;
	}
}

function fbCommentsloginClick(){
	randnum = Math.floor((Math.random()*100)+1);
	fb_pageurl = fb_add_urlParam(fb_pageurl,'ramdomparam='+randnum);
	fb_pageurl = fb_add_urlParam(fb_pageurl,'refreshlogin=1')+"#comments";
}
