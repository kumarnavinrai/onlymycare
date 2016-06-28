<style>
/*------------------------------------------------------------
Plugin Name: Youtube Channel Gallery
Plugin URI: http://www.poselab.com/
Version: 1.7.4
Description: Show a youtube video and a gallery of thumbnails for a youtube channel.
------------------------------------------------------------*/	

/*clearfix*/
.ytccf:before,.ytccf:after {content: " "; display: table;}
.ytccf:after {clear: both;}
.ytccf {*zoom: 1;}


/*shortcode wrapper*/
.ytcshort{margin: 10px 0}

/*Player*/
/*---------------------------------------------------*/
iframe.ytcplayer{display: block!important;margin-bottom: 10px!important;padding: 0!important;}

/*Thumbnails, title and description*/
/*---------------------------------------------------*/
ul.ytchagallery.ytccf{margin: 0!important; padding: 0!important;list-style: none!important;}
ul.ytchagallery.ytccf li{float:left;padding:0!important;margin: 0!important;list-style: none!important;}
ul.ytchagallery.ytccf li.ytccell-first{clear: left!important;}

	/*Thumbnails*/
	ul.ytchagallery.ytccf div.ytcthumb-cont{float: left!important;}
	ul.ytchagallery.ytccf li a.ytcthumb{display: inline-block!important;border:1px solid #999;background-position: center center;background-size: 110%;background-repeat: no-repeat;}
	ul.ytchagallery.ytccf li a.ytcthumb .ytcplay{background: url(img/play.png) -9999px -9999px no-repeat;}
	ul.ytchagallery.ytccf li a.ytcthumb:hover{opacity: 0.75;}
	ul.ytchagallery.ytccf li a.ytcthumb:hover .ytcplay{background-position: center center;}

	/*Title and description*/

		ul.ytchagallery.ytccf .ytctitle a{word-wrap: break-word; }

		/*delete*/ul.ytchagallery.ytccf .ytctitle{line-height: 1}

		/*left*/
		ul.ytchagallery.ytc-td-left div.ytcthumb-cont{margin: 0 5px 5px 0}

		/*right*/
		ul.ytchagallery.ytc-td-right div.ytcthumb-cont{float: right; margin: 0 0 5px 5px}
		

		/*top*/
		ul.ytchagallery.ytc-td-top div.ytcthumb-cont{float: none;}

		/*bottom*/
		ul.ytchagallery.ytc-td-bottom div.ytcthumb-cont{float: none; }
		ul.ytchagallery.ytc-td-bottom div.ytctitledesc-cont{margin-bottom: 5px}
		ul.ytchagallery.ytc-td-bottom div.ytctitledesc-cont h5{margin-bottom: 5px}



	/*table*/
	.ytc-row{clear: both;}
	
	.ytc-columns2 li{ width: 50%; width:-webkit-calc(100%/2 + 10px/(2 - 1)); width:calc(100%/2 + 10px/(2 - 1));}
	.ytc-columns2 li.ytccell-last{width:-webkit-calc(100%/2 - 10px); width:calc(100%/2 - 10px);}

	.ytc-columns3 li{ width: 33.33%; width:-webkit-calc(100%/3 + 10px/(3 - 1)); width:calc(100%/3 + 10px/(3 - 1));}
	.ytc-columns3 li.ytccell-last{width:-webkit-calc(100%/3 - 10px); width:calc(100%/3 - 10px);}

	.ytc-columns4 li{ width: 25%; width:-webkit-calc(100%/4 + 10px/(4 - 1)); width:calc(100%/4 + 10px/(4 - 1));}
	.ytc-columns4 li.ytccell-last{width:-webkit-calc(100%/4 - 10px); width:calc(100%/4 - 10px);}

	.ytc-columns5 li{ width: 20%; width:-webkit-calc(100%/5 + 10px/(5 - 1)); width:calc(100%/5 + 10px/(5 - 1));}
	.ytc-columns5 li.ytccell-last{width:-webkit-calc(100%/5 - 10px); width:calc(100%/5 - 10px);}

	.ytc-columns6 li{ width: 20%; width:-webkit-calc(100%/6 + 10px/(6 - 1)); width:calc(100%/6 + 10px/(6 - 1));}
	.ytc-columns6 li.ytccell-last{width:-webkit-calc(100%/6 - 10px); width:calc(100%/6 - 10px);}

	.ytc-columns7 li{ width: 20%; width:-webkit-calc(100%/7 + 10px/(7 - 1)); width:calc(100%/7 + 10px/(7 - 1));}
	.ytc-columns7 li.ytccell-last{width:-webkit-calc(100%/7 - 10px); width:calc(100%/7 - 10px);}

	.ytc-columns8 li{ width: 20%; width:-webkit-calc(100%/8 + 10px/(8 - 1)); width:calc(100%/8 + 10px/(8 - 1));}
	.ytc-columns8 li.ytccell-last{width:-webkit-calc(100%/8 - 10px); width:calc(100%/8 - 10px);}

	.ytc-columns9 li{ width: 20%; width:-webkit-calc(100%/9 + 10px/(9 - 1)); width:calc(100%/9 + 10px/(9 - 1));}
	.ytc-columns9 li.ytccell-last{width:-webkit-calc(100%/9 - 10px); width:calc(100%/9 - 10px);}

	.ytc-columns10 li{ width: 20%; width:-webkit-calc(100%/10 + 10px/(10 - 1)); width:calc(100%/10 + 10px/(10 - 1));}
	.ytc-columns10 li.ytccell-last{width:-webkit-calc(100%/10 - 10px); width:calc(100%/10 - 10px);}

	ul.ytchagallery.ytccf li .ytcliinner{padding: 0 10px 10px 0;overflow: hidden;}
	ul.ytchagallery.ytccf li.ytccell-last .ytcliinner{padding-right: 0;}
	

/*link to YouTube*/
/*---------------------------------------------------*/
.ytcmore{display: block}
</style>
<style>
/*clearfix*/
.ytchgtabs .clearfix:before, .ytchgtabs .clearfix:after { content: ""; display: table; }
.ytchgtabs .clearfix:after { clear: both; }
.ytchgtabs .clearfix { zoom: 1; }


/*tabs ul*/
.ytchgtabs ul{
	margin: 0;
	padding: 0;
}

/*tabs li*/
.ytchgtabs li { 
	display: inline-block; margin: 1px .2em 0 0; 
	padding: 0;
	list-style: none; white-space: nowrap; 
	position: relative;
	background: #F3F3F3;
	border: 1px solid #DFDFDF;
	border-bottom: none;
	border-radius: 5px 5px 0 0;
}
.ytchgtabs li.active{
	margin-bottom: -1px;
	padding-bottom: 1px;
	background: #FAFAFA;
}


/*tabs a*/
.ytchgtabs-tabs a{
	padding: 5px;
	display: inline-block;
	text-decoration: none;
}

/*content*/
.ytchgtabs div {
	border: 1px solid #DFDFDF;
	padding: 15px 12px;
	background: #FAFAFA;
}

/*title and description*/
.ytchgtabs div.ytchg-title-and-description{border: none; background: none;}


.ytchgtabs fieldset.ytchg-fieldborder{border: 1px solid #DFDFDF;border-radius: 5px;}

.ytchagallery{display:none!important}
</style>


<?php
	if(have_posts()) :
		$category = get_the_category();
		if ($category[0]->category_count == 1) :
			while (have_posts()) : the_post();
                        ?>
                        <script>
                        window.location.href = '<?php echo get_permalink($post->ID); ?>';
                        </script>
                         <?php
                        //wp_redirect(get_permalink($post->ID));
			endwhile;
		else :
			include(TEMPLATEPATH . '/index.php');
		endif;
	endif;
?>