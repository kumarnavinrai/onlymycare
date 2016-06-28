<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require_once("../../../wp-config.php");

$user = wp_get_current_user();
$fb_user = fb_get_loggedin_user();

print_r($_REQUEST);
if (isset($_REQUEST["post_id"]) && $_REQUEST["post_id"]!=""){
	//ha compartido
}else{
	//no ha compartido
}
?>
<script>
	window.close();
</script>