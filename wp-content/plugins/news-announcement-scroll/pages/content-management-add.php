<div class="wrap">
<?php
$gNews_errors = array();
$gNews_success = '';
$gNews_error_found = FALSE;

// Preset the form fields
$form = array(
	'gNews_text' => '',
	'gNews_order' => '',
	'gNews_status' => '',
	'gNews_expiration' => '',
	'gNews_date' => '',
	'gNews_type' => ''
);

// Form submitted, check the data
if (isset($_POST['gNews_form_submit']) && $_POST['gNews_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('gNews_form_add');
	
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
		$cur_date = date('Y-m-d G:i:s'); 
		$sql = $wpdb->prepare(
			"INSERT INTO `".WP_G_NEWS_ANNOUNCEMENT."`
			(`gNews_text`, `gNews_order`, `gNews_status`, `gNews_date`, `gNews_expiration`, `gNews_type`)
			VALUES(%s, %s, %s, %s, %s, %s)",
			array($form['gNews_text'], $form['gNews_order'], $form['gNews_status'], $form['gNews_date'], $form['gNews_expiration'], $form['gNews_type'])
		);
		$wpdb->query($sql);
		
		$gNews_success = __('Details was successfully added.', WP_gNews_UNIQUE_NAME);
		
		// Reset the form fields
		$form = array(
			'gNews_text' => '',
			'gNews_order' => '',
			'gNews_status' => '',
			'gNews_expiration' => '',
			'gNews_date' => '',
			'gNews_type' => ''
		);
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
	<form name="gAnnouncefrm" method="post" action="#" onsubmit="return _gAnnounce()"  >
      <h3>Add news details</h3>
      
	  <label for="tag-image">Enter announcement text</label>
      <textarea name="gNews_text" cols="115" rows="6" id="txt_news"></textarea>
      <p>Enter your news and announcement text</p>
	  
      <label for="tag-link">Display order</label>
      <input name="gNews_order" type="text" id="gNews_order" value="" maxlength="2" />
      <p>What order should the news be played in. should it come 1st, 2nd, 3rd, etc.</p>
	  
	  <label for="tag-display-status">Display status</label>
      <select name="gNews_status" id="gNews_status">
		<option value='YES'>Yes</option>
        <option value='NO'>No</option>
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
			?><option value='<?php echo $arrDistinct["gNews_type"]; ?>'><?php echo $arrDistinct["gNews_type"]; ?></option><?php
		}
		?>
		</select>
      <p>This is to group the news. Select your news group. </p>        
	  
	  <label for="tag-display-order">Expiration date</label>
      <input name="gNews_expiration" type="text" id="gNews_expiration" value="" maxlength="10" />
      <p>Please enter the expiration date in this format YYYY-MM-DD <br /> 0000-00-00 : Is equal to no expire.</p>
	  
      <input name="gNews_id" id="gNews_id" type="hidden" value="">
      <input type="hidden" name="gNews_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button-primary" value="Insert Details" type="submit" />
        <input name="publish" lang="publish" class="button-primary" onclick="gNews_redirect()" value="Cancel" type="button" />
        <input name="Help" lang="publish" class="button-primary" onclick="gNews_gHelp()" value="Help" type="button" />
      </p>
	  <?php wp_nonce_field('gNews_form_add'); ?>
    </form>
</div>
<p class="description"><?php echo WP_gNews_LINK; ?></p>
</div>