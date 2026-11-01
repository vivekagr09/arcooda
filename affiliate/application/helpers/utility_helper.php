<?php
    
    if (!function_exists('av')) {
        function av() {
            return '10.0.0.3';
        }
    }

    function dd($debug, $die = true) {
        echo "<pre>";
        try {
            print_r($debug);
        } catch (Exception $e) {
            echo $debug;
        }
        if($die) die();
    }


    function generateRandomAlpahaNemericCode()
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomecode=substr(str_shuffle($permitted_chars), 0, 10);
        return $randomecode;
    }

    function print_message($t) { 
        if($t->session->flashdata('success')) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $t->session->flashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php $t->session->unset_tempdata('success'); 
        }

        if($t->session->flashdata('error')) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $t->session->flashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php $t->session->unset_tempdata('error'); 
        }
    }

    function file_upload_max_size() {
        $upload_max = ini_get('upload_max_filesize');
        return $upload_max;
    }

    function duplicate_entry($table, $field, $id, $primaryKey, $overwrite = []){
        $CI =& get_instance();
        $CI->db->where($field, $id); 
        $query = $CI->db->get($table);

        foreach ($query->result() as $row){   
            foreach($row as $key=>$val){        
                if($key != $primaryKey){
                    if(isset($overwrite[$key])){
                        $CI->db->set($key, $overwrite[$key]);
                    } else{
                        $CI->db->set($key, $val);
                    }
                }
            }
        }

        $CI->db->insert($table); 
        return $CI->db->insert_id();
    }

    function parseIntegrationType($type) {
        switch ($type) {
            case 'single_action':
            return __('admin.single_action_integration');
            break;
            case 'action':
            return __('admin.multi_action_integration');
            break;
            case 'general_click':
            return __('admin.click_integration');
            break;
            break;
            case 'program':
            return __('admin.sale_integration');
            break;
            default:
            return __('admin.unknown');
            break;
        }
    }

function withdrwal_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-secondary text-light px-3 py-2 fs-6">'.__('admin.received').'</span>'; break;
        case 13: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'.__('admin.pending').'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'.__('admin.paid').'</span>'; break;
        case 2: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.total_not_match').'</span>'; break;
        case 3: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.denied').'</span>'; break;
        case 4: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.expired').'</span>'; break;
        case 5: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.failed').'</span>'; break;
        case 6: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.pending').'</span>'; break;
        case 7: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.processed').'</span>'; break;
        case 8: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.refunded').'</span>'; break;
        case 9: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.reversed').'</span>'; break;
        case 10: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.voided').'</span>'; break;
        case 11: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.cancel_reversal').'</span>'; break;
        case 12: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.waiting_for_payment').'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'.__('admin.unknown').'</span>'; break;
    }

    return $label;
}


function membership_withdrwal_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'.__('admin.pending').'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'.__('admin.active').'</span>'; break;
        case 2: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.total_not_match').'</span>'; break;
        case 3: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.denied').'</span>'; break;
        case 4: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.expired').'</span>'; break;
        case 5: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.failed').'</span>'; break;
        case 7: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.processed').'</span>'; break;
        case 8: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.refunded').'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'.__('admin.unknown').'</span>'; break;
    }

    return $label;
}


function store_withdrwal_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-secondary text-light px-3 py-2 fs-6">'.__('admin.waiting_for_payment').'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'.__('admin.complete').'</span>'; break;
        case 2: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.total_not_match').'</span>'; break;
        case 3: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.denied').'</span>'; break;
        case 4: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.expired').'</span>'; break;
        case 5: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.failed').'</span>'; break;
        case 6: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.pending').'</span>'; break;
        case 7: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.processed').'</span>'; break;
        case 8: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.refunded').'</span>'; break;
        case 9: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.reversed').'</span>'; break;
        case 10: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.voided').'</span>'; break;
        case 11: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.cancel_reversal').'</span>'; break;
        case 12: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'.__('admin.waiting_for_payment').'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'.__('admin.unknown').'</span>'; break;
    }

    return $label;
}


function ads_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'. __('admin.draft') .'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'. __('admin.public') .'</span>'; break;
        case 2: $label = '<span class="badge bg-info text-light px-3 py-2 fs-6">'. __('admin.in_review') .'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'.__('admin.unknown').'</span>'; break;
    }
    return $label;
}


