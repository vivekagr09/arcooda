<?php include_once(APPPATH.'helpers/utility_helper.php');

function version_images() {
    $CI =& get_instance();
    $output = $CI->output->get_output();

    // Replace image URLs to include the version
    $version = av(); // Ensure your av() function is available here
    $output = preg_replace('/(src=["\']((?!http:\/\/|https:\/\/|\/\/).+?)(\.jpg|\.png|\.gif|\.svg)["\'])/i', 'src="$2$3?v=' . $version . '"', $output);

    echo $output;
}
