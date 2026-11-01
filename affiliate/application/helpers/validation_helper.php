<?php

function vfor_user_registration_api() {
    return array(
        array('field' => 'firstname', 'label' => 'First Name', 'rules' => 'required|trim'), 'errors' => array(
            'required' => 'You must provide a %s.',
            'trim' => 'Please provide valid %s.',
        ),
        array('field' => 'lastname', 'label' => 'Last Name', 'rules' => 'required|trim'), 'errors' => array(
            'required' => 'You must provide a %s.',
            'trim' => 'Please provide valid %s.',
        ),
        array('field' => 'username', 'label' => 'Username', 'rules' => 'required|trim'), 'errors' => array(
            'required' => 'You must provide a %s.',
            'trim' => 'Please provide valid %s.',
        ),
        array('field' => 'email', 'label' => 'Email', 'rules' => 'required|valid_email|xss_clean'), 'errors' => array(
            'required' => 'You must provide a %s.',
            'valid_email' => 'Please provide valid %s.',
        ),
        array('field' => 'terms', 'label' => 'Terms & Condition', 'rules' => 'required'), 'errors' => array(
            'required' => 'You must allow a %s',
        ),
        array('field' => 'password', 'label' => 'Password', 'rules' => 'required|trim'), 'errors' => array(
            'required' => 'You must provide a %s',
            'trim' => 'Please provide valid %s.',
        ),
        array('field' => 'cpassword', 'label' => 'Confirm Password', 'rules' => 'required|trim|matches[password]'), 'errors' => array(
            'required' => 'You must provide a %s',
            'trim' => 'Please provide valid %s.',
            'matches' => '%s should be same as Password.',
        ),
        array('field' => 'device_type', 'label' => 'Device Type', 'rules' => 'required|trim'), 'errors' => array(
            'required' => 'You must provide a %s',
            'trim' => 'Please provide valid %s.',
        ),
        array('field' => 'device_token', 'label' => 'Device Token', 'rules' => 'required|trim'), 'errors' => array(
            'required' => 'You must provide a %s',
            'trim' => 'Please provide valid %s.',
        ),
    );
}    


function vfor_user_registration_custom_fields($json, $post, $_value, $field_name) {
    if($_value['required'] == 'true'){
        if(!isset($post[$field_name]) || $post[$field_name] == ''){
            $json['errors'][$field_name] = $_value['label'] ." is required.!";
        }
    }

    if(!isset($json['errors'][$field_name]) && (int)$_value['maxlength'] > 0){
        if(strlen( $post[$field_name] ) > (int)$_value['maxlength']){
            $json['errors'][$field_name] = $_value['label'] ." Maximum length is ". (int)$_value['maxlength'];
        }
    }

    if(!isset($json['errors'][$field_name]) && (int)$_value['minlength'] > 0){
        if(strlen( $post[$field_name] ) > (int)$_value['minlength']){
            $json['errors'][$field_name] = $_value['label'] ." Minimum length is ". (int)$_value['minlength'];
        }
    }
    return $json;    
}