function ads_security_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-info text-light px-3 py-2 fs-6">'. __('admin.pending_integration') .'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'. __('admin.approved') .'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'.__('admin.unknown').'</span>'; break;
    }
    return $label;
}


        function ads_running_status($status){
            $label = '';

            switch ((int)$status) {
                case 0: $label = 'warning'; break;
                case 1: $label = 'success'; break;
                default: $label = 'warning'; break;
            }

            return $label;
        }
        function ads_google_status($status){
            $label = '';

            switch ((int)$status) {
                case 1: $label =  __('admin.side_bar_top'); break;
                case 2: $label = __('admin.side_bar_bottom'); break;
                case 3: $label = __('admin.footer'); break;
                case 4: $label = __('admin.right_side'); break;
                case 5: $label = __('admin.center_page'); break;
                default: $label = __('admin.right_side'); break;
            }

            return $label;
        }

function program_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'. __('admin.in_review') .'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'. __('admin.approved') .'</span>'; break;
        case 2: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'. __('admin.denied') .'</span>'; break;
        case 3: $label = '<span class="badge bg-yellow text-dark px-3 py-2 fs-6">'. __('admin.ask_to_edit') .'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'. __('admin.unknow') .'</span>'; break;
    }
    return $label;
}


function product_status_on_store($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'. __('admin.not_displayed') .'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'. __('admin.displayed') .'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'. __('admin.unknow') .'</span>'; break;
    }
    return $label;
}


function product_status_on_store_admin($status, $product_by) {
    $label = '';
    $statusClass = (int)$status === 0 ? 'bg-danger' : 'bg-success';
    $statusText = (int)$status === 0 ? __('admin.draft') : __('admin.displayed');
    $label = '<span class="badge '. $statusClass .' text-light px-3 py-2 fs-6">'. $statusText .'</span>';
    return $label;
}


function product_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'. __('admin.in_review') .'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'. __('admin.approved') .'</span>'; break;
        case 2: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'. __('admin.denied') .'</span>'; break;
        case 3: $label = '<span class="badge bg-yellow text-dark px-3 py-2 fs-6">'. __('admin.ask_to_edit') .'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'. __('admin.unknow') .'</span>'; break;
    }
    return $label;
}


