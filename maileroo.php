<?php
/*
Plugin Name: WP Easy SMTP
Description: Streamline WordPress email delivery with universal SMTP compatibility, including Maileroo, SendGrid, Mailgun, and more - all-in-one SMTP plugin for reliable, user-friendly email integration.
Version: 1.0
Author: <a href="https://maileroo.com//" target="_blank">Maileroo</a>
*/


function maileroo_menu()
{
    add_menu_page(
        'WP Easy SMTP', // Page Title
        'WP Easy SMTP', // Menu Title
        'manage_options',
        'maileroo-settings',
        'maileroo_page',
        plugins_url('/assets/icon.svg', __FILE__),
    );
}
add_action('admin_menu', 'maileroo_menu');


function maileroo_page()
{

    include_once plugin_dir_path(__FILE__) . 'components/home_page.php';
}

function maileroo_settings_init()
{
    register_setting('maileroo_settings', 'smtp_host', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'smtp.maileroo.com', // Set your default value here
    ]);

    register_setting('maileroo_settings', 'smtp_port', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '587', // Set your default value here
    ]);

    register_setting('maileroo_settings', 'authentication', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'on', // Set your default value here
    ]);

    register_setting('maileroo_settings', 'smtp_username', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '', // Set your default value here
    ]);

    register_setting('maileroo_settings', 'smtp_password', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '', // Set your default value here
    ]);

    register_setting('maileroo_settings', 'encryption', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'starttls', // Set your default value here
    ]);

    register_setting('maileroo_settings', 'from_email', [
        'sanitize_callback' => 'sanitize_email',
        'default'           => '', // Set your default value here
    ]);

    register_setting('maileroo_settings', 'from_name', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '', // Set your default value here
    ]);
}

add_action('admin_init', 'maileroo_settings_init');

// AJAX handler for sending a test email
add_action('wp_ajax_send_test_email', 'send_test_email');
function send_test_email()
{
    $recipient_email = sanitize_email($_POST['recipient_email']);
    $subject = 'Test Email from Maileroo Plugin';
    $message = 'This is a test email sent from the Maileroo Plugin.';
    $headers = 'From: ' . get_option('from_email') . "\r\n";

    add_action('wp_mail_failed', 'capture_wp_mail_errors');

    $success = wp_mail($recipient_email, $subject, $message, $headers);

    remove_action('wp_mail_failed', 'capture_wp_mail_errors');


    if ($success) {
        echo '<div class="success">Test email sent successfully!</div>';
    } else {
        $error_message = get_transient('maileroo_error_message');

        if (!empty($error_message)) {
            echo '<div class="error">Failed to send test email. Check your server settings and try again. Error: ' . esc_html($error_message) . '</div>';
        } else {
            echo '<div class="error">Failed to send test email. Check your server settings and try again.</div>';
        }
    }

    die();
}

function capture_wp_mail_errors($wp_error)
{
    $error_message = $wp_error->get_error_message();
    set_transient('maileroo_error_message', $error_message, 60); // Store error message for 60 seconds
}



function enqueue_admin_styles()
{
    wp_enqueue_style('maileroo_styles', plugins_url('/assets/global_styles.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'enqueue_admin_styles');


// Hook into phpmailer_init action
add_action('phpmailer_init', 'custom_smtp_override');

function custom_smtp_override($phpmailer)
{
    $smtp_host = get_option('smtp_host', 'smtp.maileroo.com');
    $smtp_port = get_option('smtp_port', '587');
    $authentication = get_option('authentication', 'on');
    $smtp_username = get_option('smtp_username', '');
    $smtp_password = get_option('smtp_password', '');
    $encryption = get_option('encryption', 'starttls');
    $from_email = get_option('from_email', '');
    $from_name = get_option('from_name', '');

    // Set custom SMTP settings
    $phpmailer->isSMTP();
    $phpmailer->Host = $smtp_host;
    $phpmailer->Port = $smtp_port;
    $phpmailer->SMTPAuth = $authentication === 'on';
    $phpmailer->Username = $smtp_username;
    $phpmailer->Password = $smtp_password;
    $phpmailer->SMTPSecure = $encryption;
    $phpmailer->From = $from_email;
    $phpmailer->FromName = $from_name;
}

function my_custom_hook_on_form_submit()
{
    do_action('my_custom_form_submit_action');
}

add_action('admin_init', 'my_custom_hook_on_form_submit');

// In your plugin or theme's functions.php file or your custom plugin file
function my_custom_function_on_form_submit()
{
    // var_dump($_POST);
    if (sizeof($_POST)) {
        setcookie('submit-result', 'success', time() + 1, '/');
    }
}

add_action('my_custom_form_submit_action', 'my_custom_function_on_form_submit');
