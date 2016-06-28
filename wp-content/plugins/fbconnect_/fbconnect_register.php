<?php
global $regmodal;
$fb_user = fb_get_loggedin_user();
$locale = WPfbConnect_Logic::get_browser_lang();

if (!isset($regmodal) || !$regmodal){
	if (!fb_get_option('fb_connect_use_thick')){
		get_header();
		echo '<div class="narrowcolumn fbregform">';
	}
}
	//$userprofile = WPfbConnect_Logic::get_user();
		
	if (is_user_logged_in()){ //Si el usuario está conectado solo puede modificar su perfil
		$userprofile = wp_get_current_user();
		//print_r($userprofile);
		$name = $userprofile->display_name;
		$nickname = $userprofile->nickname;
		$user_url = $userprofile->user_url;
		$about = $userprofile->description;
		$email = $userprofile->user_email;
		$birthday = $userprofile->birthday;
		
		$location_city = $userprofile->location_city;
		$location_state  = $userprofile->location_state;
		$location_country  = $userprofile->location_country;
	 	$location_zip  = $userprofile->location_zip;
	 	$twitter =  $userprofile->twitter;
		$day = 0;
		$month = 0 ;
		$year = 0;

		if (isset($birthday) && $birthday!=""){
				$birthday = strtotime($birthday);
				$day = date("j",$birthday);
				$month = date("n",$birthday);
				$year = date("Y",$birthday);
		}
		$sex = $userprofile->sex;
		if (!isset($registered))
			$registered = true;
		$fb_user = $userprofile->fbconnect_userid;
		$company_name = $userprofile->company_name;
		$phone = $userprofile->phone;
		$locale = $userprofile->locale;
	}elseif ($fb_user!="" && (!is_user_logged_in() || (isset($force_fbloaddata) && $force_fbloaddata ))){
		$fb_user = fb_get_loggedin_user();
		$usersinfo = fb_user_getInfo($fb_user);
		if ($usersinfo!="ERROR"){
			//$name = $usersinfo["first_name"]." ".$usersinfo["last_name"];
			if ($name==""){
				$name = $usersinfo["name"];
			}
			if ($nickname==""){
				$nickname = $usersinfo["username"];
			}
			if ($user_url==""){
				if (isset($usersinfo["website"]) && $usersinfo["website"]!=""){
					$user_url_array = explode(" ",$usersinfo["website"]." ",1);
					$user_url = trim($user_url_array[0]);
				}else{
					$user_url = $usersinfo["profile_url"];
				}
			}
			if ($about==""){
				$about = $usersinfo["about_me"];
			}
			//$email = $usersinfo["proxied_email"];
			if (isset($usersinfo["current_location"])){		
				if ($location_city==""){
					$location_city = $usersinfo["current_location"]["city"];
				}
				if($location_state ==""){
					$location_state = $usersinfo["current_location"]["state"];
				}
				if ($location_country==""){
					$location_country = $usersinfo["current_location"]["country"];
				}
				if ($location_zip==""){
					$location_zip = $usersinfo["current_location"]["zip"];
				}
			}
			if ($company_name =="" && isset($usersinfo["work_history"]) && isset($usersinfo["work_history"][0])){
				$company_name = $usersinfo["work_history"][0]["company_name"];
			}
	
			if ((!isset($birthday) || $birthday=="") && isset($usersinfo["birthday"]) && $usersinfo["birthday"]!=""){		
					$birthday = 0;
					$day = 0;
					$month = 0 ;
					$year = 0;
					$birthday = strtotime($usersinfo["birthday"]);
					$day = date("j",$birthday);
					$month = date("n",$birthday);
					$year = date("Y",$birthday);
			}
			if ($sex ==""){
				$sex = $usersinfo["sex"];
			}
			if (isset($usersinfo["locale"]) && $usersinfo["locale"]!=""){
				$localearray = explode("_",$usersinfo["locale"]);
				$locale = $localearray[0];
			}else{
				$locale = "en";
			}
		}else{
			echo "No ha sido posible conectar con Facebook";
			exit;
		}
	}
	$fb_form_fields = fb_get_option('fb_form_fields');
	$fb_form_fields_bool = array();
	global $fb_reg_formfields;
	if ($fb_reg_formfields){
		foreach($fb_reg_formfields as $field){
		 	$pos = strrpos($fb_form_fields, ";".$field.";");
			$fb_form_fields_bool[$field] = true;
			if (is_bool($pos) && !$pos) { 
				$fb_form_fields_bool[$field] = false;
			}
		}
	}