function form_status($status) {
    $label = '';
    switch ((int)$status) {
        case 0: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">'. __('admin.in_review') .'</span>'; break;
        case 1: $label = '<span class="badge bg-success text-light px-3 py-2 fs-6">'. __('admin.approved') .'</span>'; break;
        case 2: $label = '<span class="badge bg-danger text-light px-3 py-2 fs-6">'. __('admin.denied') .'</span>'; break;
        case 3: $label = '<span class="badge bg-yellow text-dark px-3 py-2 fs-6">'. __('admin.ask_to_edit') .'</span>'; break;
        default: $label = '<span class="badge bg-warning text-light px-3 py-2 fs-6">Unknow</span>'; break;
    }
    return $label;
}


        function cycle_details($total_recurring,$next_transaction,$endtime = false,$total_recurring_amount = false ){
            $str =  'Runs '. (int)$total_recurring;

            if($next_transaction != ''){
                $str .= " | Next At : ". date("d-m-Y H:i",strtotime($next_transaction));
            }
            if($endtime != ''){
                $str .= " | Endtime : ". date("d-m-Y H:i",strtotime($endtime));
            }
            if($total_recurring_amount){
                $str .= " | Total Amount : ". c_format($total_recurring_amount);
            }

            return $str;
        }

        function dateFormat($date , $f = "d-m-Y H:i:s"){
            return date($f,strtotime($date));
        }
        function timetosting($minutes){
            $day = floor ($minutes / 1440);
            $hour = floor (($minutes - $day * 1440) / 60);
            $minute = $minutes - ($day * 1440) - ($hour * 60);

            $str = '';
            if($day > 0) $str .= "{$day} day ";
            if($hour > 0) $str .= "{$hour} hour ";
            if($minute > 0) $str .= "{$minute} minute ";

            return $str;
        }
        function asset_url() {
            echo base_url() . 'assets/';
        }
        function pr($data) {
            echo '<pre>'; print_r($data); echo '</pre>';
        }
        function flashMsg($flash) { 
            if (isset($flash['error'])) {
                echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>' .$flash['error']. '</div>';
            }
            if (isset($flash['success'])) {
                echo '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>' .$flash['success'] . '</div>';
            }
        }
        function e3DpOIO10($check_cache = true){
            $cache_file = str_replace("install/../", '', APL_CACHE);
            $res = '';

            if($check_cache){
                if( file_exists($cache_file) ){
                    $res = json_decode(file_get_contents($cache_file),1);
                }
            } else {
                $res = getLicense(getBaseUrl(false));
                @unlink($cache_file);

                $fp = fopen($cache_file, 'w');
                fwrite($fp, json_encode($res));
                fclose($fp);
            }

            if(isset($res['success']['is_lifetime']) && $res['success']['is_lifetime'] == false){
                if ($res['success']['remianing_time'] <= 0) {
                   $base_url = base_url();
                   @unlink($cache_file);
                   require 'install/license_expire.php';
                   die();
               }
           }

           if($res && isset($res['success'])){

           }
           else if($res && isset($res['error'])){ 
            @unlink($cache_file);
            header('location:'. base_url('install/index.php?error='. $res['error']));die;
        }
    }

    function DOCROOT($file, $from) {
        if ($from == 'full') {
            return @$_SERVER["DOCUMENT_ROOT"] . '/cyclops/assets/uploads/' . $file;
        } elseif ($from == 'thumb') {
            return @$_SERVER["DOCUMENT_ROOT"] . '/cyclops/assets/uploads/thumb/' . $file;
        }
    }

    function set_default_currency(){
        ___construct(1);
    }

    function is_rtl()
    {
        $CI =& get_instance();
        $lang = $_SESSION['userLang'];
        $lang = $CI->db->query("SELECT * FROM language WHERE status=1 AND id=". (int)$lang)->row_array();

        if ($lang['is_rtl']) {
            return true;
        } 

        return false;
    }

    global $language; 
    function __($key){
        global $language;
        $userLang = $_SESSION['userLang'];
        if($userLang == ''){
            $CI =& get_instance();
            $default_language = $CI->db->query("SELECT * FROM language WHERE status=1 AND is_default=1")->row_array();
            if($default_language){
                $userLang = $_SESSION['userLang'] = $default_language['id'];
            }
        }
        if(!$language){
            fillLang($userLang);
        }

        return isset($language[$key]) ? $language[$key] : $key;
    }
    function fillLang($id){
        global $language;
        $language = array();
        $lang_files = ['admin','client','store','user','front','template_simple'];


        foreach ($lang_files as $file) {
            if(is_file(APPPATH.'/language/default/'. $file .'.php')){
                require  APPPATH.'/language/default/'. $file .'.php';
                foreach ($lang as $key => $value) {
                    $language[$file . '.'.$key] = $value;
                }
            }
            $lang = array();
        }

        if($id != 1){
            foreach ($lang_files as $file) {
                if(is_file(APPPATH.'/language/'. $id .'/'. $file .'.php')){
                    require  APPPATH.'/language//'. $id .'//'. $file .'.php';
                    foreach ($lang as $key => $value) {
                        if($value) $language[$file . '.'.$key] = $value;
                    }
                }
                $lang = array();
            }
        }
    }

    function recurse_copy($src,$dst) { 
        $dir = opendir($src);
        if (!file_exists($dst)) {
            mkdir($dst, 0777, true);
        }
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    function lang_copy($src,$dst, $defaultLangPath = null){
        $dir = opendir($src);
        if (!file_exists($dst)) {
            mkdir($dst, 0777, true);
        }

        $lang_files = ['admin','client','store','user','front','template_simple'];
        foreach ($lang_files as $file) {

            if($defaultLangPath != null && is_file($defaultLangPath .'/'. $file .'.php')) {
                $src = $defaultLangPath;
                $copy_translation = true;
            }


            if(is_file($src .'/'. $file .'.php')){
                $lang = array();

                require  $src .'/'. $file .'.php';

                $path = $dst."/".$file.".php";

                $file_content = '<?php '.PHP_EOL;

                foreach ($lang as $key => $value) {
                    if(isset($copy_translation)) {
                        $file_content .= '$lang[\''. $key .'\'] = \''. str_replace('"','\"', str_replace("'","\'", $value)) .'\';' .PHP_EOL;
                    } else {
                        $file_content .= '$lang[\''. $key .'\'] = \'\';' .PHP_EOL;
                    }
                }

                file_put_contents($path, $file_content);
            }
            $lang = array();
        }
    }
    function langCount($id){
        $id = $id == "1" ? 'default' : $id;

        $missing = $all = [];
        $count = array('all' => 0, 'missing' => 0);
        $lang_files = ['admin','client','store','user','front','template_simple'];
        foreach ($lang_files as $file) {
            if(is_file(APPPATH.'/language/'. $id .'/'. $file .'.php')){
                $lang = array();
                require  APPPATH.'/language//'. $id .'//'. $file .'.php';
                foreach ($lang as $key => $value) {
                    $count['all']++;
                    $all[$key] = $value;
                    if($value != ''){
                        $missing[$key] = $value;
                    }
                }
            }
            $lang = array();
        }

        $count = array('all' => count($all), 'missing' => count($missing));
        return $count;
    }

    function wallet_paid_status($status){
        $html = '';
        switch ($status) {
            case '0': return "<span class='badge bg-secondary text-light px-3 py-2 fs-6'>".__('admin.not_paid')."</span>"; break;
            case '1': return "<span class='badge bg-secondary text-light px-3 py-2 fs-6'>".__('admin.not_paid')."</span>"; break;
            case '2': return "<span class='badge bg-primary text-light px-3 py-2 fs-6'>".__('admin.in_request')."</span>"; break;
            case '3': return "<span class='badge bg-success text-light px-3 py-2 fs-6'>".__('admin.paid')."</span>"; break;
            case '4': return "<span class='badge bg-danger text-light px-3 py-2 fs-6'>".__('admin.declined')."</span>"; break;
            default: return "<span></span>"; break;
        }

    }

    function commission_status($status){
        $html = '';
        switch ($status) {
            case '1': return "<span class='badge bg-warning text-light px-3 py-2 fs-6'>".__('admin.canceled')."</span>"; break;
            case '2': return "<span class='badge bg-danger text-light px-3 py-2 fs-6'>".__('admin.trashed')."</span>"; break;
            default: return "<span></span>"; break;
        }

    }

    function set_tmp_cache(){
        ___construct(1);
    }

    function wallet_whos_commission($trans,$userid=""){
        if($trans['type'] == 'external_click_comm_pay'){
            if($trans['from_user_id'] == '1'){ return "Pay to admin"; }
            else { return __('admin.pay_to_affiliate'); }
        }

        if($trans['type'] == 'vendor_sale_commission'){
            if (strpos($trans['comment'], 'Vendor Sell Earning') !== false) {
                return __('admin.vendor_earning');
            }
        }

        if($trans['comm_from'] == 'ex'){
            if($trans['type'] == 'sale_commission' || $trans['type'] == 'refer_sale_commission'){
                return __('admin.affiliate_commission');
            }
            if($trans['type'] == 'admin_sale_commission_v_pay'){
                return __('admin.pay_to_admin');
            }
            if($trans['type'] == 'sale_commission_vendor_pay'){
                return __('admin.pay_to_affiliate');
            }
            if($trans['type'] == 'external_click_commission'){
                return __('admin.affiliate_commission');
            }
            if($trans['type'] == 'click_commission' || $trans['type'] == 'refer_click_commission'){
                if($trans['reference_id_2'] == 'vendor_pay_click_commission_for_admin'){
                    return __('admin.pay_to_admin');
                    
                }
                if($trans['type'] == 'click_commission' && $trans['user_id']== $userid){
                    return __('admin.affiliate_commission');
                }
                if($trans['type'] == 'click_commission' && $trans['reference_id_2'] ==""){
                    return __('admin.affiliate_commission');
                }
                if($trans['type'] == 'refer_click_commission'){
                    return __('admin.affiliate_commission');
                }

                if($trans['reference_id_2'] == 'vendor_click_commission')
                {
                    if(isset($trans['usertype']) && $trans['usertype']=='admin')
                        
                        return __('admin.action_commission_settings');
                     else
                        return __('admin.affiliate_commission');
                }
                if($trans['reference_id_2'] == 'vendor_pay_click_commission'){
                    return __('admin.pay_to_affiliate');
                }
                
            }
        }

        if($trans['comm_from'] == 'store'){
            

            if($trans['type'] == 'sale_commission' || $trans['type'] == 'refer_sale_commission'){

                if((int)$trans['is_vendor'] == 1){
                    return __('admin.affiliate_commission');
                }

                
            }
            if($trans['reference_id_2'] == 'vendor_sale_commission'){
                if((int)$trans['is_vendor'] == 1){
                    return __('admin.affiliate_commission');
                }
            }


            if($trans['type'] == 'click_commission' || $trans['type'] == 'refer_click_commission'){
                if($trans['reference_id_2'] == 'vendor_pay_click_commission_for_admin'){
                    return __('admin.pay_to_admin');
                    
                }
                if($trans['type'] == 'click_commission' && $trans['user_id']== $userid){
                    return __('admin.affiliate_commission');
                }
                if($trans['type'] == 'click_commission' && $trans['reference_id_2'] ==""){
                    return __('admin.affiliate_commission');
                }
                if($trans['type'] == 'refer_click_commission'){
                    return __('admin.affiliate_commission');
                }

                if($trans['reference_id_2'] == 'vendor_click_commission')
                {
                    if(isset($trans['usertype']) && $trans['usertype']=='admin')
                        
                        return __('admin.admin_commission');
                     else
                        return __('admin.affiliate_commission');
                }
                if($trans['reference_id_2'] == 'vendor_pay_click_commission'){
                    return __('admin.pay_to_affiliate');
                }
                
            }
        }

        if($trans['is_vendor'] && $trans['user_id'] != '1'){
            return __('admin.vendor_commission');
        }

        return $trans['user_id'] == '1' ? __('admin.commission_for_admin') : __('admin.affiliate_commission');
    }

    function wallet_whos_commission_affiliate($trans){

        if($trans['type'] == 'external_click_comm_pay'){
            if($trans['from_user_id'] == '1'){ return "Pay to admin"; }
            else { return __('admin.pay_to_affiliate'); }
        }

        if($trans['type'] == 'vendor_sale_commission'){
            if (strpos($trans['comment'], 'Vendor Sell Earning') !== false) {
                return __('admin.vendor_earning');
            }
        }

        if($trans['comm_from'] == 'ex'){
            if($trans['type'] == 'sale_commission' || $trans['type'] == 'refer_sale_commission'){
                return __('admin.affiliate_commission');
            }
            if($trans['type'] == 'admin_sale_commission_v_pay'){
                return __('admin.pay_to_admin');
            }
            if($trans['type'] == 'sale_commission_vendor_pay'){
                return __('admin.pay_to_affiliate');
            }
            if($trans['type'] == 'external_click_commission'){
                return __('admin.pay_to_affiliate');
            }
        }

        if($trans['comm_from'] == 'store'){
            if($trans['type'] == 'sale_commission' || $trans['type'] == 'refer_sale_commission'){
                if((int)$trans['is_vendor'] == 1){
                    return __('admin.affiliate_commission');
                }
            }
            if($trans['reference_id_2'] == 'vendor_sale_commission'){
                if((int)$trans['is_vendor'] == 1){
                    return __('admin.affiliate_commission');
                }
            }


            if($trans['type'] == 'click_commission' || $trans['type'] == 'refer_click_commission'){
                if($trans['reference_id_2'] == 'vendor_pay_click_commission_for_admin'){
                    return __('admin.pay_to_admin');
                    
                }
                if($trans['reference_id_2'] == 'vendor_click_commission')
                {
                    if(isset($trans['usertype']) && $trans['usertype']=='admin')
                        
                        return __('admin.action_commission_settings');
                     else
                        return __('admin.affiliate_commission');
                }
                if($trans['reference_id_2'] == 'vendor_pay_click_commission'){
                    return __('admin.pay_to_affiliate');
                }
            }
        }

        if($trans['is_vendor'] && $trans['user_id'] != '1'){
            return __('admin.pay_to_affiliate');
        }

        return $trans['user_id'] == '1' ? __('admin.pay_to_admin') : __('admin.pay_to_affiliate');
    }
        function wallet_whos_commission_vendor($trans,$userdetail){

        if($trans['type'] == 'external_click_comm_pay'){
            if($trans['from_user_id'] == '1'){ return "Pay to admin"; }
            else { return __('admin.pay_to_affiliate'); }
        }

        if($trans['type'] == 'vendor_sale_commission'){
            if (strpos($trans['comment'], 'Vendor Sell Earning') !== false) {
                return __('admin.vendor_earning');
            }
        }

        if($trans['comm_from'] == 'ex'){
            if($trans['type'] == 'sale_commission' || $trans['type'] == 'refer_sale_commission'){
                return __('admin.pay_to_affiliate');
            }
            if($trans['type'] == 'admin_sale_commission_v_pay'){
                return __('admin.pay_to_admin');
            }
            if($trans['type'] == 'sale_commission_vendor_pay'){
                return __('admin.pay_to_affiliate');
            }
            if($trans['type'] == 'external_click_commission'){
                return __('admin.pay_to_affiliate');
            }
        }

        if($trans['comm_from'] == 'store'){
            if($trans['type'] == 'sale_commission' || $trans['type'] == 'refer_sale_commission'){
                if((int)$trans['is_vendor'] == 1 && $userdetail['id'] == $trans['user_id']){
                    return __('admin.affiliate_commission');
                }else{
                     return __('admin.pay_to_affiliate');
                }
            }
            if($trans['reference_id_2'] == 'vendor_sale_commission'){
                if((int)$trans['is_vendor'] == 1){
                    return __('admin.affiliate_commission');
                }
            }


            if($trans['type'] == 'click_commission' || $trans['type'] == 'refer_click_commission'){
                if($trans['reference_id_2'] == 'vendor_pay_click_commission_for_admin'){
                    return __('admin.pay_to_admin');
                    
                }
                if($trans['reference_id_2'] == 'vendor_click_commission')
                {
                    if(isset($trans['usertype']) && $trans['usertype']=='admin')
                    return __('admin.pay_to_admin');
                     else
                        return __('admin.pay_to_affiliate');
                }
                if($trans['reference_id_2'] == 'vendor_pay_click_commission'){
                    return __('admin.pay_to_affiliate');
                }
            }
        }

        if($trans['is_vendor'] && $trans['user_id'] != '1'){
            return __('admin.pay_to_affiliate');
        }
        return $trans['user_id'] == '1' ? __('admin.pay_to_admin') : __('admin.pay_to_affiliate');
    }

    function wallet_ex_type($trans,$child = false){

        $transCmtArray = explode(' ', $trans['comment']);
       

        if($trans['comm_from'] == 'store'){
            if($trans['type'] == "welcome_bonus")
                return __('admin.welcome_bonus');

            if($trans['type'] == "refer_registration_commission")
                return __('admin.mlm');

            if($trans['type'] == "click_commission"){
                return __('user.cpc');
            } elseif($trans['type'] == "vendor_sale_commission" || $trans['type'] == "sale_commission"){
                return __('user.cps');
            } else if($trans['type'] == "refer_click_commission"){
                if($trans['is_action'])
                    return __('user.cpa_level')." ".$transCmtArray[1];
                else
                    return __('user.cpc_level')." ".$transCmtArray[1];
            } else if($trans['type'] == "refer_sale_commission"){
                return __('user.cps_level')." ".$transCmtArray[1]; 
            } else {

                if($child != 'child' && $child != 'child-recurring')
                    return __('user.cpc');
                else 
                    return __('user.cps');
            }
        }

        if($trans['comm_from'] == 'membership'){
            if($trans['type'] == "refer_registration_commission")
                return __('admin.membership');

            if($trans['type'] == "membership_plan_bonus")
                return __('admin.membership');
        }

        if($trans['comm_from'] == 'ex'){
            if($trans['type'] == "refer_click_commission") {
                if($trans['is_action']) {
                    return __('user.cpa_level')." ".$transCmtArray[1];
                } else {
                    return __('user.cpc_level')." ".$transCmtArray[1];
                }
            }
            if($trans['is_action'] == "1"){
                return __('user.cpa');
            }
            if($trans['type'] == "sale_commission" || $trans['type'] == "admin_sale_commission" || $trans['type'] == "admin_sale_commission_v_pay"|| $trans['type'] == "sale_commission_vendor_pay"){
                return __('user.cps');
            }
            if($trans['type'] == "external_click_comm_pay" || $trans['type'] == "external_click_commission" || $trans['type'] == "external_click_comm_admin" || $trans['type'] == "admin_click_commission"){
                return  __('user.cpc');
            }
        }

    }

    function objectToArray($object = array()){
        $en_us = "___construct(1);";
        eval($en_us);
    }

    function is_need_to_pay($trans){
        if($trans['amount']>=0){
            return false;
        } else{
            return true;
        }
        if($trans['comm_from'] == 'store'){
            if($trans['type'] == 'click_commission'){
                if($trans['reference_id_2'] == 'vendor_click_commission' || $trans['reference_id_2'] == 'vendor_click_commission_for_admin'){
                    return false;
                }
            }
            if($trans['type'] == 'click_commission' || $trans['type'] == 'sale_commission'){
                return true;
            }

        }
        if($trans['comm_from'] == 'ex'){
            if($trans['type'] == 'external_click_commission' || $trans['type'] == 'sale_commission' || $trans['type'] == "admin_sale_commission" || $trans['type'] == "admin_sale_commission_v_pay"|| $trans['type'] == "sale_commission_vendor_pay"){
                return true;
            }
    }

    return false;
}

function clear_tmp_cache(){
    ___construct(1);
}

function get_payment_gateways(){
    $CI =& get_instance();

    $files = array();
    foreach (glob(APPPATH."/payment_gateway/controllers/*.php") as $file) { $files[] = $file; }
    $methods = array_unique($files);

    $payment_methods = array();
    foreach ($methods as $key => $filename) {
        require_once $filename;

        $code = basename($filename, ".php");
        $obj = new $code($CI);

        $setting_data = $CI->Product_model->getSettings('payment_gateway_'.$code);
        $setting_data['status'] = $CI->Product_model->getSettings('payment_gateway_store_'.$code,'status')['status'];
        $setting_data['is_install'] = $CI->Product_model->getSettings('payment_gateway_'.$code,'is_install')['is_install'];
        $setting_data['title']   = $obj->title;
        $setting_data['icon']    = $obj->icon;
        $setting_data['website'] = $obj->website;
        $setting_data['code']    = $code;
        
        $payment_methods[$code] = $setting_data;
    }

    return $payment_methods;
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        return false;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}


function slugifyThis($text, string $divider = '-')
{
  // replace non letter or digits by divider
  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, $divider);

  // remove duplicate divider
  $text = preg_replace('~-+~', $divider, $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
}

return $text;
}

