<?php
if (!class_exists('Newsletter_Handler')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/class.genericsection_handler.php';
    class Newsletter_Handler extends GenericSection_Handler
    {
        public $section_name    = 'newsletter';
        public $table_name      = "newsletters";
        public $database_prefix = '';
        public $search_bits     = array('a.id');
        public function fromDatabase($id)
        {
            $result                   = parent::fromDatabase($id);
            $result['newsletter_url'] = "/mailout/" . formatURLSafe(sprintf("%s_%s", $result['subject'], $result['id'])) . ".html";
            return $result;
        }
        public function formatNumber($id)
        {
            return sprintf('MO#%06d', $id);
        }
        public function getOptionList($table_name)
        {
            global $DB;
            global $_GLOBALS;
            $table       = '';
            $option_list = array();
            switch ($table_name) {
                case 'id':
                    $table = 'newsletters';
                    $key   = 'id';
                    $value = "date_added";
                    break;
                case 'site_id':
                    $table = '`highwaygroup`.`company_info`';
                    $key   = 'company_id';
                    $value = 'company_name';
                    break;
                case 'display_online':
                    return array("y" => "Yes", "n" => "No");
                case 'staff_id':
                    return getStaffList();
                case 'style_id':
                    $table = 'newsletter_styles';
                    if (3 != $_GLOBALS['site_settings']['site_id']) {
                        $table .= " WHERE site_id=" . $_GLOBALS['site_settings']['site_id'];
                    }

                    $key   = 'style_id';
                    $value = 'style_description';
                    break;
                case 'ext_fields':
                    return array(
                        'modified_time' => 'UNIX_TIMESTAMP(a.modified)',
                        'time_added'    => 'UNIX_TIMESTAMP(a.date_added)',
                        'time_sent'     => 'UNIX_TIMESTAMP(a.date_sent)',
                    );
                    break;
                default:
                    return parent::getOptionList($table_name);
            }
            if ('' != $table) {
                $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
                while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                    $option_list[$row[0]] = $row[1];
                }

            }
            return $option_list;
        }
        public function generateMemberList($item)
        {
            moduleLoad('member'); //memberSearch

            $country         = explode(",", $item["country"]);
            $options         = explode(",", $item["best_described"]);
            $include_members = in_array("site_3", explode(",", $item["subscr_list"]));
            $include_mailing = false;
            foreach (explode(",", $item["subscr_list"]) as $v) {
                if (substr($v, 0, 6) == "style_") {
                    $include_mailing = true;
                }
            }

            global $DB;
            if (in_array("", $country)) {
                unset($country);
            }

            if (in_array("", $options)) {
                unset($options);
            }

            $email_list = array();
            if ($include_members) {
                $email_list = array_merge($email_list, getMembersEmails());
            }

            if ($options) {
                $email_list = array_intersect_key($email_list, getOptionsEmails($options));
            }

            if ($country) {
                $email_list = array_intersect_key($email_list, getCountryEmails($country));
            }

            if ($include_mailing) {
                $addme_list = getMailingEmails();
                if ($country) {
                    $addme_list = array_intersect_key($addme_list, getCountryMailing($country));
                }

                $email_list = array_merge($email_list, $addme_list);
            }
            foreach ($email_list as $key => $value) {
                if (strpos($value, '@') === false) {
                    unset($email_list[$key]);
                }
            }

            $fault = true;
            while ($fault) {
                $fault = false;
                foreach ($email_list as $key => $value) {
                    if (str_replace(',', ' ', $value) != $value) {
                        $fault            = true;
                        $email_list[$key] = $value = str_replace(',', ' ', $value);
                    }
                    if (str_replace('  ', ' ', $value) != $value) {
                        $fault            = true;
                        $email_list[$key] = $value = str_replace('  ', ' ', $value);
                    }
                    if (strtolower(substr($value, 0, 12)) == 'not supplied') {
                        $fault            = true;
                        $email_list[$key] = $value = trim(substr($value, 12));
                    }
                    if (strtolower(substr($value, 0, 7)) == 'unknown') {
                        $fault            = true;
                        $email_list[$key] = $value = trim(substr($value, 7));
                    }
                    if (substr($value, 0, 1) == '<') {
                        $fault            = true;
                        $email_list[$key] = $value = trim(substr($value, 1, -1));
                    }
                }
            }
            asort($email_list);

            $email_list = array_merge(array(
                'errors@coinopexpress.com' => 'Development User',
            ), $email_list);

            return $email_list;
            /*
        global $DB,$_GLOBALS;
        $member_list = array();
        $first = true;
        foreach(explode(',', $item['subscr_list']) as $subscr)
        {
        $k = explode('_', $subscr);
        $v = array_pop($k);
        $k = implode('_', $k);
        $temp = array();
        switch($k)
        {
        case 'site':
        $criteria = $_GLOBALS['criteria_options']['member'];
        $criteria['status'] = array("active", "not_contacted");
        $criteria['site_id'] = $v;
        $temp = memberSearch($criteria);
        break;
        case 'style':
        $temp = array();
        $query = $DB->query("SELECT ml.member_id FROM `highwaygroup`.`new_members_mailing_list` AS `ml` INNER JOIN `highwaygroup`.`new_members` AS `m` ON m.member_id=ml.member_id WHERE m.status!='not_active' AND ml.active='y' AND ml.style_id=" . intval($v));
        while($row = $DB->fetch_assoc($query)) $temp[$row['member_id']] = $row['member_id'];
        $DB->free_result($query);
        break;
        }
        if($first) $member_list = $temp;
        else $member_list = array_intersect($member_list, $temp);
        $first = false;
        }
        $criteria = array();
        foreach(array('country', 'best_described') as $k) if ($item[$k]!='') $criteria[$k] = explode(',',$item[$k]);
        if(is_array($criteria['best_described']) && in_array('oth',$criteria['best_described'])) $criteria['best_described'][]='';
        $criteria['country'][]='';
        if(count($criteria)) $member_list=array_intersect($member_list,memberSearch($criteria));
        return $member_list;
         */
        }
        public function styleFromDatabase($style_id)
        {
            global $DB;
            $results = array();
            if (is_array($style_id)) {
                $query = $DB->query("SELECT * FROM newsletter_styles WHERE style_id" . ((count($style_id)) ? (" IN(" . implode(',', $style_id) . ")") : ""));
                while ($row = $DB->fetch_assoc($query)) {
                    $results[$row['style_id']] = $row;
                }

                $DB->free_result($query);
            } else {
                $results = $DB->query_first("SELECT * FROM newsletter_styles WHERE style_id=" . intval($style_id) . " LIMIT 1", MYSQLI_ASSOC);
            }

            return $results;
        }
        public function mailinglistToDatabase($newsletter_id, $mailing_list)
        {
            global $DB;
            $DB->query("DELETE FROM `newsletter_emails` WHERE `newsletter_id` = " . intval($newsletter_id));
            foreach ($mailing_list as $row) {
                $DB->query("INSERT INTO `newsletter_emails` (`newsletter_id`, `member_id`, `email_address`, `email_status`) VALUES (" . intval($newsletter_id) . ", " . intval($row['member_id']) . ", " . formatSQLString($row["email_address"]) . ", " . formatSQLString($row['email_status']) . ")");
            }

        }
        public function mailinglistFromDatabase($newsletter_id)
        {
            moduleLoad('invoice'); //invoiceGetValue, invoiceSearch

            global $DB;
            $results = array();
            $query   = $DB->query("SELECT `id`, `newsletter_id`, `member_id`, `email_address`, `email_status` FROM `newsletter_emails` WHERE `newsletter_id` = " . intval($newsletter_id));
            while ($row = $DB->fetch_assoc($query)) {
                $results[$row['id']] = $row;
            }

            $DB->free_result($query);

            //Hack to update ordered customers
            /*
            foreach(invoiceGetValue(invoiceSearch(array('newsletter_id' => $newsletter_id)), 'member_id') as $member_id) foreach($results as $k=>$v) if($v['member_id'] == $member_id && $v['email_status'] != 'ordered')
            {
            $results[$k]['email_status'] = 'ordered';
            $DB->query("UPDATE newsletter_emails SET email_status='ordered' WHERE id=" . intval($v['id']) . " LIMIT 1");
            break;
            }
             */

            return $results;
        }
        public function negotiateContent($content, $vars)
        {
            while (($pos1 = strripos($content, '<negotiate_list')) !== false && ($pos2 = stripos($content, '</negotiate_list>', $pos1)) !== false) {
                $struct = substr($content, $pos1, 17 + $pos2 - $pos1);
                $result = '';

                /**
                 * Find what field we are negotiating
                 */
                $match_tag = array();
                if (preg_match('/^<negotiate_list([^>]*)>/i', $struct, $match_tag)) {
                    $field_name = '';
                    $match_attr = array();
                    preg_match_all('/([a-z]+)="([^"]*)"/i', $match_tag[1], $match_attr);
                    if (is_array($match_attr[1])) {
                        foreach ($match_attr[1] as $k => $v) {
                            if (strtolower($v) == 'field') {
                                $field_name = $match_attr[2][$k];
                            }
                        }
                    }

                    /**
                     * Choose the negotiated content
                     */
                    if ('' != $field_name) {
                        $result = $this->negotiateAlternatives(substr($struct, strlen($match_tag[0]), -17), $vars[$field_name]);
                    }

                }
                /**
                 * Replace the structure with the resulting content
                 */
                $content = substr($content, 0, $pos1) . $result . substr($content, $pos2 + 17);
            }
            return $content;
        }
        public function negotiateAlternatives($content, $val)
        {
            $default = '';
            while (($pos1 = strripos($content, '<negotiate_item')) !== false && ($pos2 = stripos($content, '</negotiate_item>', $pos1)) !== false) {
                $struct = substr($content, $pos1, 17 + $pos2 - $pos1);

                /**
                 * Test this structure against our value
                 */
                $field_value = '';
                $match_tag   = array();
                if (preg_match('/^<negotiate_item([^>]*)>/i', $struct, $match_tag)) {
                    $match_attr = array();
                    preg_match_all('/([a-z]+)="([^"]*)"/i', $match_tag[1], $match_attr);
                    if (is_array($match_attr[1])) {
                        foreach ($match_attr[1] as $k => $v) {
                            if (strtolower($v) == 'value' && $match_attr[2][$k] == $val) {
                                return substr($struct, strlen($match_tag[0]), -17);
                            }

                            if (strtolower($v) == 'default' && strtolower($match_attr[2][$k]) == 'default') {
                                $default = substr($struct, strlen($match_tag[0]), -17);
                            }

                        }
                    }

                }

                /**
                 * Remove the structure from the content
                 */
                $content = substr($content, 0, $pos1) . substr($content, $pos2 + 17);
            }
            return $default;
        }
        public function generateAlternateText($content)
        {
            $basename = "https://" . $_SERVER['HTTP_HOST'] . "/";

            //Get base address
            $matches = array();
            if (preg_match('/<base[^>]* href="([^"]+)"[^>]* \\/>/siU', $content, $matches)) {
                $basename = $matches[1];
            }

            //Remove html head
            $content = preg_replace('/<head[^>]*>.*<\\/head>/siU', '', $content);

            //Add extra newlines
            $content = preg_replace('/<br \\/>/siU', "\n", $content);
            $content = preg_replace('/<hr \\/>/siU', "\n----------------------------------------\n", $content);

            //Replace images
            $content = preg_replace('/<img[^>]* alt="([^"]+)"[^>]*\\/>/siU', '\\1', $content);

            //Replace html links
            $content = preg_replace('/<a[^>]* href="([^"]+:[^"]+)"[^>]*>(.*)<\\/a>/siU', '\\2 -> \\1', $content);
            $content = preg_replace('/<a[^>]* href="([^"]+)"[^>]*>(.*)<\\/a>/siU', '\\2 -> ' . rtrim($basename, '/') . '\\1', $content);

            //Remove html tags
            $content = strip_tags($content);

            //Cleanup html entities
            $content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');

            //Cleanup whitespace
            $content = strtr($content, array("\r\n" => "\n"));
            $content = strtr($content, array("\r" => "\n"));
            $content = strtr($content, array("\t" => " "));
            $content = preg_replace('/  +/', ' ', $content);

            //Remove Duplicate Lines
            $explode = explode("\n", $content);
            foreach ($explode as $k => $v) {
                $explode[$k] = trim($v);
            }

            $found = array();
            $prev  = '';
            foreach ($explode as $k => $v) {
                if (('' == $v && '' == $prev) || (strpos($v, 'http://') !== false && in_array($v, $found)) || (strpos($v, 'https://') !== false && in_array($v, $found))) {
                    unset($explode[$k]);
                    $v = '';
                } else {
                    $found[] = $v;
                }

                $prev = $v;
            }
            $content = implode("\n", $explode);

            return wordwrap($content);
        }
        public function generateMailMerge($content, $vars = false)
        {
            if (!$vars) {
                return preg_replace('/\\[[A-Z_]{1,32}\\]/', '...', $content);
            }

            $replace = array();
            foreach ($vars as $k => $v) {
                if (!is_array($v)) {
                    $replace['[' . strtoupper($k) . ']'] = hecho($v, 1);
                } else {
                    $replace['[' . strtoupper($k) . ']'] = "";
                }
            }

            return strtr($content, $replace);
        }
        public function templateList()
        {
            global $DB;

            $result = array();
            if ($query = $DB->query(sprintf(
                "SELECT `template_id`, `template_name`, `template_subject`, `template_text` FROM `highwaygroup`.`email_templates` WHERE FIND_IN_SET('newsletters', `template_group`)"
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $result[$row['template_id']] = $row;
                }
                $DB->free_result($query);
            }

            return $result;
        }
        public function templateVars($newsletter = array())
        {
            global $DB;
            $id      = intval($newsletter['id']);
            $results = array();
            foreach (getTableColumns('newsletters') as $k) {
                $results['[' . strtoupper($k) . ']'] = '';
            }

            $results['[REQUEST_DATE]'] = date('jS F, Y', time());
            $results['[REQUEST_TIME]'] = date('g:i a', time());
            $results['[HTTP_HOST]']    = array_shift(explode('/', str_replace('https://', '', str_replace('http://', '', $website_info['url']))));
            $results['[STAFF_NAME]']   = '';
            foreach ($newsletter as $k => $v) {
                $results['[' . strtoupper($k) . ']'] = $v;
                $option_list                         = newsletterGetOptionList($k);
                if (count($option_list) > 0) {
                    $results['[' . strtoupper($k) . ']'] = $option_list[$v];
                }

            }
            foreach ($results as $k => $v) {
                $results[$k] = str_replace("\r", "", $v);
            }

            return $results;
        }
    }
}

