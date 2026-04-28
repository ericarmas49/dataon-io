</main>

<?php // get_sidebar(); ?>

</div>
    <footer id="footer" role="contentinfo" class="container-fluid gx-0">
        <div class="container">
            <div class="row">
                <div class="footer-logo col-lg-2">
                    <?php echo get_custom_logo(); ?>
                </div>

                <div class="footer-nav col-lg-7">
                    <div class="footer-menu block-1">
                        <?php wp_nav_menu( array('menu' => 'footer-block-1', 'container_aria_label' => '') ); ?>
                    </div>
                    <div class="footer-menu block-2">
                        <?php wp_nav_menu( array('menu' => 'footer-block-2',) ); ?>
                    </div>
                    <div class="footer-menu block-3">
                        <?php wp_nav_menu( array('menu' => 'footer-block-3',) ); ?>
                    </div>
                    <div class="footer-menu block-4">
                        <?php wp_nav_menu( array('menu' => 'footer-block-4',) ); ?>
                    </div>
                </div>
                <div class="footer-contact col-lg-3">
                    <?php dynamic_sidebar( 'social-widget-area' ); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 footer-copy">
                <a href="#testimonials" class="skip-link screen-reader-text" style="visibility: hidden;" >Skip to the content</a>
                    <div id="copyright">
                        &copy; <?php echo esc_html( date_i18n( __( 'Y', 'blankslate' ) ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

<?php wp_footer(); ?>


<!-- Start of LiveChat (www.livechat.com) code -->
<script>
    window.__lc = window.__lc || {};
    window.__lc.license = 9389740;
    ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
</script>
<noscript><a href="https://www.livechat.com/chat-with/9389740/" rel="nofollow">Chat with us</a>, powered by <a href="https://www.livechat.com/?welcome" rel="noopener nofollow" target="_blank">LiveChat</a></noscript>
<!-- End of LiveChat code -->

<script>
    function liveChat() {
        if (window.LiveChatWidget && typeof window.LiveChatWidget.call === 'function') {
            window.LiveChatWidget.call('maximize');
            return false;
        }

        if (window.LiveChatWidget && typeof window.LiveChatWidget.on === 'function') {
            window.LiveChatWidget.on('ready', function () {
                window.LiveChatWidget.call('maximize');
            });
        }

        return false;
    }
</script>



</body>
</html>