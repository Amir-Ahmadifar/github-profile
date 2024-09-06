<?php

/**
 * settin page inside wp admin panel
 */
function github_profile_fetcher_create_menu()
{
    add_menu_page(
        'تنظیمات پروفایل گیت',
        'پروفایل Git',
        'manage_options',
        'github-profile-fetcher',
        'github_profile_fetcher_settings_page',  
        'dashicons-admin-generic',      
        72                           
    );
}
add_action('admin_menu', 'github_profile_fetcher_create_menu');

/**
 * seeting page for git api management
 */
function github_profile_fetcher_settings_page()
{
?>
    <div class="wrap">
        <h1>تنظیمات افزونه</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('github_profile_fetcher_options_group');
            do_settings_sections('github-profile-fetcher');
            submit_button();
            ?>
        </form>
        <hr>
        <div>
            <h1>راهنمای استفاده از افزونه</h1>
            <p>در هر بخش از وبسایت که لازم است از این افزونه استفاده کنید کافیست Shortcode زیر را در آن بخش کپی کنید : </p>
            <code>
            [github_profile username="octocat"]
            </code>
            <ul>
                <li> <strong>توجه :</strong> مقدار نام کاربری پیش فرض برای خالی نبودن اطلاعات octocat می باشد که باید نام کاربری خود را به جای آن وارد نمایید</li>
            </ul>
        </div>

    </div>
<?php
}

/**
 * save plugin setting
 */
function github_profile_fetcher_register_settings()
{
    register_setting('github_profile_fetcher_options_group', 'github_api_token');

    add_settings_section(
        'github_profile_fetcher_main_section',
        'Main Settings',
        null,
        'github-profile-fetcher'
    );

    add_settings_field(
        'github_api_token',
        'GitHub API Token',
        'github_profile_fetcher_token_input',
        'github-profile-fetcher',
        'github_profile_fetcher_main_section'
    );
}
add_action('admin_init', 'github_profile_fetcher_register_settings');

/**
 * input for token field
 */
function github_profile_fetcher_token_input()
{
    $token = get_option('github_api_token');
    echo '<input style="width:70%" type="text" id="github_api_token" name="github_api_token" value="' . esc_attr($token) . '" />';
}

/**
 * function for profile request from git
 */
function github_profile_fetch()
{
    if (!isset($_GET['username'])) {
        wp_send_json_error('وارد کردن نام کاربری الزامیست');
    }

    $username = sanitize_text_field($_GET['username']);
    $token = get_option('github_api_token');

    if (empty($token)) {
        wp_send_json_error('توکن Api مربوط به گیت به درستی ثبت نشده است');
    }

    $url = "https://api.github.com/users/" . $username;
    $args = array(
        'headers' => array(
            'Authorization' => 'token ' . $token,
            'User-Agent' => 'WordPress'
        )
    );

    $response = wp_remote_get($url, $args);
    $body = wp_remote_retrieve_body($response);

    if (is_wp_error($response)) {
        wp_send_json_error('Error fetching profile');
    }

    wp_send_json_success(json_decode($body));
}
add_action('wp_ajax_fetch_github_profile', 'github_profile_fetch');
add_action('wp_ajax_nopriv_fetch_github_profile', 'github_profile_fetch');

/**
 * shortcode for use script on wp pages or in elementor
 */
function github_profile_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'username' => 'octocat',
    ), $atts, 'github_profile');

    ob_start();
?>
    <div id="github-profile" class="github-card">
        <h2 class="github-title">GitHub Profile</h2>
        <div class="github-content">
            <p>بارگذاری پروفایل . . .</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchGithubProfile('<?php echo esc_js($atts['username']); ?>');
        });
    </script>
<?php
    return ob_get_clean();
}
