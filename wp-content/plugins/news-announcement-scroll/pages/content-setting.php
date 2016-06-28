<div class="wrap">
  <div class="form-wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br>
    </div>
    <h2><?php echo WP_gNews_TITLE; ?></h2>
	<h3>Widget setting</h3>
    <?php
	$gNewsAnnouncementtitle = get_option('gNewsAnnouncementtitle');
	$gNewsAnnouncementwidth = get_option('gNewsAnnouncementwidth');
	$gNewsAnnouncementfont = get_option('gNewsAnnouncementfont');
	$gNewsAnnouncementheight = get_option('gNewsAnnouncementheight');
	$gNewsAnnouncementfontsize = get_option('gNewsAnnouncementfontsize');
	$gNewsAnnouncementslidedirection = get_option('gNewsAnnouncementslidedirection');
	$gNewsAnnouncementfontweight = get_option('gNewsAnnouncementfontweight');
	$gNewsAnnouncementslidetimeout = get_option('gNewsAnnouncementslidetimeout');
	$gNewsAnnouncementfontcolor = get_option('gNewsAnnouncementfontcolor');
	$gNewsAnnouncementtextalign = get_option('gNewsAnnouncementtextalign');
	$gNewsAnnouncementtextvalign = get_option('gNewsAnnouncementtextvalign');
	$gNewsAnnouncementnoannouncement = get_option('gNewsAnnouncementnoannouncement');
	$gNewsAnnouncementorder = get_option('gNewsAnnouncementorder');
	$gNewsAnnouncementtype = get_option('gNewsAnnouncementtype');

	
	if (@$_POST['gNews_submit']) 
	{
		//	Just security thingy that wordpress offers us
		check_admin_referer('gNews_form_setting');
			
		$gNewsAnnouncementtitle = stripslashes($_POST['gNewsAnnouncementtitle']);
		$gNewsAnnouncementwidth = stripslashes($_POST['gNewsAnnouncementwidth']);
		$gNewsAnnouncementfont = stripslashes($_POST['gNewsAnnouncementfont']);
		$gNewsAnnouncementheight = stripslashes($_POST['gNewsAnnouncementheight']);
		$gNewsAnnouncementfontsize = stripslashes($_POST['gNewsAnnouncementfontsize']);
		$gNewsAnnouncementslidedirection = stripslashes($_POST['gNewsAnnouncementslidedirection']);
		$gNewsAnnouncementfontweight = stripslashes($_POST['gNewsAnnouncementfontweight']);
		$gNewsAnnouncementslidetimeout = stripslashes($_POST['gNewsAnnouncementslidetimeout']);
		$gNewsAnnouncementfontcolor = stripslashes($_POST['gNewsAnnouncementfontcolor']);
		$gNewsAnnouncementtextalign = stripslashes($_POST['gNewsAnnouncementtextalign']);
		$gNewsAnnouncementtextvalign = stripslashes($_POST['gNewsAnnouncementtextvalign']);
		$gNewsAnnouncementnoannouncement = stripslashes($_POST['gNewsAnnouncementnoannouncement']);
		$gNewsAnnouncementorder = stripslashes($_POST['gNewsAnnouncementorder']);
		$gNewsAnnouncementtype = stripslashes($_POST['gNewsAnnouncementtype']);
		
		update_option('gNewsAnnouncementtitle', $gNewsAnnouncementtitle );
		update_option('gNewsAnnouncementwidth', $gNewsAnnouncementwidth );
		update_option('gNewsAnnouncementfont', $gNewsAnnouncementfont );
		update_option('gNewsAnnouncementheight', $gNewsAnnouncementheight );
		update_option('gNewsAnnouncementfontsize', $gNewsAnnouncementfontsize );
		update_option('gNewsAnnouncementslidedirection', $gNewsAnnouncementslidedirection );
		update_option('gNewsAnnouncementfontweight', $gNewsAnnouncementfontweight );
		update_option('gNewsAnnouncementslidetimeout', $gNewsAnnouncementslidetimeout );
		update_option('gNewsAnnouncementfontcolor', $gNewsAnnouncementfontcolor );
		update_option('gNewsAnnouncementtextalign', $gNewsAnnouncementtextalign );
		update_option('gNewsAnnouncementtextvalign', $gNewsAnnouncementtextvalign );
		update_option('gNewsAnnouncementnoannouncement', $gNewsAnnouncementnoannouncement );
		update_option('gNewsAnnouncementorder', $gNewsAnnouncementorder );
		update_option('gNewsAnnouncementtype', $gNewsAnnouncementtype );
		
		?>
		<div class="updated fade">
			<p><strong>Details successfully updated.</strong></p>
		</div>
		<?php
	}
	?>
	<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/news-announcement-scroll/gAnnounce/gAnnounceform.js"></script>
	<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/news-announcement-scroll/gAnnounce/noenter.js"></script>
    <form name="ssg_form" method="post" action="">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td width="35%">
		<label for="tag-title">Title</label>
		<input name="gNewsAnnouncementtitle" id="gNewsAnnouncementtitle" type="text" value="<?php echo $gNewsAnnouncementtitle; ?>" size="50" />
		<p>This title is only for widget.</p>
		
		<label for="tag-title">Text alignment</label>
		<select name="gNewsAnnouncementtextalign" id="gNewsAnnouncementtextalign">
			<option value='left' <?php if($gNewsAnnouncementtextalign == 'left') { echo 'selected="selected"' ; } ?>>Left</option>
			<option value='center' <?php if($gNewsAnnouncementtextalign == 'center') { echo 'selected="selected"' ; } ?>>Center</option>
			<option value='right' <?php if($gNewsAnnouncementtextalign == 'right') { echo 'selected="selected"' ; } ?>>Right</option>
			<option value='justify' <?php if($gNewsAnnouncementtextalign == 'justify') { echo 'selected="selected"' ; } ?>>Justify</option>
		</select>
		<p>This alignment is for horizontal direction.</p>
		
		<label for="tag-title">Width</label>
		<input name="gNewsAnnouncementwidth" id="gNewsAnnouncementwidth" type="text" value="<?php echo $gNewsAnnouncementwidth; ?>" />
		<p>Enter widget width, only number</p>
		
		<label for="tag-title">Font</label>
		<input name="gNewsAnnouncementfont" id="gNewsAnnouncementfont" type="text" value="<?php echo $gNewsAnnouncementfont; ?>" size="50" />
		<p>Enter news fornt name, Example: verdana, arial, sans-serif</p>
		
		<label for="tag-title">Text alignment</label>
		<select name="gNewsAnnouncementtextvalign" id="gNewsAnnouncementtextvalign">
			<option value='top' <?php if($gNewsAnnouncementtextvalign == 'top') { echo 'selected="selected"' ; } ?>>Top</option>
			<option value='middle' <?php if($gNewsAnnouncementtextvalign == 'middle') { echo 'selected="selected"' ; } ?>>Middle</option>
			<option value='bottom' <?php if($gNewsAnnouncementtextvalign == 'bottom') { echo 'selected="selected"' ; } ?>>Bottom</option>
		</select>
		<p>This alignment is for vertical direction.</p>
		
		<label for="tag-title">Height</label>
		<input name="gNewsAnnouncementheight" id="gNewsAnnouncementheight" type="text" value="<?php echo $gNewsAnnouncementheight; ?>" />
		<p>Enter widget height, only number</p>
		
		<label for="tag-title">Announcement display order</label>
		<select name="gNewsAnnouncementorder" id="gNewsAnnouncementorder">
			<option value='0' <?php if($gNewsAnnouncementorder == '0') { echo 'selected="selected"' ; } ?>>Display order</option>
			<option value='1' <?php if($gNewsAnnouncementorder == '1') { echo 'selected="selected"' ; } ?>>Random Order</option>
		</select>
		<p>Display order: It means display as per order option. Random order: It means random order.</p>
		</td>
		<td width="65%">
		<label for="tag-title">Font size</label>
		<input name="gNewsAnnouncementfontsize" id="gNewsAnnouncementfontsize" type="text" value="<?php echo $gNewsAnnouncementfontsize; ?>" />
		<p>News font size, Example: 13px.</p>
		
		<label for="tag-title">Font color</label>
		<input name="gNewsAnnouncementfontcolor" id="gNewsAnnouncementfontcolor" type="text" value="<?php echo $gNewsAnnouncementfontcolor; ?>" />
		<p>News font color, Example: #000000.</p>
		
		<label for="tag-title">Slide direction</label>
		<select name="gNewsAnnouncementslidedirection" id="gNewsAnnouncementslidedirection">
			<option value='0' <?php if($gNewsAnnouncementslidedirection == '0') { echo 'selected="selected"' ; } ?>>Down to Up</option>
			<option value='1' <?php if($gNewsAnnouncementslidedirection == '1') { echo 'selected="selected"' ; } ?>>Up to Down</option>
		</select>
		<p>Select your slid direction</p>
		
		<label for="tag-title">Font weight</label>
		<select name="gNewsAnnouncementfontweight" id="gNewsAnnouncementfontweight">
			<option value='blod' <?php if($gNewsAnnouncementfontweight == 'blod') { echo 'selected="selected"' ; } ?>>Blod</option>
			<option value='normal' <?php if($gNewsAnnouncementfontweight == 'normal') { echo 'selected="selected"' ; } ?>>Normal</option>
		</select>
		<p>Select font weight.</p>
		
		<label for="tag-title">No announcement text</label>
		<input name="gNewsAnnouncementnoannouncement" id="gNewsAnnouncementnoannouncement" type="text" value="<?php echo $gNewsAnnouncementnoannouncement; ?>"  size="50" />
		<p>Text to show if no news/announcement available in the database.</p>
		
		<label for="tag-title">Slide timeout</label>
		<input name="gNewsAnnouncementslidetimeout" id="gNewsAnnouncementslidetimeout" type="text" value="<?php echo $gNewsAnnouncementslidetimeout; ?>" />
		<p>1000 = 1 second.</p>
		
		<label for="tag-title">News type</label>
		<select name="gNewsAnnouncementtype" id="gNewsAnnouncementtype">
	 	<?php
		$sSql = "SELECT distinct(gNews_type) as gNews_type FROM `".WP_G_NEWS_ANNOUNCEMENT."` order by gNews_type";
		$myDistinctData = array();
		$arrDistinctDatas = array();
		$myDistinctData = $wpdb->get_results($sSql, ARRAY_A);
		$i = 0;
		if(count($myDistinctData) > 0)
		{
			foreach ($myDistinctData as $DistinctData)
			{
				$arrDistinctData[$i]["gNews_type"] = strtoupper($DistinctData['gNews_type']);
				$i = $i+1;
			}
			foreach ($arrDistinctData as $arrDistinct)
			{
				if(strtoupper($gNewsAnnouncementtype) == strtoupper($arrDistinct["gNews_type"]) ) 
				{ 
					$selected = "selected='selected'"; 
				}
				?>
				<option value='<?php echo $arrDistinct["gNews_type"]; ?>' <?php echo $selected; ?>><?php echo strtoupper($arrDistinct["gNews_type"]); ?></option>
				<?php
				$selected = "";
			}
		}
		else
		{
			?><option value='widget'>Widget</option><?php
		}
		?>
      </select>
		<p>Select your news type to display (This option available in news management page).</p>
		</td>
	  </tr>
	</table>
	  <input name="gNews_submit" id="gNews_submit" class="button-primary" value="Submit" type="submit" />
	  <input name="publish" lang="publish" class="button-primary" onclick="gNews_redirect()" value="Cancel" type="button" />
      <input name="Help" lang="publish" class="button-primary" onclick="gNews_help()" value="Help" type="button" />
	  <?php wp_nonce_field('gNews_form_setting'); ?>
    </form>
  </div>
  <br /><p class="description"><?php echo WP_gNews_LINK; ?></p>
</div>
