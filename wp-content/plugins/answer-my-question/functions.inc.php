<?php 
if ( !defined('ABSPATH') ) define('ABSPATH', dirname(__FILE__));
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $amq_db_version;
$amq_db_version = "1.3";
$plugindir = plugin_dir_url(__FILE__);

//Update the question data
if(isset($_POST['posted']) && $_POST['posted'] == 1 && $_POST['id']){
	updateQuestionData($_POST);

	//Show success notification
	echo '
	<div class="updated"> 
		<p><strong>'.__("Updates Saved!").'</strong></p>
	</div>';
}

/**
 *
 * Runs the required SQL when the plugin is activated
 *
 * @param    none
 * @return	 none
 */
function amq_install() {
   global $wpdb,$amq_db_version,$table_name;
   $table_name = $wpdb->prefix . "answer_my_question";
      
   $sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  date_asked DATETIME NOT NULL,
		  date_response DATETIME NOT NULL,
		  user_name VARCHAR(60) NOT NULL,
		  user_email VARCHAR(60) NOT NULL,
		  url VARCHAR(60) DEFAULT '' NOT NULL,
		  subject VARCHAR(60) NOT NULL,
		  question TEXT NOT NULL,
		  answer TEXT NOT NULL,
		  answered tinyint(1) NOT NULL,
		  notify_user tinyint(1) NOT NULL,
		  show_on_site tinyint(1) NOT NULL DEFAULT '1',
		  UNIQUE KEY id (id));";	

   dbDelta($sql);
   add_option("amq_db_version", $amq_db_version);
   
   //Update Database Schema
   $installed_ver = get_option("amq_db_version");
   if($installed_ver != $amq_db_version) {
	   $sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  date_asked DATETIME NOT NULL,
			  date_response DATETIME NOT NULL,
			  user_name VARCHAR(60) NOT NULL,
			  user_email VARCHAR(60) NOT NULL,
			  url VARCHAR(60) DEFAULT '' NOT NULL,
			  subject VARCHAR(60) NOT NULL,
			  question TEXT NOT NULL,
			  answer TEXT NOT NULL,
			  answered tinyint(1) NOT NULL,
			  notify_user tinyint(1) NOT NULL,
			  show_on_site tinyint(1) NOT NULL DEFAULT '1',
			  UNIQUE KEY id (id));";	
	   
	   dbDelta($sql);
	   update_option("amq_db_version", $amq_db_version);
   }
}

/**
 *
 * Compares the current database version with the version recorded as installed in the users database. If they don't match, amq_install is run to update the schema
 *
 * @param    none
 * @return	 none
 */
function amq_update_db_check() {
    global $amq_db_version;
    if (get_site_option('amq_db_version') != $amq_db_version) {
        amq_install();
    }
}


/**
 *
 * Adds top level and sub level menu item for the plugin. Editor and above can use the plugin
 *
 * @param    none
 * @return	 none
 */
function register_amq_menu_page() {
   add_menu_page("Answer My Question", "Answer My Question", "delete_pages", "answer-my-question", "answerMyQuestionView");
   add_submenu_page("answer-my-question", "", "Settings", "delete_pages", "answer-my-question-settings", "answerMyQuestionSettings");
}

/**
 *
 * Enqueues the plugin specific JavaScript and CSS to to the head for the main admin page
 *
 * @param    none
 * @return	 none
 */
function loadMainScripts() {
	global $plugindir;
	wp_enqueue_script('', $plugindir . '/js/main.js');
	echo "<link rel='stylesheet' href='".plugins_url('css/admin_main.css', __FILE__)."' type='text/css' />\n";
}

/**
 *
 * Enqueues the plugin specific JavaScript and CSS to the public facing site
 *
 * @param    none
 * @return	 none
 */
function loadClientAssets(){
	echo "<link rel='stylesheet' href='".plugins_url('css/answer_my_question_site_modal.css', __FILE__)."' type='text/css' />\n<link rel='stylesheet' href='".plugins_url('css/answer_my_question_full_list.css', __FILE__)."' type='text/css' />\n";
	wp_enqueue_script('', plugins_url('js/answer_my_question_scripts.js', __FILE__), false, false, true);
}