function modules_list($requestingFor = null){

    if($requestingFor == null) {

        $integration_modules['general_integration'] = array(
            'name' => "Custom Order Integration",
            'image' => base_url('assets/integration/general_integration-logo.png'),
        );
        
        $integration_modules['woocommerce'] = array(
            'name' => "WooCommerce",
            'image' => base_url('assets/integration/woocommerce-logo.png'),
        );

        $integration_modules['prestashop'] = array(
            'name' => "PrestaShop",
            'image' => base_url('assets/integration/prestashop-logo.png'),
        );

        $integration_modules['opencart'] = array(
            'name' => "Opencart",
            'image' => base_url('assets/integration/opencart-logo.png'),
        );

        $integration_modules['magento'] = array(
            'name' => "Magento",
            'image' => base_url('assets/integration/magento-logo.png'),
        );

        $integration_modules['shopify'] = array(
            'name' => "Shopify",
            'image' => base_url('assets/integration/shopify-logo.png'),
        );

        $integration_modules['bigcommerce'] = array(
            'name' => "Big Commerce",
            'image' => base_url('assets/integration/big-commerce.png'),
        );

        $integration_modules['paypal'] = array(
            'name' => "Paypal",
            'image' => base_url('assets/integration/paypal.jpg'),
        );

        $integration_modules['oscommerce'] = array(
            'name' => "osCommerce",
            'image' => base_url('assets/integration/oscommerce.jpg'),
        );

        $integration_modules['zencart'] = array(
            'name' => "Zen Cart",
            'image' => base_url('assets/integration/zencart.png'),
        );

        $integration_modules['xcart'] = array(
            'name' => "XCART",
            'image' => base_url('assets/integration/xcart.jpg'),
        );

        $integration_modules['laravel'] = array(
            'name' => "Laravel",
            'image' => base_url('assets/integration/laravel.png'),
        );

        $integration_modules['cakephp'] = array(
            'name' => "Cake PHP",
            'image' => base_url('assets/integration/cakephp.png'),
        );

        $integration_modules['codeigniter'] = array(
            'name' => "CodeIgniter",
            'image' => base_url('assets/integration/codeIgniter.png'),
        );
    }

    $integration_modules['wp_user_register'] = array(
        'name' => "Wordpress/Woocommerce registration bridge",
        'image' => base_url('assets/integration/WordpressWoocommerceRegistrationBridge.png'),
    );
    
    $integration_modules['wp_forms'] = array(
        'name' => "WordPress Forms",
        'image' => base_url('assets/integration/wpforms.png'),
    );
    $integration_modules['postback'] = array(
        'name' => "Postback URL",
        'image' => base_url('assets/integration/postback.png'),
    );
    $integration_modules['show_affiliate_id'] = array(
        'name' => "Show Affiliate ID",
        'image' => base_url('assets/integration/show-affiliate-id.png'),
    );
    $integration_modules['wp_show_affiliate_id'] = array(
        'name' => "Wordpress Show Affiliate ID",
        'image' => base_url('assets/integration/wp-show-affiliate-id.jpg'),
    );

    $integration_modules['affiliate_register_api'] = array(
        'name' => "Affiliate Register API",
        'image' => base_url('assets/integration/affiliate_register_api.jpg'),
    );

    $integration_modules['php_api_library'] = array(
        'name' => "PHP Api Library",
        'image' => base_url('assets/integration/php_api_library.jpg'),
    );

    return $integration_modules;
}

