<?php
/*
Plugin Name: Last3een Github Profile
Plugin URI: https://last3een.ir/افزونه-پروفایل-github/ 
Description: Fetch and display a Github user's profile securely using GitHub API.
Requires at least: 5.2
Version: 1.0.1
Author: Amir Mohammad Ahmadifar
Author URI: https://last3een.ir
Requires PHP: 7.2
License: GPL2
*/

// avoid accessing directly to the files
defined('ABSPATH') or die('No script kiddies please!');

// resources include
require_once plugin_dir_path(__FILE__) . 'includes/github-profile-functions.php';

// بارگذاری اسکریپت‌ها و استایل‌ها
function github_profile_fetcher_enqueue_scripts()
{
    wp_enqueue_script('github-profile-fetcher', plugins_url('assets/js/github-profile.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_style('github-profile-fetcher-style', plugins_url('assets/css/style.css', __FILE__));
    wp_localize_script('github-profile-fetcher', 'githubProfileData', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'github_profile_fetcher_enqueue_scripts');

//use shortcode
add_shortcode('github_profile', 'github_profile_shortcode');
