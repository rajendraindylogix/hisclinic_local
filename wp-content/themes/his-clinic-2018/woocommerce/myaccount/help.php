<!-- HELP NEW MESSAGE LAYOUT -->
    <div class="account-top">
            <?php echo wp_kses_post( get_field( 'heading_text', 'option' ) ); ?>
    </div>

    <div class="chat-app">

        <div id="chat-blocks-wrap">
            
            <div class="chat-block chat-block--left chat-block--light">
                <?php 
                    $welcome_chat_message = get_field( 'welcome_chat_message', 'option' );

                    $user     = get_user_by( 'id', get_current_user_id() );
                    $username = $user->user_firstname;

                    $dynamic_tags = array(
                        '{customer_name}' => $username,
                    );
                    
                    $welcome_chat_message_text = str_replace( array_keys( $dynamic_tags ), $dynamic_tags, $welcome_chat_message );
                    
                    echo wp_kses_post( $welcome_chat_message_text );
                ?>
                <img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/admin-chat.png" alt="">
            </div>

            <?php 
                // Get Chats.
                $hc_user_chats = get_user_meta( $user->ID, 'hc_dr_support_messages', true );

                if ( ! empty( $hc_user_chats ) && is_array( $hc_user_chats ) ) :

                    foreach ( $hc_user_chats as $key => $chat_block ) {
                        
                        ?>
                            <?php if ( $chat_block['composer'] === 'user' ) : ?>
                                <div class="right-wrap">
                            <?php endif; ?>
                                <div class="chat-block chat-block--<?php echo $chat_block['composer'] === 'user' ? 'right' : 'left'; ?> chat-block--<?php echo $chat_block['composer'] === 'user' ? 'dark' : 'light'; ?>">
                                    <p><?php echo wp_kses_post( $chat_block['dr_support_chat'] ); ?></p>
                                    <img src="<?php echo 'user' === $chat_block['composer'] ? get_user_avatar_url(get_user_avatar(get_current_user_id())) : get_stylesheet_directory_uri() . '/assets/img/admin-chat.png'; ?>" alt="avatar">
                                </div>
                            <?php if ( $chat_block['composer'] === 'user' ) : ?>
                                </div>
                            <?php endif; ?>
                        <?php
                    }
                endif;
            
            ?>

            <script type="text/html" id="tmpl-hc-chat-block">
                <div class="right-wrap">
                    <div class="chat-block chat-block--right chat-block--dark">
                        <p>{{data.data.data.message}}</p>
                        <img src="{{data.data.data.avatar}}" alt="avatar">
                    </div>
                </div>
            </script>

        </div>

        <!-- <div class="right-wrap">
            <div class="chat-block chat-block--right chat-block--dark">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua?</p>
                <img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/user-chat.png" alt="">
            </div>
        </div>

        <div class="chat-block chat-block--left chat-block--light">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et.</p>
                <img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/user-chat.png" alt="">
        </div>

        <div class="right-wrap">
            <div class="chat-block chat-block--right chat-block--dark">
                <p> Hi John!</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua?</p>
                <img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/admin-chat.png" alt="">
            </div>
        </div> -->

        <div class="accordion-bottom">
            <form id="hc-send-chat-message">
                <div class="label-textarea">
                    <textarea required name="dr_support_chat" id="msg-reply" placeholder="Type your message"></textarea>
                    <input type="hidden" name="action" value="hc_dr_support_send_mesage">
                    <input type="hidden" name="composer" value="user">
                    <input type="hidden" name="user_id" value="<?php echo esc_attr( get_current_user_id() ); ?>">
                </div>
                <button id="hc-send-chat-submit" class="btn btn-filled" type="submit"><?php _e( 'Send Message', 'woocommerce' ); ?></button>
            </form>
        </div>
    </div>
