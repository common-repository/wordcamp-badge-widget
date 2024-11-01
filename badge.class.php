<?php
    /**
    *   Plugin name: WordCamp Badge Widget
    *   Plugin URI: http://travisballard.com/wordpress/wordcamp-badge-widget/
    *   Description: Dynamically add 'im attending', 'im sponsoring', 'im speaking at', 'wish i could attend' captions to badges and switch to past tense phrases after the event is over.
    *   Version: 1.0
    *   Author: Travis Ballard
    *   Author URI: http://travisballard.com
    */

    /**
    *   TO DO:
    *
    *   [ ] add count down in days until event? see if requests come in for this. maybe an option to enable, disable.
    *       would like a sprite for numbers though instead of just text. if no sprite found will default to plain text if enabled.
    *
    *   [ ] function to add badge to site in event of no defined sidebars.
    *
    *   [ ] shortcode? add badge to post?
    */

    class WordCampBadge extends WP_Widget
    {
        var $event_title = 'WordCamp Miami',            # title of event, ie: WordCamp Miami
            $event_date = 'February 20 2010',           # date of event
            $background = 'wcmiami.jpg',                # badge background image. jpg only but adding png support wouldnt be hard. must be in plugin folder.
            $font_size = 12,                            # font size for caption text
            $font = 'verdana.ttf',                      # font to use. must be TrueType and must be in plugin folder.
            $text_color = '#ffffff',                    # hex color code for caption text
            $y_offset = 25,                             # offset for text from top of image, in pixels.
            $badge_square_size = 250,                   # default size of badge in pixels. square image so this is height & width
            $badge_folder = 'badges',                   # folder to store generated badges in
            $event_url = 'http://www.wordcampmia.com';  # url to event home page

        # leave these as is.
        var $event_past = false,
            $cwd = null,
            $image = null;

        /**
        * construct
        */
        function WordCampBadge()
        {
            parent::WP_Widget( 0, $name = sprintf( '%s Badge', $this->event_title ) );
            $this->cwd = dirname( __FILE__ );
            $this->background = sprintf( '%s/%s', $this->cwd, $this->background );
            $this->font = sprintf( '%s/%s', $this->cwd, $this->font );
            $this->badge_folder = sprintf( '%s/%s', $this->cwd, $this->badge_folder );
        }

        /**
        * widget options form
        *
        * @param mixed $i
        */
        function form( $i )
        {
            $size = isset( $i['size'] ) ? esc_attr( $i['size'] ) : 250;
            if( $size > 250 || ! is_numeric( $size ) ) $size = 250;
            $type = isset( $i['type'] ) && !empty( $i['type'] ) ? $i['type'] : 'attending';

            ?>
            <h3>Which badge would you like to display?</h3>
            <p>
                <input class="widefat" id="<?php echo $this->get_field_id('type'); ?>1" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="attending" <?php checked( 'attending', $type, 1 );?>/>
                <label for="<?php echo $this->get_field_id('type'); ?>1"><?php _e('Attending'); ?></label>
            </p>
            <p>
                <input class="widefat" id="<?php echo $this->get_field_id('type'); ?>2" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="sponsoring" <?php checked( 'sponsoring', $type, 1 );?>/>
                <label for="<?php echo $this->get_field_id('type'); ?>2"><?php _e('Sponsoring'); ?></label>
            </p>
            <p>
                <input class="widefat" id="<?php echo $this->get_field_id('type'); ?>3" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="speaking" <?php checked( 'speaking', $type, 1 );?>/>
                <label for="<?php echo $this->get_field_id('type'); ?>3"><?php _e('Speaking'); ?></label>
            </p>
            <p>
                <input class="widefat" id="<?php echo $this->get_field_id('type'); ?>4" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="wishing" <?php checked( 'wishing', $type, 1 );?>/>
                <label for="<?php echo $this->get_field_id('type'); ?>4"><?php _e('Wish I Was Attending'); ?></label>
            </p>

            <h3>Badge Square Size?<br /><small>( must be lower than 250 )</small></h3>
            <p>
                <input class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" type="text" value="<?php echo $size; ?>" size="4" style="width:40px;" />
                <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Pixels'); ?></label>
            </p>
            <?php
        }

        /**
        * update widget
        *
        * @param mixed $n
        * @param mixed $o
        */
        function update( $n, $o )
        {
            return $n;
        }

        /**
        * display widget
        *
        * @param mixed $a
        * @param mixed $i
        */
        function widget( $a, $i )
        {
            extract( $a );

            # check if event is in past
            if( time() > strtotime( $this->event_date ) )
                $this->event_past = true;

            # check if badge folder exists. if not, create it.
            if( ! is_dir( $this->badge_folder ) )
            {
                @mkdir( $this->badge_folder );
                chmod( $this->badge_folder, 0755 );
            }

            # set badge size make sure it's between 75 and 250px squared
            if( $i['size'] < 250 && $i['size'] > 75 )
                $this->badge_square_size = $i['size'];

            switch( $i['type'] )
            {
                # show attending image
                case 'attending' :
                    $this->image = $this->event_past ?
                        sprintf( '%s/attended.jpg', $this->badge_folder ) :
                        sprintf( '%s/attending.jpg', $this->badge_folder );

                    # check event isnt in past
                    if( ! $this->event_past )
                    {   # check if image exists. if not, create it
                        list( $w, $h ) = @getimagesize( $this->image );
                        if( !file_exists( $this->image ) || isset( $w ) && $w != $this->badge_square_size )
                            $this->make_badge( __( 'I\'m Attending' ) );

                        $this->display_badge( $a );
                        break;
                    }
                    else
                    {   # event in in past, show past tense image. create if needed
                        if( !file_exists( $this->image ) )
                            $this->make_badge( __( 'I Attended' ) );

                        $this->display_badge( $a );
                        break;
                    }

                # speaking images
                case 'speaking' :
                    $this->image = $this->event_past ?
                        sprintf( '%s/speaking.jpg', $this->badge_folder ) :
                        sprintf( '%s/spoke.jpg', $this->badge_folder );

                    if( ! $this->event_past )
                    {
                        list( $w, $h ) = @getimagesize( $this->image );
                        if( !file_exists( $this->image ) || isset( $w ) && $w != $this->badge_square_size )
                            $this->make_badge( __( 'I\'m Speaking At' ) );

                        $this->display_badge( $a );
                        break;
                    }
                    else
                    {
                        if( !file_exists( $this->image ) )
                            $this->make_badge( __( 'I Spoke At' ) );

                        $this->display_badge( $a );
                        break;
                    }

                # sponsor images
                case 'sponsoring' :
                    $this->image = $this->event_past ?
                        sprintf( '%s/sponsored.jpg', $this->badge_folder ) :
                        sprintf( '%s/sponsoring.jpg', $this->badge_folder );

                    if( ! $this->event_past )
                    {
                        list( $w, $h ) = @getimagesize( $this->image );
                        if( !file_exists( $this->image ) || isset( $w ) && $w != $this->badge_square_size )
                            $this->make_badge( __( 'I\'m Sponsoring' ) );

                        $this->display_badge( $a );
                        break;
                    }
                    else
                    {
                        if( !file_exists( $this->image ) )
                            $this->make_badge( __( 'I Sponsored' ) );

                        $this->display_badge( $a );
                        break;
                    }

                # wish images
                case 'wishing' :
                    $this->image = $this->event_past ?
                        sprintf( '%s/wished.jpg', $this->badge_folder ) :
                        sprintf( '%s/wish.jpg', $this->badge_folder );

                    if( ! $this->event_past )
                    {
                        list( $w, $h ) = @getimagesize( $this->image );
                        if( !file_exists( $this->image ) || isset( $w ) && $w != $this->badge_square_size )
                            $this->make_badge( __( 'Wish I Was Attending' ) );

                        $this->display_badge( $a );
                        break;
                    }
                    else
                    {
                        if( !file_exists( $this->image ) )
                            $this->make_badge( __( 'Wish I Attended' ) );

                        $this->display_badge( $a );
                        break;
                    }

                default : break;
            }
        }

        /**
        * display badge
        *
        * @param mixed $a
        */
        function display_badge( $a )
        {
            extract( $a );
            printf(
                '%s<a href="%s"><img src="%s" alt="Attending WordCamp Badge" /></a>%s',
                $before_widget,
                $this->event_url,
                plugins_url( sprintf( '%s/%s', basename( $this->badge_folder ), basename( $this->image ) ), __FILE__ ),
                $after_widget
            );
        }

        /**
        * make badge
        *
        * @param mixed $text
        */
        function make_badge( $text )
        {
            $image = imagecreatefromjpeg( $this->background );

            list( $r, $g, $b ) = $this->hex2rgb( $this->text_color );
            $color = imagecolorallocate( $image, $r, $g, $b );

            imagettftext(
                $image,
                $this->font_size,
                0,
                round( ( imagesx( $image ) / 2 ) - ( ( strlen( $text ) * imagefontwidth( $this->font_size ) ) / 2 ), 1 ),
                $this->y_offset,
                $color,
                $this->font,
                $text
            );

            # resize if needed
            if( $this->badge_square_size != 250 )
            {
                $resampled = imagecreatetruecolor( $this->badge_square_size, $this->badge_square_size );
                imagecopyresampled( $resampled, $image, 0, 0, 0, 0, $this->badge_square_size, $this->badge_square_size, imagesx( $image ), imagesy( $image ) );
                $i = imagejpeg( $resampled, $this->image, 90 );
                imagedestroy( $resampled );
            }
            else
                $i = imagejpeg( $image, $this->image, 90 );

            imagedestroy( $image );
            return $this->image;
        }

        /**
        * convert hex value to r,g,b array
        *
        * @param mixed $h
        * @return array
        */
        function hex2rgb( $h )
        {
            $i = hexdec( ltrim( $h, '#' ) );
            return array(
                0xFF & $i >> 0x10,        # r
                0xFF & $i >> 0x8,         # g
                0xFF & $i                 # b
            );
        }
    }

    add_action( 'widgets_init', create_function( '', 'return register_widget("WordCampBadge");' ) );
?>