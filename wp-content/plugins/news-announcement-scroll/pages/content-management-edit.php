<div class="wrap">
<?php
$did = isset($_GET['did']) ? $_GET['did'] : '0';

// First check if ID exist with requested ID
$sSql = $wpdb->prepare(
	"SELECT COUNT(*) AS `count` FROM ".WP_G_NEWS_ANNOUNCEMENT."
	WHERE `gNews_id` = %d",
	array($did)
);
$result = '0';
$result = $wpdb->get_var($sSql);

if ($result != '1')
{
	?><div class="error fade"><p><strong>Oops, selected details doesn't exist.</strong></p></div><?php
}
else
{
	$gNews_errors = array();
	$gNews_success = '';
	$gNews_error_found = FALSE;
	
	$sSql = $wpdb->prepare("
		SELECT *
		FROM `".WP_G_NEWS_ANNOUNCEMENT."`
		WHERE `gNews_id` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'gNews_text' => $data['gNews_text'],
		'gNews_order' => $data['gNews_order'],
		'gNews_status' => $data['gNews_status'],
		'gNews_expiration' => $data['gNews_expiration'],
		'gNews_date' => $data['gNews_date'],
		'gNews_type' => $data['gNews_type']
	);
}
// Form submitted, check the data
if (isset($_POST['gNews_form_submit']) && $_POST['gNews_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('gNews_form_edit');
	
	$form['gNews_text'] = isset($_POST['gNews_text']) ? $_POST['gNews_text'] : '';
	if ($form['gNews_text'] == '')
	{
		$gNews_errors[] = __('Please enter the news.', WP_gNews_UNIQUE_NAME);
		$gNews_error_found = TRUE;
	}
	
	$form['gNews_order'] = isset($_POST['gNews_order']) ? $_POST['gNews_order'] : '';
	if ($form['gNews_order'] == '')
	{
		$gNews_errors[] = __('Please enter the display order.', WP_gNews_UNIQUE_NAME);
		$gNews_error_found = TRUE;
	}

	$form['gNews_expiration'] = isset($_POST['gNews_expiration']) ? $_POST['gNews_expiration'] : '';
	$form['gNews_status'] = isset($_POST['gNews_status']) ? $_POST['gNews_status'] : '';
	$form['gNews_type'] = isset($_POST['gNews_type']) ? $_POST['gNews_type'] : '';
	$form['gNews_date'] = 'now()';

	//	No errors found, we can add this Group to the table
	if ($gNews_error_found == FALSE)
	{	
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_G_NEWS_ANNOUNCEMENT."`
				SET `gNews_text` = %s,
				`gNews_order` = %s,
				`gNews_status` = %s,
				`gNews_date` = %s,
				`gNews_expiration` = %s,
				`gNews_type` = %s
				WHERE gNews_id = %d
				LIMIT 1",
				array($form['gNews_text'], $form['gNews_order'], $form['gNews_status'], $form['gNews_date'], $form['gNews_expiration'], $form['gNews_type'], $did)
			);
		$wpdb->query($sSql);
		
		$gNews_success = 'Details was successfully updated.';
	}
}

if ($gNews_error_found == TRUE && isset($gNews_errors[0]) == TRUE)
{
?>
  <div class="error fade">
    <p><strong><?php echo $gNews_errors[0]; ?></strong></p>
  </div>
  <?php
}
if ($gNews_error_found == FALSE && strlen($gNews_success) > 0)
{
?>
  <div class="updated fade">
    <p><strong><?php echo $gNews_success; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=news-announcement-scroll">Click here</a> to view the details</strong></p>
  </div>
  <?php
}
?>
<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/news-announcement-scroll/gAnnounce/gAnnounceform.js"></script>
<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/news-announcement-scroll/gAnnounce/noenter.js"></script>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo WP_gNews_TITLE; ?></h2>
	<form name="gNews_form" method="post" action="#" onsubmit="return gNews_submit()"  >
      <h3>Update news details</h3>
 		
	  <label for="tag-image">Enter announcement text</label>
      <textarea name="gNews_text" cols="115" rows="6" id="gNews_text"><?php echo esc_html(stripslashes($form['gNews_text'])); ?></textarea>
      <p>Enter your news and announcement text</p>
	  
      <label for="tag-link">Display order</label>
      <input name="gNews_order" type="text" id="gNews_order" value="<?php echo $form['gNews_order']; ?>" maxlength="2" />
      <p>What order should the news be played in. should it come 1st, 2nd, 3rd, etc.</p>
	  
	  <label for="tag-display-status">Display status</label>
      <select name="gNews_status" id="gNews_status">
		<option value='YES' <?php if($form['gNews_status']=='YES') { echo 'selected="selected"' ; } ?>>Yes</option>
        <option value='NO' <?php if($form['gNews_status']=='NO') { echo 'selected="selected"' ; } ?>>No</option>
      </select>
	  <p>Do you want to show this news in front end?</p>
	  
      <label for="tag-select-gallery-group">Select news type/group</label>
		<select name="gNews_type" id="gNews_type">
		<?php
		$sSql = "SELECT distinct(gNews_type) as gNews_type FROM `".WP_G_NEWS_ANNOUNCEMENT."` order by gNews_type";
		$myDistinctData = array();
		$arrDistinctDatas = array();
		$myDistinctData = $wpdb->get_results($sSql, ARRAY_A);
		$i = 0;
		foreach ($myDistinctData as $DistinctData)
		{
			$arrDistinctData[$i]["gNews_type"] = strtoupper($DistinctData['gNews_type']);
			$i = $i+1;
		}
		for($j=$i; $j<$i+5; $j++)
		{
			$arrDistinctData[$j]["gNews_type"] = "GROUP" . $j;
		}
		$arrDistinctData[$j+1]["gNews_type"] = "WIDGET";
		$arrDistinctData[$j+2]["gNews_type"] = "SAMPLE";
		$arrDistinctDatas = array_unique($arrDistinctData, SORT_REGULAR);
		foreach ($arrDistinctDatas as $arrDistinct)
		{
			if(strtoupper($form['gNews_type']) == strtoupper($arrDistinct["gNews_type"]) ) 
			{ 
				$selected = "selected='selected'"; 
			}
			?>
			<option value='<?php echo $arrDistinct["gNews_type"]; ?>' <?php echo $selected; ?>><?php echo strtoupper($arrDistinct["gNews_type"]); ?></option>
			<?php
			$selected = "";
		}
		?>
		</select>
      <p>This is to group the news. Select your news group. </p>        
	  
	  <label for="tag-display-order">Expiration date</label>
      <input name="gNews_expiration" type="text" id="gNews_expiration" value="<?php echo $form['gNews_expiration']; ?>" maxlength="10" />
      <p>Please enter the expiration date in this format YYYY-MM-DD</p>
		
      <input name="gNews_id" id="gNews_id" type="hidden" value="">
      <input type="hidden" name="gNews_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button-primary" value="Update Details" type="submit" />
        <input name="publish" lang="publish" class="button-primary" onclick="gNews_redirect()" value="Cancel" type="button" />
        <input name="Help" lang="publish" class="button-primary" onclick="gNews_help()" value="Help" type="button" />
      </p>
	  <?php wp_nonce_field('gNews_form_edit'); ?>
    </form>
</div>
<p class="description"><?php echo WP_gNews_LINK; ?></p>
</div>