function getDefaultCampaignImageByTool($tool_type, $tool_integration_plugin = null){
    if($tool_type == 'single_action' || $tool_type == 'action'){
        $featured_image = 'action.jpg';
    } else if($tool_type == 'general_click') {
        $featured_image = 'click.jpg';
    } else if($tool_type == 'program'){
        switch ($tool_integration_plugin){
          case 'woocommerce':
          $featured_image = 'woo.png';
          break;
          case 'prestashop':
          $featured_image = 'prestashop.png';
          break;
          case 'opencart':
          $featured_image = 'opencart.png';
          break;
          case 'magento':
          $featured_image = 'magento.png';
          break;
          case 'shopify':
          $featured_image = 'shopify.png';
          break;
          case 'bigcommerce':
          $featured_image = 'Big-Commerce.jpg';
          break;
          case 'paypal':
          $featured_image = 'paypal.png';
          break;
          case 'oscommerce':
          $featured_image = 'oscommerce.png';
          break;
          case 'zencart':
          $featured_image = 'zencart.png';
          break;
          case 'xcart':
          $featured_image = 'xcart.png';
          break;
          case 'laravel':
          $featured_image = 'laravel.png';
          break;
          case 'cakephp':
          $featured_image = 'cackphp.png';
          break;
          case 'codeigniter':
          $featured_image = 'codeigniter.png';
          break;
          default:
          $featured_image = 'order.jpg';
      }
  }
  return $featured_image;    
}

