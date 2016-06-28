
<div id="fbfanthickbox" class="tabcontent" style="text-align:center; padding:20px;">

<?php 
$siteUrl= fb_get_option('siteurl');
$usethick = "true";
if (isset($_REQUEST["usethick"]) && $_REQUEST["usethick"]!=""){
	$usethick = $_REQUEST["usethick"];
}
$pageid="";
if (isset($_REQUEST["postid"]) && $_REQUEST["postid"]!=""){
	$pageurl = get_post_meta($_REQUEST["postid"], '_fbconnect_access_page_url', true);
	$pageid = get_post_meta($_REQUEST["postid"], '_fbconnect_access_pageid', true);
	$postfan = get_post( $_REQUEST["postid"]);
}else{
	$pageurl = fb_get_option('fb_fanpage_url');
	$pageid = fb_get_option('fb_fanpage_id');
}

if (isset($_REQUEST["getcontent"]) && $postfan!="" && $postfan->post_status=="publish" && $postfan->post_password==""){
		$fb_user = fb_get_loggedin_user();
		if ( $fb_user ){
			$isfan = fb_pages_isFan($pageid, "".$fb_user);
		}else{
			$isfan = false;
		}
		if($isfan){
			echo $postfan->post_content;
			exit;
		}
}

if (isset($_REQUEST["pageid"]) && $_REQUEST["pageid"]!=""){
	$pageid = $_REQUEST["pageid"];  
}

if (isset($_REQUEST["postid"]) && $_REQUEST["postid"]!=""){
	$texto = get_post_meta($_REQUEST["postid"], 'texto_hazte_fan_introtab', true);
	if ($texto==""){
		$texto = get_post_meta($_REQUEST["postid"] , '_fbconnect_access_page_text', true);
		$textopie = get_post_meta($_REQUEST["postid"] , '_fbconnect_access_page_text_footer', true);
	}
}
if($texto==""){
	$texto = fb_get_option('fb_nofans_thick_headtext');
}
if($textopie==""){
	$textopie = fb_get_option('fb_nofans_thick_footertext');
}

$fb_user = fb_get_loggedin_user();
 
global $pagetabinfo;
echo $texto ?>
		<div id="fanboxlikecontainer">		
		<div id="fanboxlike">
		<?php
		global $pagetabinfo;
		
		if ($fb_user || (FBCONNECT_CANVAS=="tab" && $pageid==$pagetabinfo["id"])) { 
			if($pageurl!=""){?> 
				<div class="fb-like" data-href="<?php echo $pageurl;?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>
			<?php 
			}else if (isset($pageid) && trim($pageid)!=""){
			?>
			 	<fb:like-box profile_id="<?php echo $pageid;?>" show_faces="false" stream="false" header="false"></fb:like-box>
			<?php
			}
?>
<!-- 
<fb:like href="<?php echo $pageurl;?>" send="false" show_faces="false" layout="button_count"></fb:like>
-->
<?php	}else{?>
			<a class="fb_button fb_button_xlarge" onclick="login_fan('<?php echo $pageid;?>','<?php echo $_REQUEST["postid"];?>',<?php echo $usethick;?>);">
			<span class="fb_button_text"><?php _e('Log In');?></span>
			</a>
		<?php }
		?>	 
		</div>
		</div>
<?php 
		$pos = strrpos($pageurl, "?");
		if ($pos === false) {
			$pageurl = $pageurl."?sk=app_".get_appId();
		}else{
			$pageurl = $pageurl."&sk=app_".get_appId();
		}
echo $textopie; ?>
		<script>
			FB.XFBML.parse();
			
			FB.Event.subscribe('edge.create', function(response) {
				<?php if($usethick=="false"){
					$taburl = $siteUrl."/?fbconnect_action=tab&usethick=false&postid=".$_REQUEST["postid"]."&getcontent=true";
				?>
				//top.location = fb_pageurl;
				jQuery('#fbaccesstable').html("Loading...");
				jQuery('#fbaccesstable').load('<?php echo $taburl;?>',function(){
						//alert('cargado');
				});
				<?php }else{?>
				isFbFan = true;
				tb_remove();
				<?php }?>
			});
		
		</script>    
    
    
</div>
	

