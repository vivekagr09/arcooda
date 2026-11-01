<?php


function reh_fetchRefferer()
{
    $login_data = $_SESSION['login_data'];
    
    if($login_data && isset($login_data['refid'])){
        $post['refid'] = $login_data['refid'];
    }

    return isset($post['refid']) ? base64_decode($post['refid']) : 0;
}  

function reh_user_registration_file_upload_config()
{
    return array(
        'upload_path'   => "assets/user_upload/",
        'allowed_types' => 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG|ICO|ico|pdf|docx|doc|ppt|xls|txt',
        'max_size'      => 2048,
    );
}

function reh_fetchReffererStatus($referlevelSettings, $userID) {
    $disabled_for = json_decode( (isset($referlevelSettings['disabled_for']) ? $referlevelSettings['disabled_for'] : '[]'),1);
        
    $refer_status = true;
    
    if((int)$referlevelSettings['status'] == 0 || ((int)$referlevelSettings['status'] == 2 && in_array($userdetails['id'], $disabled_for))){ $refer_status = false; }

    return $refer_status;
}
