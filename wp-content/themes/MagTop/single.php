<style>
#content {
    overflow: hidden !important;
    float: left !important;
    width: 630px !important;
    margin-left: 0px !important;
}
.related_post_title_custom{
 font-size:24px !important;
 color:#9C0356;
 font-weight:bold;
 margin-top:10px;
 margin-bottom:8px;
}
.wp_rp_thumbnail_custom{

    background-color: #FFFFFF;
    border: 1px solid #BDBDBD;
    padding: 4px;

}

ul.related_post li a.wp_rp_title_custom {
    margin-top: 3px !important;
    color: #9C0356 !important;
    font-weight:bold !important;
}
/*
#respond input{
width:50% !important;
}
#respond textarea{
width:50% !important;
clear:both !important;
}
#respond{
widht:400px !important;
}

#respond label {
font-size: 14px !important;
font-weight: bold !important;
padding:4px !important;
margin:4px !important;
}
*/

#reply-title{
color:#9C0356;

}

#respond { 
background: #ececec;
padding:5px 5px 0 5px;

}

/* Highlight active form field */

#respond input[type=text], textarea {
  -webkit-transition: all 0.30s ease-in-out;
  -moz-transition: all 0.30s ease-in-out;
  -ms-transition: all 0.30s ease-in-out;
  -o-transition: all 0.30s ease-in-out;
  outline: none;
  padding: 3px 0px 3px 3px;
  margin: 5px 1px 3px 0px;
  border: 1px solid #DDDDDD;
}
 
 
#respond input[type=text]:focus, textarea:focus {
  box-shadow: 0 0 5px rgba(81, 203, 238, 1);
  margin: 5px 1px 3px 0px;
  border: 1px solid rgba(81, 203, 238, 1);
}

#author { 
font-family: Lobster, "Lobster 1.4", "Droid Sans", Arial;
font-size: 16px;
color:#1d1d1d; 
letter-spacing:.1em;
} 

#url  { 
color: #21759b;
font-family: "Luicida Console", "Courier New", "Courier", monospace; 
} 

#submit {
font-family: Arial;
color: #ffffff;
font-size: 20px;
padding: 10px;
width:200px;
text-decoration: none;
box-shadow: 0px 1px 3px #666666;
-webkit-box-shadow: 0px 1px 3px #666666;
-moz-box-shadow: 0px 1px 3px #666666;
text-shadow: 1px 1px 3px #666666;
background: -webkit-gradient(linear, 0 0, 0 100%, from(#9C0356), to(#9C02FE));
background: -moz-linear-gradient(top, #006ad4, #003366);
} 

#submit:hover {
  background: -webkit-gradient(linear, 0 0, 0 100%, from(#9C02FE), to(#9C0356));
  background: -moz-linear-gradient(top, #003366, #006ad4)
}
#respond input[type=submit]{
width:200px !important;
}
#respont p input[type=submit]{
dispaly:none !important;
}
.title{
font-size:30px !important;
color:#9C1B7D !important;
}


</style>
<?php global $theme; get_header(); ?>

    <div id="main">
    
        <?php $theme->hook('main_before'); ?>
    
        <div id="content">
            
            <?php $theme->hook('content_before'); ?>
            
            <?php 
                if (have_posts()) : while (have_posts()) : the_post();
                    /**
                     * Find the post formatting for the single post (full post view) in the post-single.php file
                     */
                    get_template_part('post', 'single');
                endwhile;
                
                else :
                    get_template_part('post', 'noresults');
                endif; 
            ?>
            
            <?php $theme->hook('content_after'); ?>
        
        </div><!-- #content -->
    
        <?php //get_sidebars(); ?>
        <?php //get_sidebar('sidebar_secondary'); ?>
        <?php //get_sidebars(); ?>
        <?php include ('sidebar-single.php'); ?>
        <?php $theme->hook('main_after'); ?>
        
    </div><!-- #main -->
    
<?php get_footer(); ?>