if (!defined('QUERY_NEWSLETTER')) {
    define('QUERY_NEWSLETTER', 90403);
}

if (!function_exists("getMembersEmails")) {
    function getMembersEmails($member_list = false)
    {
        global $DB;
        if (false === $member_list) {
            $member_list = array();
            $query       = $DB->query(sprintf(
                "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members` AS `m` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `mm` ON `mm`.`member_id` = `m`.`member_id` WHERE `m`.`status` = 0 AND `mm`.`active` = 'y'"
            ));
            while ($row = $DB->fetch_assoc($query)) {
                $member_list[] = $row['member_id'];
            }
            $DB->free_result($query);
        }
        $email_list = array();
        foreach ($member_list as $member_id) {
            $query = $DB->query("SELECT CONCAT_WS(\" \", `name`, `surname`) AS `member_name`, `email_address` AS `email` FROM `highwaygroup`.`new_members` WHERE `member_id` = " . intval($member_id));
            while ($row = $DB->fetch_assoc($query)) {
                foreach (explode(',', $row['email_address']) as $email) {
                    $email_list[trim(strtolower($email))] = trim($row['name']) . '<' . trim($email) . '>';
                }
            }

            $DB->free_result($query);
        }
        return $email_list;
    }
}
if (!function_exists("getMailingEmails")) {
    function getMailingEmails()
    {
        global $DB;
        $email_list = array();
        $query      = $DB->query("SELECT `name`, `email` FROM `mailinglist` WHERE `active` = 1");
        while ($row = $DB->fetch_assoc($query)) {
            foreach (explode(',', $row['email']) as $email) {
                $email_list[trim(strtolower($email))] = trim($row['name']) . '<' . trim($email) . '>';
            }
        }

        $DB->free_result($query);
        return $email_list;
    }
}
if (!function_exists("getCountryEmails")) {
    function getCountryEmails($country_list = array())
    {
        global $DB;
        if (count($country_list)) {
            $member_list = array();
            $query       = $DB->query(sprintf(
                "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members` AS `m` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `mm` ON `mm`.`member_id` = `m`.`member_id` WHERE `m`.`status` = 0 AND `mm`.`active` = 'y' AND `m`.`country` IN(%s)",
                "'" . implode("', '", $country_list) . "'"
            ));
            while ($row = $DB->fetch_assoc($query)) {
                $member_list[] = $row['member_id'];
            }

            $DB->free_result($query);
            return getMembersEmails($member_list);
        } else {
            return getMembersEmails();
        }

    }
}
if (!function_exists("getCountryMailing")) {
    function getCountryMailing($country_list = array())
    {
        global $DB;
        if (count($country_list)) {
            $email_list = array();
            $query      = $DB->query("SELECT `name`, `email` FROM `mailinglist` WHERE `active` = 1 AND `country` IN ('" . implode("', '", $country_list) . "')");
            while ($row = $DB->fetch_assoc($query)) {
                foreach (explode(',', $row['email']) as $email) {
                    $email_list[trim(strtolower($email))] = trim($row['name']) . '<' . trim($email) . '>';
                }
            }

            $DB->free_result($query);
            return $email_list;
        } else {
            return getMailingEmails();
        }

    }
}
if (!function_exists("getOptionsEmails")) {
    function getOptionsEmails($options_list = array())
    {
        global $DB;
        if (is_array($options_list) && count($options_list) > 0) {
            $member_list = array();
            $query       = $DB->query(sprintf(
                "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members` AS `m` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `mm` ON `mm`.`member_id` = `m`.`member_id` LEFT JOIN `highwaygroup`.`new_members_survey` AS `ms` ON `ms`.`member_id` = `m`.`member_id` WHERE `m`.`status` = 0 AND `mm`.`active` = 'y' AND `ms`.`option` IN(%s)",
                "'" . implode("', '", $options_list) . "'"
            ));
            while ($row = $DB->fetch_assoc($query)) {
                $member_list[] = $row['member_id'];
            }

            $DB->free_result($query);
            return getMembersEmails($member_list);
        } else {
            return getMembersEmails();
        }

    }
}