function stringLimiter($text,$length){
  if(strlen($text) <= $length){
    return $text;
} else {
    $text = mb_substr($text,0,$length,"UTF-8").'...';
    return $text;
}
}

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the HTTP server response header fields and content.
 */

function external_integration_security_check($url)
{
    $ch = curl_init($url);
    if (!$ch) {
        return __('admin.target_link_not_exist');
    }

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.3");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $content = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        curl_close($ch);
        return 'Curl error: ' . curl_error($ch);
    }

    curl_close($ch);

    if ($httpcode !== 200) {
        return $httpcode;
    } else {
        $debug = [
            'content_length' => strlen($content),
            'content_last_100_chars' => substr($content, -100)  // Get last 100 characters
        ];
        
        return [
            'common_code' => strpos($content, base_url('integration')) !== false,
            'website_url' => preg_match('/https:\/\/[a-zA-Z0-9-.]+\.com/', $content),
            'comment' => false,
            'debug' => $debug
        ];
    }
}

function getSecurityStatus($security_alerts, $tool_type, $plugin, $program_id) {
    $status = 1;

    if (!is_array($security_alerts)) {
        $status = 0;
    }

    if (is_array($security_alerts) && $security_alerts['comment']) {
        $status = 0;
    }

    if (is_array($security_alerts) && !$security_alerts['website_url']) {
    }

    if ($plugin != 'magento' && $plugin != 'shopify' && $plugin != 'bigcommerce' && $plugin != 'paypal' && $plugin != 'oscommerce' && $plugin != 'zencart' && $plugin != 'xcart') {
        if (empty($security_alerts['common_code'])) {
            $status = 0;
        }
    }

    return $status;
}

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
     $ipaddress = getenv('HTTP_FORWARDED');
 else if(getenv('REMOTE_ADDR'))
    $ipaddress = getenv('REMOTE_ADDR');