?>	
<script>
fb_errormsgcontainer = "#errormsgcontainerthick";
</script>	
<div class="fbconnect_regform">
<div id="errormsgcontainerthick" class="errormsgcontainer"></div>
<?php if (false && !isset($show_userphoto) || $show_userphoto) : ?>		
<div class="fbconnect_reghead">
	<div class="fbconnect_userpicmain">
		<fb:profile-pic uid="<?php echo $fb_user; ?>" size="square" facebook-logo="false" linked="false"></fb:profile-pic>
	</div>
	<div class="titlepassport"><?php _e('User profile', 'fbconnect') ?></div>
			
</div>

<?php endif; 

/*if (!is_user_logged_in()){
	global $fb_showwplogin;
	$fb_showwplogin = "off";
	include("fbconnect_widget_login.php");

	echo "Or fill the registration form:<br/>";
		echo "<br/>";
}*/
$registered = false;
$validationrules = "{";
$separator = "";
?>

<div class="fbconnect_profiletexts">
<form id="fbregisterform" name="fbregisterform" class="workspaceform" action="<?php echo fb_get_option('siteurl')."/index.php?fbconnect_action=register_update"; ?>" method="post">

<?php if ($fb_form_fields_bool["name"] || $registered) : 
	$validationrules .= "userappname: {required: true,minlength: 5, maxlength: 250 }";
	$separator = ",";
?>		
		<span id="fbnamefield" class="fbformfield">
			<label class="labelform" for="name"><?php _e('Name:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="userappname" id="userappname" value="<?php echo $name;?>"/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["nickname"] || $registered) : 
	$validationrules .= $separator." nickname: {required: true,minlength: 5, maxlength: 250 }";
	$separator = ",";
?>		
		<span id="fbnicknamefield" class="fbformfield">
			<label class="labelform" for="nickname"><?php _e('Nickname:', 'fbconnect') ?> </label>
		    <input class="input inputlong" type="text" name="nickname" id="nickname" value="<?php echo $nickname;?>"/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["email"] || $registered) : 
	$validationrules .= $separator." email: {required: true,email: true }";
	$separator = ",";
