<?php 
    $contentArryVideo = array();
    	
    $categories = get_the_category(); $catId = $categories[0]->term_taxonomy_id;
    $catName = $categories[0]->cat_name; ?> 
    <!-- videos cat check starts here -->
    <?php if ((!(preg_match("/videos/i", strtolower($catName)))) && (!(preg_match("/video/i", strtolower($catName)))) ) { ?>
    <?php
      include_once("category-all.php");    
    
     }elseif((preg_match("/videos/i", strtolower($catName))) || (preg_match("/video/i", strtolower($catName))) ) {
      include_once("category-video.php");
     } 
     ?>