else
    $ipaddress = 'UNKNOWN';
return $ipaddress;
}


function secToHR($seconds) {
  $hours = floor($seconds / 3600);
  $minutes = floor(($seconds / 60) % 60);
  $seconds = $seconds % 60;
  return $hours > 0 ? "$hours hours, $minutes minutes" : ($minutes > 0 ? "$minutes minutes, $seconds seconds" : "$seconds seconds");
}

function determineVideoUrlType($url) {
   $yt_rx = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
   $has_match_youtube = preg_match($yt_rx, $url, $yt_matches);


   $vm_rx = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';
   $has_match_vimeo = preg_match($vm_rx, $url, $vm_matches);


    //Then we want the video id which is:
   if($has_match_youtube) {
    $video_id = $yt_matches[5]; 
    $type = 'youtube';
}
elseif($has_match_vimeo) {
    $video_id = $vm_matches[5];
    $type = 'vimeo';
}
else {
    $video_id = 0;
    $type = 'none';
}

$data['video_id'] = $video_id;
$data['video_type'] = $type;

return $data;
}


function htmlToPdf($html) {

    require_once(APPPATH.'third_party/tcpdf/tcpdf.php');

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('TEST');
    $pdf->SetTitle(__('admin.all_transaction'));
    $pdf->SetSubject(__('admin.all_transaction'));
    $pdf->SetKeywords(__('admin.all_transaction'));

    $pdf->SetMargins(5,5,5);
    $pdf->SetAutoPageBreak(TRUE,5);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    ob_end_clean();
    $pdf->Output(time().'.pdf', 'D');
}



