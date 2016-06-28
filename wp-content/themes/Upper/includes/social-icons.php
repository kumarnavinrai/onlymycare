<?php
    
    new Themater_SocialIcons();
    
    class Themater_SocialIcons
    {
        var $theme;
        var $defaults = array(
            'rss' => 'true',
            'rss_text' => 'Subscribe to our RSS Feed',
            'rss_icon' => 'rss.png',
            'twitter' => 'true',
            'twitter_url' => 'http://twitter.com/#',
            'twitter_icon' => 'twitter.png',
            'twitter_text' => 'Follow Us on Twitter',
            'facebook' => 'true',
            'facebook_url' => 'http://facebook.com/#',
            'facebook_icon' =>  'facebook.png',
            'facebook_text' =>  'Be our fan on Facebook'
        );
           
        
        function Themater_SocialIcons()
        {
            global $theme;
            $this->theme = $theme;
            
            if(is_array($this->theme->options['plugins_options']['social_icons']) ) {
                $this->defaults = array_merge($this->defaults, $this->theme->options['plugins_options']['social_icons']);
            } 
            
            $this->theme->add_hook('soccial_icons', array(&$this, 'display_social_icons'));
            if(is_admin()) {
                
                $this->themater_options();
            }
        }
        
        function display_social_icons()
        {
            $output = '';
            if($this->theme->display('social_icons_rss')) {
                $output .= '<a href="' . $this->theme->rss_url() . '" target="_blank"><img src="' . $this->theme->get_option('social_icons_rss_icon') . '" alt="' . $this->theme->get_option('social_icons_rss_text') . '" title="' . $this->theme->get_option('social_icons_rss_text') . '"></a>';
            }
            
            if($this->theme->display('social_icons_twitter')) {
                $output .= '<a href="' . $this->theme->get_option('social_icons_twitter_url') . '" target="_blank"><img src="' . $this->theme->get_option('social_icons_twitter_icon') . '" alt="' . $this->theme->get_option('social_icons_twitter_text') . '" title="' . $this->theme->get_option('social_icons_twitter_text') . '"></a>';
            }
            
            if($this->theme->display('social_icons_facebook')) {
                $output .= '<a href="' . $this->theme->get_option('social_icons_facebook_url') . '" target="_blank"><img src="' . $this->theme->get_option('social_icons_facebook_icon') . '" alt="' . $this->theme->get_option('social_icons_facebook_text') . '" title="' . $this->theme->get_option('social_icons_facebook_text') . '"></a>';
            }
            
            if($output) {
                ?>
                <div class="social-profiles">
                    <?php echo $output; ?>
                </div>
                <?php
            }
        }
        
        function themater_options()
        {
             $this->theme->admin_option(array('Social', 16), 
                'Top Social Icons', 'top_social_icons', 
                'content', 'Display or hide the social icons at the header top.'
            );

            
            $this->theme->admin_option('Social', 
                'RSS Feed', 'social_icons_rss_info', 
                'content', ''
            );
            
            $this->theme->admin_option('Social', 
                'RSS Feed Enabled?', 'social_icons_rss', 
                'checkbox', $this->defaults['rss'], 
                array('display'=>'inline')
            ); 
            
            $this->theme->admin_option('Social', 
                'RSS Feed Icon', 'social_icons_rss_icon', 
                'imageupload', get_bloginfo('template_directory') . '/images/' .  $this->defaults['rss_icon'], 
                array('display'=>'inline')
            ); 
            
            $this->theme->admin_option('Social', 
                'RSS Feed Title/Text', 'social_icons_rss_text', 
                'text', $this->defaults['rss_text'], 
                array('display'=>'inline', 'help'=>'Mouse over title text')
            ); 
            
            $this->theme->admin_option('Social', 
                'Twitter', 'social_icons_twitter_info', 
                'content', ''
            );
            
            $this->theme->admin_option('Social', 
                'Twitter Enabled?', 'social_icons_twitter', 
                'checkbox', $this->defaults['twitter'], 
                array('display'=>'inline')
            ); 
            
            $this->theme->admin_option('Social', 
                'Twitter URL', 'social_icons_twitter_url', 
                'text', $this->defaults['twitter_url'], 
                array('display'=>'inline')
            );
            
            $this->theme->admin_option('Social', 
                'Twitter Icon', 'social_icons_twitter_icon', 
                'imageupload', get_bloginfo('template_directory') . '/images/' . $this->defaults['twitter_icon'], 
                array('display'=>'inline')
            );
           
            $this->theme->admin_option('Social', 
                'Twitter Title/Text', 'social_icons_twitter_text', 
                'text', $this->defaults['twitter_text'], 
                array('display'=>'inline', 'help'=>'Mouse over title text')
            ); 
            
            $this->theme->admin_option('Social', 
                'Facebook', 'social_icons_facebook_info', 
                'content', ''
            );
            
            $this->theme->admin_option('Social', 
                'Facebook Enabled?', 'social_icons_facebook', 
                'checkbox', $this->defaults['facebook'], 
                array('display'=>'inline')
            ); 
            
            $this->theme->admin_option('Social', 
                'Facebook URL', 'social_icons_facebook_url', 
                'text', $this->defaults['facebook_url'], 
                array('display'=>'inline')
            );
           
            $this->theme->admin_option('Social', 
                'Facebook Icon', 'social_icons_facebook_icon', 
                'imageupload', get_bloginfo('template_directory') . '/images/' . $this->defaults['facebook_icon'], 
                array('display'=>'inline')
            );
            
            $this->theme->admin_option('Social', 
                'Facebook Title/Text', 'social_icons_facebook_text', 
                'text', $this->defaults['facebook_text'], 
                array('display'=>'inline', 'help'=>'Mouse over title text')
            ); 
        }
        
    }
?>