<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Get all subscription emails
$subscription_emails = (get_option('_cjwpbldr_data_email_subscribers') != '') ? get_option('_cjwpbldr_data_email_subscribers') : '';

$current_url = $this->helpers->callbackURL('core-custom-data', null, 'cjwpbldr');
$remove_all_url = $current_url . '&remove-all-data=1';

// Check if global $_POST request is made
if (isset($_POST)) {

    // Remove individual email
    if (isset($_POST['email_to_remove'])) {
        $remove_email = $_POST['email_to_remove'];

        if (count($subscription_emails) > 1) {
            if (($key = array_search($remove_email, $subscription_emails)) !== false) {
                unset($subscription_emails[$key]);
            }
            $updated_option = array_diff_assoc($subscription_emails, $remove_email);
            update_option('_cjwpbldr_data_email_subscribers', $subscription_emails);
        } else {
            delete_option('_cjwpbldr_data_email_subscribers');
        }

        wp_redirect($current_url);
        exit;
    }

    // Download emails in CSV format
    if (isset($_POST['download_subscription_emails'])) {
        $headings_array = array(
            __('Email Addresses', 'wp-builder-locale')
        );
        $download_emails = [];

        foreach ($subscription_emails as $key => $value) {
            $download_emails[] = [$value];
        }
        $file_name = 'cjwpbldr-subscription-emails-' . date('Y-m-d');
        $this->helpers->createCSV($file_name, $headings_array, $download_emails);

        wp_redirect($current_url);
        exit;
    }
}

// Remove all emails
if (isset($_GET['remove-all-data']) && $_GET['remove-all-data'] == 1) {
    delete_option('_cjwpbldr_data_email_subscribers');

    wp_redirect($current_url);
    exit;
}

$options = [
    [
        'id' => 'env-heading',
        'type' => 'sub-heading',
        'default' => __('Subscription Emails Data', 'wp-builder-locale'),
        'options' => '', // array in case of dropdown, checkbox and radio buttons
    ],
];

// Display page heading
echo '<div class="cj-mb-30">';
echo $this->helpers->renderAdminForm($options);
echo '</div>';

if ($subscription_emails != '') {

    // Loop through all the emails and setup a <form> 
    // to remove them individually
    foreach ($subscription_emails as $key => $email) {
        $email_options[] = [
            'id' => 'remove_email',
            'type' => 'submit',
            'label' => $email,
            'default' => __('Remove', 'wp-builder-locale'),
            'params' => [
                'class' => 'cj-is-danger'
            ],
        ];
        $email_options[] = [
            'id' => 'email_to_remove',
            'type' => 'hidden',
            'default' => $email
        ];
        echo '<form action="" method="post">';
        echo $this->helpers->renderAdminForm($email_options);
        echo '</form>';

        $email_options = [];
    }

    // Setup up a <form> button to download all
    // emails as CSV
    $remove_all_email_options[] = [
        'id' => 'download_subscription_emails',
        'type' => 'submit',
        'default' => __('Download Data', 'wp-builder-locale'),
        'label' => sprintf(__('<a class="cj-button cj-is-danger" href="%s" title="">Clear All Data</a>', 'wp-builder-locale'), $remove_all_url),
    ];

    echo '<form action="" method="post">';
    echo $this->helpers->renderAdminForm($remove_all_email_options);
    echo '</form>';
} else {

    // Display a message if no emails are found
    $no_email_options[] = [
        'id' => 'no_email_heading',
        'type' => 'sub-heading',
        'label' => 'yes',
        'default' => __('No Records Found', 'wp-builder-locale'),
    ];
    echo '<form action="" method="post">';
    echo $this->helpers->renderAdminForm($no_email_options);
    echo '</form>';
}