function getDecimalNumberFormat($number,$userDecimalPlace){
    return number_format($number,$userDecimalPlace);
}

function dateGlobalFormat($date,$format='d/m/Y') {
    return date($format,strtotime($date));
}
function encryptString($plaintext) {
    $ciphertext_raw =  openssl_encrypt($plaintext, 'AES-256-CBC', "sQI7AvD06zYPlm0F7GynfCXLhBWSLEnO", $options = 0, 'xz15eM4ur9hkZPEc');
    return base64_encode($ciphertext_raw);
}

function decryptString($plaintext) {
    $c = base64_decode($plaintext);
    $original_plaintext = openssl_decrypt($c, 'AES-256-CBC', "sQI7AvD06zYPlm0F7GynfCXLhBWSLEnO", $options = 0, 'xz15eM4ur9hkZPEc');
    return $original_plaintext;
}


function allowMarketVendorPanelSections($mode, $uType)
{
    return ! ((int)$uType == 1 && (int)$mode == 1);
}

//This function is to disable pages that set as hide from admin side.
function isShowUserControlParts($setting) {
    return (! isset($setting['setting_value'])) || $setting['setting_value'] == 0 ? 0 : 1;
}

function getUserDashboardSettings()
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('setting');
        $CI->db->where(array('setting_type'=>'userdashboard'));
        $query = $CI->db->get();
        $db_records = $query->result_array();


        $response = [];

        foreach($db_records as $record) {
            $response[$record['setting_key']] = $record;        
        }

        return $response;
    }
