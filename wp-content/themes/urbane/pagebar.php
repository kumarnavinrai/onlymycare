<div class="grid_5">
 <?php include (TEMPLATEPATH . '/tabs-content.php'); ?>
  
        <div class="clear"></div><br />
                <div class="box">
                <ul class="sidebar">
                <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Single/Page Sidebar') ) : else : ?>
            
                         
                    <li>
                    <h2><?php _e('Links'); ?></h2>
                        <ul>
                         <?php get_links(2, '<li>', '</li>', '', TRUE, 'url', FALSE); ?>
                         </ul>
                    </li>
                    
                    <li>
                    <h2><?php _e('Meta'); ?></h2>
                        <ul>
                        <?php wp_register(); ?>
                        <li><?php wp_loginout(); ?></li>
                        <?php wp_meta(); ?>
                        </ul>
                    </li>
                    
                <?php endif; ?>
                </ul>
                </div>

  
</div><!--grid_5-->
<script type="text/javascript">
<!--
var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
//-->
</script>
<div class="clear"></div>

<br />