?>		
		<span id="fbemailfield" class="fbformfield">
			<label class="labelform" for="email"><?php _e('Email:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="email" id="email" value="<?php echo $email;?>"/>	
		</span>
<?php endif; ?>
<?php if (false && $fb_form_fields_bool["email"] || $registered) : 
	$validationrules .= $separator.' confirmemail: {required: true,email: true,equalTo: "#email" }';
	$separator = ",";
?>		
		<span id="fbconfirmemailfield" class="fbformfield">
			<label class="labelform" for="confirmemail"><?php _e('Confirm email:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="confirmemail" id="confirmemail" value=""/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["password"] || $registered) : 
	if (is_user_logged_in()){
		$validationrules .= $separator." password: {minlength: 5, maxlength: 50 }";
	}else{
		$validationrules .= $separator." password: {required: true,minlength: 5, maxlength: 50 }";
	}
	$separator = ",";
?>		
		<span id="fbpasswordfield" class="fbformfield">
			<label class="labelform" for="password"><?php _e('Password:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="password" name="password" id="password" value=""/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["password"] || $registered) : 
	if (is_user_logged_in()){
		$validationrules .= $separator.' confirmpassword: {equalTo: "#password" }';
	}else{
		$validationrules .= $separator.' confirmpassword: {required: true,equalTo: "#password" }';
	}
	$separator = ",";
?>		
		<span id="fbconfirmpasswordfield" class="fbformfield">
			<label class="labelform" for="confirmpassword"><?php _e('Confirm password:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="password" name="confirmpassword" id="confirmpassword" value=""/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["sex"] || $registered) : ?>		
		<span id="fbsexfield" class="fbformfield">
			<label class="labelform" for="sex"><?php _e('Sex:', 'fbconnect') ?> </label>
			<select class="input inputlong" id="sex" name="sex">
				<option selected="" value=""></option>
				<option value="male" <?php if($sex=="male") echo "selected" ?> ><?php _e('Male', 'fbconnect') ?></option>
				<option value="female" <?php if($sex== "female") echo "selected" ?> ><?php _e('Female', 'fbconnect') ?></option>
			</select>
		</span>		
<?php endif; ?>
<?php if ($fb_form_fields_bool["birthdate"] || $registered) : 
    $currentyear = date("Y")-14;
	//$validationrules .= $separator.' birthdate_day: {required: true,min: 1 }';
	//$validationrules .= $separator.' birthdate_month: {required: true,min: 1 }';
	$validationrules .= $separator.' birthdate_year: {required: true,min: 1910, max: '.$currentyear.'}';
	$separator = ",";
?>		
		<span id="fbbirthdatefield" class="fbformfield">
				<label class="labelform" for="birthdate"><?php _e('Birdthdate:', 'fbconnect') ?> </label>
			
				<select class="input inputlong fb_dateselect" style="width:75px;margin-right:2px;" id="birthdate_day" name="birthdate_day">
				<option selected="" value="00"><?php _e('Day', 'fbconnect') ?></option>
				<?php
				 for($i=1;$i<=31;$i++){
					echo "<option value=\"$i\" ";
					if ($day==$i) echo "selected";
					echo ">$i</option>";
				}
				?>
				</select>
				<select class="input inputlong" style="width:90px;margin-right:2px;" class="fb_dateselect" id="birthdate_month" name="birthdate_month">
				<option value="00"><?php _e('Month', 'fbconnect') ?></option>
				<?php
				 global $wp_locale;
				 $months = $wp_locale->month;
				 for($i=1;$i<=12;$i++){
					echo "<option value=\"$i\" ";
					if ($month==$i) echo "selected";
					echo ">".$months[substr("0".$i,-2,2)]."</option>";
				}
				?>
				</select>
				<select class="input inputlong fb_dateselect" style="width:75px;"  id="birthdate_year" name="birthdate_year">
				<option selected="" value="0000"><?php _e('Year', 'fbconnect') ?></option>
				<?php
				 for($i=1910;$i<=2010;$i++){
					echo "<option value=\"$i\" ";
					if ($year==$i) echo "selected";
					echo ">".$i."</option>";
				}
				?>					
				</select>								
				
			</span>	
<?php endif; ?>
<?php if ($fb_form_fields_bool["user_url"] || $registered) : ?>		
			<span id="fbuserurlfield" class="fbformfield">
				<label class="labelform" for="user_url"><?php _e('Web:', 'fbconnect') ?> </label>
				<input class="input inputlong" type="text" name="user_url" id="user_url" value="<?php echo $user_url;?>"/>	
			</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["location_city"] && $registered) : ?>		
		<span id="fblocationcityfield" class="fbformfield">
				<label class="labelform" for="location_city"><?php _e('City:', 'fbconnect') ?> </label>
				<input class="input inputlong" type="text" name="location_city" id="location_city" value="<?php echo $location_city;?>"/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["location_state"] && $registered) : ?>	
		<span id="fblocationstatefield" class="fbformfield">
				<label class="labelform" for="location_state"><?php _e('State:', 'fbconnect') ?> </label>
				<input class="input inputlong" type="text" name="location_state" id="location_state" value="<?php echo $location_state;?>"/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["location_country"] && $registered) : ?>	
		<span id="fblocationcountryfield" class="fbformfield">
			<label class="labelform" for="location_country"><?php _e('Country:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="location_country" id="location_country" value="<?php echo $location_country;?>"/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["location_zip"] && $registered) : ?>	
		<span id="fblocationzipfield" class="fbformfield">
			<label class="labelform" for="location_zip"><?php _e('ZIP:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="location_zip" id="location_zip" value="<?php echo $location_zip;?>"/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["company_name"] && $registered) : ?>		
		<span id="fbcompanyfield" class="fbformfield">
			<label class="labelform" for="company_name"><?php _e('Company:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="company_name" id="company_name" value="<?php echo $company_name;?>"/>	</td>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["phone"] && $registered) : ?>		
		<span id="fbphonefield" class="fbformfield">
			<label class="labelform" for="phone"><?php _e('Phone:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="phone" id="phone" value="<?php echo $phone;?>"/>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["twitter"] && $registered) : ?>		
		<span id="fbtwitterfield" class="fbformfield">
			<label class="labelform" for="twitter"><?php _e('Twitter:', 'fbconnect') ?> </label>
			<input class="input inputlong" type="text" name="twitter" id="twitter" value="<?php echo $twitter;?>"/>	</td>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["about"] || $registered) : ?>		
		<span id="fbaboutfield" class="fbformfield">
			<label class="labelform" for="about"><?php _e('About me:', 'fbconnect') ?></label>
			<textarea class="input inputlong textareaform" type="text" name="about" id="about" cols=30 rows=2><?php echo $about;?></textarea>	</td>	
		</span>
<?php endif; ?>
<?php if ($fb_form_fields_bool["locale"] || $registered) : ?>		
		<span id="fblocalefield" class="fbformfield">				
			<label class="labelform" for="locale"><?php _e('Language:', 'fbconnect') ?> </label>
			<select id="locale" name="locale" class="input inputsmall">
				<option value="en_US" <?php if($locale=="en_US") echo "selected" ?> ><?php _e('English', 'fbconnect') ?></option>
				<option value="es_ES" <?php if($locale== "es_ES") echo "selected" ?> ><?php _e('Spanish', 'fbconnect') ?></option>
			</select>
		</span>	
<?php endif; ?>
<?php 
	if (isset($custom_fields) && $custom_fields!=""){
		echo $custom_fields;
	}
?>
<?php if (!is_user_logged_in() && ($fb_form_fields_bool["terms"] && !$registered)) : 
	$validationrules .= $separator.' terms: {required: true }';
	$separator = ",";
	if(fb_get_option('fb_terms_page')!=""){
		$termspermalik =  get_permalink(fb_get_option('fb_terms_page'));
	}else{
		$termspermalik = fb_get_option('siteurl'). __("/privacy-policy","fbconnect");
	}
?>		
		<span id="fbtermsfield" class="fbformfield">
			<input type="checkbox" name="terms" id="terms"/>
			<a href="#" onclick="fb_windowopen('<?php echo $termspermalik; ?>','TermsAndConditions');return false;"><?php _e('I agree to the Terms of Use and Privacy Policy', 'fbconnect') ?></a>
		</span>
<?php endif; ?>
		<?php if (isset($custom_vars) && $custom_vars!="") : ?>		
		<input type="hidden" name="custom_vars" id="custom_vars" value="<?php echo $custom_vars; ?>"/>
		<?php endif; ?>
		<div class="fbdialogbuttons" id="fbdialogbuttons">
		<span id="fbsubmitfield" class="fbformfield">
			<input type="submit" class="fbconnect_sendbutton wsbutton" name="send" id="send" value="<?php _e('Send', 'fbconnect') ?>">	

			<?php if (!isset($fb_show_cancel) || $fb_show_cancel) : ?>	
				<input type="button" name="cancel" class="wsbuttonSecondary" id="cancel" onclick="tb_remove();" value="<?php _e('Cancel', 'fbconnect') ?>"> 
			<?php endif; ?>	
			<input type="hidden" name="fbajaxregister" id="fbajaxregister" value="1"/>
		</span>
		</div>
		
</form>		
	</div>

</div>
<?php 
$validationrules .= "}";
if (class_exists('sjWorkspacesLogic')):
 ?>	
<script>

function submitregformajax(){
	fb_registeruser(false);
	return false;
}
var regformvalidator;
function validateForm(){
	regformvalidator = jQuery("#fbregisterform").validate({
		submitHandler: submitregformajax,
		invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                        error1 = validator.errorList[0];
                        regformvalidator.errorList = new Array();
                        regformvalidator.errorList[0] = error1;
                        //jQuery(".error").remove();
                    }
               },
		rules: <?php echo $validationrules;?>,
		messages: {
			userappname: {
				required: "<?php _e('Please enter your name', 'fbconnect') ?>",
				minlength: "<?php _e('Your name must consist of at least 5 characters', 'fbconnect') ?>",
				maxlength: "<?php _e('Your name must consist of a max 250 characters', 'fbconnect') ?>"
			},
			email: "<?php _e('Please enter a valid email', 'fbconnect') ?>",
			confirmemail: {
				required: "<?php _e('Please your email again', 'fbconnect') ?>",
				equalTo: "<?php _e('Please enter the same email as above', 'fbconnect') ?>"
			},
			password: {
				required: "<?php _e('Please provide a password', 'fbconnect') ?>",
				minlength: "<?php _e('Your password must be at least 5 characters long', 'fbconnect') ?>"
			},
			confirmpassword: {
				required: "<?php _e('Please provide a password', 'fbconnect') ?>",
				minlength: "<?php _e('Your password must be at least 5 characters long', 'fbconnect') ?>",
				equalTo: "<?php _e('Please enter the same password as above', 'fbconnect') ?>"
			},
			birthdate_year:{
				required: "<?php _e('Please provide a birth date', 'fbconnect') ?>",
				max: "<?php _e('You must be 14 or older', 'fbconnect') ?>",
				min: "<?php _e('Please select your birthdate', 'fbconnect') ?>",
			}
				
		}
	});
}

validateForm();


</script>
<?php
endif;

if (!fb_get_option('fb_connect_use_thick')){
	if (!isset($_REQUEST["modal"]) || $_REQUEST["modal"]=="false"){
		echo '</div>';
		get_sidebar();
		get_footer();
	}
}
?>