<?php
/*
  Plugin Name: Ajax Theme
  Description: This plugin provides some tools to develop a wordpress ajax theme.
  Author: Fractlia - Applications Lab
  Version: 0.1.3
  Author URI: http://fractalia.pe/

 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This plugin is according to the google's document A proposal for
 * making AJAX crawlable (http://googlewebmastercentral.blogspot.com/2009/10/proposal-for-making-ajax-crawlable.html)
 */

class ajax_theme {

    public $is_enabled = false;
    public $anchor;

    public function __construct() {        
        if (!is_admin()) {
            wp_register_script('jquery-history', WP_PLUGIN_URL . '/ajax-theme/js/jquery.history.js', array('jquery'));
            wp_enqueue_script('jquery-history');
        }
        add_action('wp_head', array($this, 'ajax_scripts'));
    }

    public function is_ajax() {
        if (isset($_REQUEST['ajax_request']) && strpos($_SERVER['HTTP_REFERER'], /*get_bloginfo('wpurl')*/$_SERVER['HTTP_HOST']) != false){
            if($_REQUEST['ajax_request'] == '1'){
                return true;
            } else if($_REQUEST['ajax_request'] == '0'){
                return false;
            }
        }
        return (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (!strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? false : true;
    }

    public function ajax_scripts() {
?>
        <!-- Ajax theme 0.1.3 by Fractalia - Applications Lab -->
        <script type="text/javascript">
            function ajax_anchor(){                
                return window.location.hash.substring(<?php echo strlen($this->anchor); ?>);
            }
            
            function ajax_has_anchor(){
                return (window.location.hash == '' || window.location.hash == '!' || window.location.hash == '!/') ? false : true;
            }

            function ajax_init(f){
                jQuery(document).ready(function(){
                    jQuery.history.init(function(hash){
                        <?php if(strlen($this->anchor) > 1): ?>
                        f(hash.substr(1));
                        <?php else: ?>
                        f(hash);
                        <?php endif; ?>
                    });
                });
            }
        </script>
        <!-- / Ajax theme 0.13 by Fractalia - Applications Lab -->
<?php
    }

    function ajax_header($name = null, $callback = null) {
        if ($this->is_enabled) {
            if (!$this->is_ajax()) {
                $this->ajax_redirection();
                get_header($name);
            }
            if (isset($callback)) {
                call_user_func($callback, $name);
            }
        } else {
            get_header($name);
        }
    }

    function ajax_footer($name = null, $callback = null) {
        if ($this->is_enabled) {
            if (!$this->is_ajax()) {
                get_footer($name);
            }
            if (isset($callback)) {
                call_user_func($callback, $name);
            }
        } else {
            get_footer($name);
        }
    }

    function is_post() {
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
    }

    function ajax_redirection() {
        if ($this->is_robots() === false && $this->is_ajax() === false) {
            global $q_config;
            if (isset($_SERVER['REDIRECT_URL']) || ($_SERVER['QUERY_STRING'] != '')) {
                $redirect_url = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '';
                if ($redirect_url != '' && $redirect_url[strlen($redirect_url) - 1] != '/') {
                    header('location: ' . $redirect_url . '/');
                    die();
                } else {
                    $complete_url = get_option('siteurl');
                    if (isset($q_config)) {
                        $lang = $q_config['language'];
                        if (strpos($redirect_url, '/' . $lang . '/') !== false) {
                            $complete_url .= '/' . $lang;
                        }
                    }
                    $request_uri = substr($_SERVER['REQUEST_URI'], strlen(str_replace('index.php', '', $_SERVER['PHP_SELF'])));
                    if ($request_uri != '') {
                        header('location: ' . $complete_url . '/' . $this->anchor . '/' . $request_uri);
                        die();
                    }
                }
            }
        }
    }

    function is_robots() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            return true;
        } else {
            $crawlers = array('aspseek', 'abachobot', 'accoona', 'acoirobot', 'adsbot', 'alexa', 'alta vista', 'altavista', 'ask jeeves', 'baidu', 'crawler', 'croccrawler', 'dumbot', 'estyle', 'exabot', 'facebook', 'fast-enterprise', 'fast-webcrawler', 'francis', 'geonabot', 'gigabot', 'google', 'heise', 'heritrix', 'ibm', 'iccrawler', 'idbot', 'ichiro', 'lycos', 'msn', 'msrbot', 'majestic-12', 'metager', 'ng-search', 'nutch', 'omniexplorer', 'psbot', 'rambler', 'seosearch', 'scooter', 'scrubby', 'seekport', 'sensis', 'seoma', 'snappy', 'steeler', 'synoo', 'telekom', 'turnitinbot', 'tagxedo', 'voyager', 'wisenut', 'yacy', 'yahoo');
            foreach ($crawlers as $c) {
                if (stristr($_SERVER['HTTP_USER_AGENT'], $c)) {
                    return true;
                }
            }
            return false;
        }
    }

}

global $ajax_theme;
$ajax_theme = new ajax_theme();

function enable_ajax_theme($anchor = '#!') {
    global $ajax_theme;
    $ajax_theme->anchor = $anchor;
    $ajax_theme->is_enabled = true;
}

function is_ajax(){
    global $ajax_theme;
    return $ajax_theme->is_ajax();
}

function ajax_header($name = null) {
    global $ajax_theme;
    $ajax_theme->ajax_header($name);
}

function ajax_footer($name = null) {
    global $ajax_theme;
    $ajax_theme->ajax_footer($name);
}

function ajax_is_robots() {
    global $ajax_theme;
    return $ajax_theme->is_robots();
}

function ajax_start_hook() {
    global $ajax_theme;
    if ($ajax_theme->is_ajax()) {
        echo '<div id="ajax-wrap">';
    }
}

function ajax_end_hook() {
    global $ajax_theme;
    if ($ajax_theme->is_ajax()) {
        echo '</div>';
    }
}
?>