/**
 *
 * Inserts the modal markup into the public facing site's footer
 *
 * @param    none
 * @return	 none
 */
function insertModal() {
	global $wpdb;
	$table_name = $wpdb->prefix . "options";
	
	//Get the plugin options
	$result = $wpdb->get_results("SELECT option_value FROM $table_name WHERE option_name = 'amq_option_item' LIMIT 1;");
	
	if(count($result) > 0){
		foreach($result as $row){
			$modalDetails = unserialize($row->option_value);
		}
	}

	$modalTitle = (!empty($modalDetails['title']) ? sprintf( __('%s'), $modalDetails['title']) : __('Answer My Question'));
	$modalBody = (!empty($modalDetails['body']) ? str_replace("\n", "<br>", sprintf( __('%s'), $modalDetails['body'])) : __('Please fill out the form below.'));
	
    echo '<div id="answer-my-question-modal-bg"></div>
<div id="answer-my-question-modal">
	<div class="inner">
		<h1>'.$modalTitle.'</h1>
		<span class="close">'.__("CLOSE").'</span>
		<h2 id="message-sent">'.__("Your question has been sent!").'</h2>
		<img id="sending-loader" src="'.WP_PLUGIN_URL.'/answer-my-question/images/ajax-loader.gif" alt="" />
		<div class="form-contents">
		<p>'.$modalBody.'</p>
		<form id="question-form" action="" method="post">
	<table>
		<tr>
			<td class="field">'.__("Name ").' <span class="required">*</span></td>
			<td><input type="text" name="name" /></td>
		</tr>
		<tr>
			<td class="field">'.__("Email ").' <span class="required">*</span></td>
			<td><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td class="field">'.__("URL").'
			<small>('.__("include").' http://)</small></td>
			<td><input type="text" name="url" /></td>
		</tr>
		<tr>
			<td class="field">'.__("Subject").' <span class="required">*</span></td>
			<td><input type="text" name="subject" /></td>
		</tr>
		<tr>
			<td class="field">'.__("Question").' <span class="required">*</span></td>
			<td><textarea name="question"></textarea><br>
			<label for="notify">'.__("Notify Me On Answer").':</label> <input type="checkbox" name="notify" id="notify" value="1" />
			</td>
		</tr>
	</table>
	
	<span class="legend"><span class="required">*</span> '.__("Required Field").'</span>
	<input type="hidden" name="post_location" id="post_location" value="'.WP_PLUGIN_URL.'/answer-my-question/record_question.php" />
	<button class="clean-gray" id="send">'.__("Send").'</button>
	</form>
	</div>
	</div>
</div>';
}

/**
 *
 * Front end markup for the admin panel screen
 *
 * @param    none
 * @return	 none
 */
function answerMyQuestionView() {
	loadMainScripts();
	global $plugindir,$wpdb;
	$table_name = $wpdb->prefix . "answer_my_question";
	
	//Get a list of all questions
	$result = $wpdb->get_results("SELECT id, date_asked, user_name, user_email, subject, show_on_site, answered FROM $table_name ORDER BY date_asked DESC");
	echo '<div class="wrap">
			<h2>'.__("Answer My Question").'</h2>
			<p>Here you can administer all questions submitted by your site users.</p>
			<p>&nbsp;</p>';
			
	if(count($result) > 0){
		echo '<div id="pager" class="pager">
					<img src="'.$plugindir.'/images/first.png" class="first">
					<img src="'.$plugindir.'/images/prev.png" class="prev">
					<input type="text" class="pagedisplay">
					<img src="'.$plugindir.'/images/next.png" class="next">
					<img src="'.$plugindir.'/images/last.png" class="last">
					<select class="pagesize">
						<option selected="selected" value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="50">50</option>
					</select>
			</div>
			<table id="amq_list" class="tablesorter"> 
			<thead> 
			<tr> 
				<th>'.__("Date Asked").'</th>
				<th>'.__("Status").'</th>				
				<th>'.__("Name").'</th> 
				<th>'.__("Email").'</th> 
				<th>'.__("Subject").'</th> 
				<th><span title="'.__("Set to NO for each question you don\'t want to display. Click the table table cell to toggle the display state").'" class="info">'.__("Show On Site").'</span></th>
				<th>'.__("Actions").'</th> 
			</tr> 
			</thead> 
			<tbody>';
			foreach($result as $row){
				$answerStatus = ($row->answered == 0 ? __('unanswered') : __('answered'));
				$displayStatus = ($row->show_on_site == 0 ? '<span class="display_no">'.__("No").'</span>' : '<span class="display_yes">'.__("Yes").'</span>');
				echo '<tr> 
						<td>'.date("m/d/y", strtotime($row->date_asked)).'</td> 
						<td class="'.$answerStatus.'">'.ucfirst($answerStatus).'</td> 
						<td>'.$row->user_name.'</td> 
						<td>'.$row->user_email.'</td>
						<td>'.$row->subject.'</td>
						<td class="display_status" rel="'.$row->id.'">'.$displayStatus.'</td> 
						<td class="actions">
						<a href="" class="answer" rel="'.$row->id.'"><img src="'.$plugindir.'/images/icon_answer.png" alt="'.__("Answer Question").'" title="'.__("Answer This Question").'"></a>
						<a href="" class="delete" rel="'.$row->id.'"><img src="'.$plugindir.'/images/icon_delete.png" alt="'.__("Delete Question").'" title="'.__("Delete This Question").'"></a></td>
					 </tr>';
			}
			echo '</tbody> 
			</table>';
	}else{
		echo '<strong>'.__("Looks like you don't have any questions yet!").'</strong>';
	}
		echo '</div>
			  <input type="hidden" value="'.$plugindir.'" id="plugin_path">';
} 

/**
 *
 * Updates the database record for the question
 *
 * @param	 array		$data: Array containing all posted question data
 * @return	 boolean 	True if row has been updated. False otherwise
 */
function updateQuestionData($data=array()){
	global $wpdb;
	$table_name = $wpdb->prefix . "answer_my_question";
	
	$questionID = $data['id'];
	$notifyUser = $data['notify'];
	$userEmail = $data['user_email'];
	unset($data['id'], $data['posted'], $data['notify'], $data['user_email']);
	$data['date_response'] = date("Y-m-d G:i:s");
	$data['answered'] = 1;
	
	$wpdb->update(
	  $table_name,
	  $data,
	  array('id' => $questionID)
	);
	
	if($wpdb->rows_affected > 0){
		//Should user be notified?
		if($notifyUser == 1){
			
			$result = $wpdb->get_results("SELECT answer FROM $table_name WHERE id = $questionID LIMIT 1;");	
			foreach($result as $row){
				$response = $row->answer;
				
			}
			sendNotificationMail($userEmail, $data['subject'], $data['date_response'], $response);
		}
	}
	return true;
}

/**
 *
 * Send an email notification to a user that requested to be notified
 *
 * @param	 string		$email: Email Address
 * @param	 string		$subject: The subject of the users question
 * @param	 string		$responseDate: Date of admin response. Should be in MySQL datetime format
 * @return	 boolean 	True if email was sent. False otherwise
 */
function sendNotificationMail($email, $subject, $responseDate, $response){
	$to  = $email;
	$emailSubject = 'You\'re Question Has Been Answered!';
	$message = '
	<html>
	<head>
	  <title>You\'re Question Has Been Answered!</title>
	</head>
	<body>
		<p>Hello,<br>
		This is an automated email from <a href="'.site_url().'">'.get_bloginfo("name").'</a>.</p> 
		<p>Your quesiton titled: <strong>'.$subject.'</strong>, has been answered on <strong>'.date("F j, Y", strtotime($responseDate)).'</strong>.</p>
		<hr>
		<p><em>"'.$response.'"</em></p>
	</body>
	</html>';

	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Additional headers
	// NOTE: If multiple admins, you need to explode here and change $headers
	$headers .= 'To: '.$email.' <'.$email.'>' . "\r\n";
	$headers .= 'From: '.get_bloginfo('admin_email').' <'.get_bloginfo('admin_email').'>' . "\r\n";
	
	// Mail it
	if(mail($to, $emailSubject, $message, $headers)){
		return true;
	}else{	
		return false;
	}
}

/**
 *
 * Settings options page
 *
 * @param    none
 * @return	 none
 */
function answerMyQuestionSettings() {
	loadMainScripts();
	if($_GET['settings-updated'] == true){
		echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
				<p><strong>'.__("Settings saved").'.</strong></p>
			  </div>';
	}
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2><?php _e("Settings");?></h2>
		<form method="post" action="options.php">
			<?php settings_fields('amq_options'); ?>
			<?php $options = get_option('amq_option_item'); ?>
			<table class="form-table">
				<tr valign="top"><th scope="row"><?php _e("Modal title");?></th>
					<td><input class="regular-text" type="text" name="amq_option_item[title]" value="<?php echo (isset($options['title']) ? $options['title'] : ''); ?>" /> <span class="description"><?php _e("The title text of the question modal window");?></span></td>
				</tr>
				<tr valign="top"><th scope="row"><?php _e("Modal body");?></th>
					<td><textarea style="width: 25em; height: 9em; vertical-align: top;" name="amq_option_item[body]"><?php echo (isset($options['body']) ? $options['body'] : ''); ?></textarea> <span class="description"><?php _e("Intro text for the modal window body");?></span></td>
				</tr>
				<tr valign="top"><th scope="row"><?php _e("Notify me of new questions");?></th>
					<td><input id="notify" style="width: 1em;" name="amq_option_item[notify]" type="checkbox" value="1" <?php checked('1', (isset($options['notify']) ? $options['notify'] : '')); ?> /></td>
				</tr>
				<tr valign="top"><th scope="row"><?php _e("Email Address");?></th>
					<td>
					<textarea id="email" style="width: 25em; height: 9em; vertical-align: top;" name="amq_option_item[email]"><?php echo (isset($options['email']) ? $options['email'] : ''); ?></textarea>
					 <span class="description"><?php _e("One email address per line");?>.</span></td>
				</tr>	
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
			</p>
		</form>
	</div>
<?php 
}

/**
 *
 * Shortcode to list all question/answer pairs that have been answered
 *
 * @param    none
 * @return	 Markup for the page
 */
function listAmq(){
	global $wpdb;
	$table_name = $wpdb->prefix . "answer_my_question";

	$result = $wpdb->get_results("SELECT * FROM $table_name WHERE answered = 1 AND show_on_site = 1 ORDER BY date_asked DESC;");
	
	$amqList = '<div id="amq_full_list">';
	
	if(count($result) > 0){
		foreach($result as $row){
			$userName = (!empty($row->url) ? '<a href="'.$row->url.'" target="_blank">'.$row->user_name.'</a>': $row->user_name);
			
			$amqList .= date('m/d/Y', strtotime($row->date_asked)).' - <strong class="asks">'.
			$userName.'</strong> '.__("asks").': <strong>'.$row->subject.'</strong>
			<em class="question">"'.$row->question.'"</em>
			<strong class="answers">'.__("Answer").':</strong>
			<div class="answer">'.$row->answer.'</div>';
		}
	}
	$amqList .= '</div>';
	return $amqList;
}

function amqModal($atts, $content = null) {
   return '<span class="amq_show_modal">' . $content . '</span>';
}

/**
 *
 * Sanitize and validate input
 *
 * @param    array	$input: Array of form values
 * @return	 array	$input: Array of sanitized values for the database
 */
function amq_options_validate($input) {
	//Only valid form values can be passed
	$possible_values = array(
		$input['title'],
		$input['body'],
	);
	
	if(isset($input['notify'])){
		$possible_values[] = $input['notify'];
		$input['notify'] = ($input['notify'] == 1 ? 1 : 0);
	}
	
	if(isset($input['email'])){
		$possible_values[] = $input['email'];
	}
	
	foreach($input as $key=>$value){
		if(!in_array($value, $possible_values)){
			unset($input[$key]);
		}
	}
	
	// No HTML tags
	$input['title'] =  wp_filter_nohtml_kses($input['title']);
	$input['body'] =  wp_filter_nohtml_kses($input['body']);
	
	return $input;
}

/**
 *
 * Loads the jQuery library on the client facing site
 *
 * @param    none
 * @return	 none
 */
function loadjQuery() {
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', false, '1.7.1', true);
		wp_enqueue_script('jquery');
	}
}

load_plugin_textdomain('amq_plugin_I18n', false, basename( dirname( __FILE__ ) ) . '/languages' );
?>
