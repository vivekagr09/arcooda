<?php
if (!class_exists('Newsletter_Handler'))
{
	include_once '/home/common/class.genericsection_handler.php';
	class Newsletter_Handler extends GenericSection_Handler
	{
		var $section_name = 'newsletter';
		var $table_name = "newsletters";
		var $database_prefix = '`highwaygroup`.';
		var $search_bits = array('`a`.`id`');

		/**
		 * Gets newsletter from database and assigns url.
		 * @param mixed $id
		 * @return array
		 */
		function fromDatabase($id)
		{
			$result = parent::fromDatabase($id);
			$result['newsletter_url'] = "/newsletters/" . formatURLSafe(sprintf("%s-%s", $result['subject'], $result['id'])) . ".html";
			return $result;
		}

		/**
		 * Format a given number.
		 * @param mixed $id
		 * @return string
		 */
		function formatNumber($id)
		{
			return sprintf('MO#%06d', $id);
		}

		/**
		 * Returns array of options for table column/s
		 * @param string $table_name
		 * @return array
		 */
		function getOptionList($table_name)
		{
			global $DB;
			global $_GLOBALS;
			$table = '';
			$option_list = array();
			switch ($table_name)
			{
			case 'id':
				$table = '`highwaygroup`.`newsletters`';
				$key = '`id`';
				$value = "`timestamp`";
				break;
			case 'site_id':
				$table = '`highwaygroup`.`company_info`';
				$key = '`company_id`';
				$value = '`company_name`';
				break;
			case 'display_online':
				return array("y" => "Yes", "n" => "No");
			case 'staff_id':
				return getStaffList();
			case 'style_id':
				$table = '`highwaygroup`.`newsletter_styles`';
				if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $table .= " WHERE `site_id` = " . $_GLOBALS['site_settings']['site_id'];
				$key = '`style_id`';
				$value = '`style_description`';
				break;
			//case 'attachment_path': return $_SERVER['DOCUMENT_ROOT'] . '/assets/newsletter';
			case 'ext_fields':
				return array(
					'timestamp_time' => 'UNIX_TIMESTAMP(`a`.`timestamp`)',
					'time_added' => 'UNIX_TIMESTAMP(`a`.`date_added`)',
					'time_sent' => 'UNIX_TIMESTAMP(`a`.`date_sent`)',
				);
				break;
			default:
				return parent::getOptionList($table_name);
			}
			if ($table != '')
			{
				$query = $DB->query("SELECT " . $key . ", " . $value . " FROM " . $table);
				while ($row = $DB->fetch_array($query, MYSQL_NUM)) $option_list[$row[0]] = $row[1];
				$DB->free_result($query);
			}
			return $option_list;
		}

		/**
		 * Get list of members for an item.
		 * @param array $item
		 * @return array
		 */
		function generateMemberList($item)
		{
			global $DB,$_GLOBALS;
			$member_list = array();
			$first = true;
			$criteria = array();
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
					$query = $DB->query("SELECT `ml`.`member_id` FROM `highwaygroup`.`new_members_mailing_list` AS `ml` INNER JOIN `highwaygroup`.`new_members` AS `m` ON `m`.`member_id` = `ml`.`member_id` WHERE `m`.`status` != 'not_active' AND `ml`.`active` = 'y' AND `ml`.`style_id` = " . intval($v));
					while($row = $DB->fetch_assoc($query)) $temp[$row['member_id']] = $row['member_id'];
					$DB->free_result($query);
					break;
				case 'sales':
					$criteria = $_GLOBALS['criteria_options']['invoice'];
					$criteria['invoice_status'] = array("processed");
					$criteria['site_id'] = $v;
					$temp = array_keys(invoiceGroupCount(invoiceSearch($criteria, strtotime("-1 year")), "member_id"));
					break;
				case 'agents':
					$criteria = $_GLOBALS['criteria_options']['member'];
					$criteria['status'] = "active";
					$criteria['status'] = "active";
					$criteria['customer_type'] = 'agent';
					$temp = memberSearch($criteria);
					break;
				case 'distributor':
					$criteria = $_GLOBALS['criteria_options']['member'];
					$criteria['status'] = "active";
					$criteria['status'] = "active";
					$criteria['customer_type'] = 'distributor';
					$temp = memberSearch($criteria);
					break;
				case 'distagents':
					$criteria = $_GLOBALS['criteria_options']['member'];
					$criteria['status'] = "active";
					$criteria['status'] = "active";
					$criteria['customer_type'] = ['distributor','agents'];
					$temp = memberSearch($criteria);

					break;
					break;
				}
				if($first) $member_list = $temp;
				else $member_list = array_intersect($member_list, $temp);
				$first = false;
			}
			$criteria = array();
			foreach(array('country', 'address_type') as $k) if ($item[$k] != '') $criteria[$k] = explode(',', $item[$k]);
			if (is_array($criteria['address_type']) && in_array('oth', $criteria['address_type'])) $criteria['address_type'][] = '';
			$criteria['country'][] = '';
			if (count($criteria)) $member_list = array_intersect($member_list, memberSearch($criteria));
			return $member_list;
		}

		/**
		 * Get styles from the database.
		 * If input is an array, output is an array of rows as arrays
		 * from table 'newsletter_styles'; else output a single row as an array.
		 * @param mixed $style_id
		 * @return array
		 */
		function styleFromDatabase($style_id)
		{
			global $DB;
			$results = array();
			if (is_array($style_id))
			{
				$query = $DB->query("SELECT * FROM `highwaygroup`.`newsletter_styles` WHERE `style_id`" . ((count($style_id)) ? (" IN(" . implode(', ', $style_id) . ")") : ""));
				while($row = $DB->fetch_assoc($query)) $results[$row['style_id']] = $row;
				$DB->free_result($query);
			}
			else $results = $DB->query_first("SELECT * FROM `highwaygroup`.`newsletter_styles` WHERE `style_id` = " . intval($style_id) . " LIMIT 1", MYSQL_ASSOC);
			return $results;
		}

		/**
		 * Replace rows from newletter_emails with new list.
		 * Remove rows from newletter_emails with provided id;
		 * Add provided list of rows to database.
		 * @param mixed $newsletter_id
		 * @param array $mailing_list
		 */
		function mailinglistToDatabase($newsletter_id, $mailing_list)
		{
			global $DB;
			$DB->query("DELETE FROM `highwaygroup`.`newsletter_emails` WHERE `newsletter_id` = " . intval($newsletter_id));
			foreach($mailing_list as $row) $DB->query("INSERT INTO `highwaygroup`.`newsletter_emails` (`newsletter_id`, `member_id`, `email_status`) VALUES (" . intval($newsletter_id) . ", " . intval($row['member_id']) . ", " . formatSQLString($row['email_status']) . ")");
		}

		/**
		 * Return list of rows with provided newsletter id.
		 * Return list and update email status for customers.
		 * @param mixed $newsletter_id
		 * @return array
		 */
		function mailinglistFromDatabase($newsletter_id)
		{
			global $DB;
			$results = array();
			$query = $DB->query("SELECT `id`, `newsletter_id`, `member_id`, `email_status` FROM `highwaygroup`.`newsletter_emails` WHERE `newsletter_id` = " . intval($newsletter_id));
			while ($row = $DB->fetch_assoc($query)) $results[$row['id']] = $row;
			$DB->free_result($query);

			//Hack to update ordered customers
			foreach(invoiceGetValue(invoiceSearch(array('newsletter_id' => $newsletter_id)), 'member_id') as $member_id) foreach($results as $k => $v) if($v['member_id'] == $member_id && $v['email_status'] != 'ordered')
			{
				$results[$k]['email_status'] = 'ordered';
				$DB->query("UPDATE `highwaygroup`.`newsletter_emails` SET `email_status` = 'ordered' WHERE `id` = " . intval($v['id']) . " LIMIT 1");
				break;
			}

			return $results;
		}

		/**
		 * Works out what in $content can be removed.
		 * @param string $content
		 * @param array $vars
		 * @return string
		 */
		function negotiateContent($content, $vars)
		{
			while(($pos1 = strripos($content, '<negotiate_list')) !== false && ($pos2 = stripos($content, '</negotiate_list>', $pos1)) !== false)
			{
				$struct = substr($content, $pos1, 17+$pos2-$pos1);
				$result = '';

				/**
				 * Find what field we are negotiating
				 */
				$match_tag = array();
				if(preg_match('/^<negotiate_list([^>]*)>/i', $struct, $match_tag))
				{
					$field_name = '';
					$match_attr = array();
					preg_match_all('/([a-z]+)="([^"]*)"/i', $match_tag[1], $match_attr);
					if (is_array($match_attr[1])) foreach($match_attr[1] as $k => $v) if(strtolower($v) == 'field') $field_name = $match_attr[2][$k];

					/**
					 * Choose the negotiated content
					 */
					if ($field_name != '') $result = $this->negotiateAlternatives(substr($struct, strlen($match_tag[0]), -17), $vars[$field_name]);

				}
				/**
				 * Replace the structure with the resulting content
				 */
				$content = substr($content, 0, $pos1) . $result . substr($content, $pos2 + 17);
			}
			return $content;
		}

		/**
		 * Handles the removal and replacement of the contents of input.
		 * @param string $content
		 * @param mixed $val
		 * @return string
		 */
		function negotiateAlternatives($content, $val)
		{
			$default = '';
			while(($pos1 = strripos($content, '<negotiate_item')) !== false && ($pos2 = stripos($content, '</negotiate_item>', $pos1)) !== false)
			{
				$struct = substr($content, $pos1, 17+$pos2-$pos1);

				/**
				 * Test this structure against our value
				 */
				$field_value = '';
				$match_tag = array();
				if(preg_match('/^<negotiate_item([^>]*)>/i', $struct, $match_tag))
				{
					$match_attr = array();
					preg_match_all('/([a-z]+)="([^"]*)"/i', $match_tag[1], $match_attr);
					if (is_array($match_attr[1])) foreach($match_attr[1] as $k => $v)
					{
						if(strtolower($v) == 'value' && $match_attr[2][$k] == $val) return substr($struct, strlen($match_tag[0]), -17);
						if(strtolower($v) == 'default' && strtolower($match_attr[2][$k]) == 'default') $default = substr($struct, strlen($match_tag[0]), -17);
					}
				}

				/**
				 * Remove the structure from the content
				 */
				$content = substr($content, 0, $pos1) . substr($content, $pos2 + 17);
			}
			return $default;
		}

		/**
		 * TODO
		 * @param string $content
		 * @return string
		 */
		function generateAlternateText($content)
		{
			$basename = "http://" . $_SERVER['HTTP_HOST'] . "/";

			//Get base address
			$matches = array();
			if (preg_match('/<base[^>]* href="([^"]+)"[^>]* \\/>/siU', $content, $matches)) $basename = $matches[1];

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
			$content = strtr($content, array("\r\n"=>"\n"));
			$content = strtr($content, array("\r"=>"\n"));
			$content = strtr($content, array("\t"=>" "));
			$content = preg_replace('/  +/', ' ', $content);

			//Remove Duplicate Lines
			$explode = explode("\n", $content);
			foreach($explode as $k => $v) $explode[$k] = trim($v);
			$found = array();
			$prev = '';
			foreach($explode as $k => $v)
			{
				if (($v == '' && $prev == '') || (strpos($v, 'http://') !== false && in_array($v, $found)))
				{
					unset($explode[$k]);
					$v = '';
				}
				else $found[] = $v;
				$prev = $v;
			}
			$content = implode("\n",$explode);

			return wordwrap($content);
		}

		/**
		 * TODO
		 * @param string $content
		 * @param mixed $vars
		 */
		function generateMailMerge($content, $vars = false)
		{
			if (!$vars) return preg_replace('/\\[[A-Z_]{1,32}\\]/', '...', $content);
			$replace = array();
			foreach($vars as $k => $v) if (!is_array($v)) $replace['[' . strtoupper($k) . ']'] = formatHTMLSafe($v); else $replace['[' . strtoupper($k) . ']'] = "";
			return strtr($content, $replace);
		}
		function templateList()
		{
			global $DB;
			$results = array();
			$query = $DB->query("SELECT template_id, template_name, template_subject, template_text FROM highwaygroup.email_templates WHERE FIND_IN_SET('newsletters', template_group)");
			while($row = $DB->fetch_assoc($query)) $results[$row['template_id']] = $row;
			$DB->free_result($query);
			return $results;
		}
		function templateVars($newsletter = array())
		{
			global $DB;
			$id = intval($newsletter['id']);
			$results = array();
			foreach(getTableColumns('`highwaygroup`.`newsletters`') as $k) $results['[' . strtoupper($k) . ']'] = '';
			$results['[REQUEST_DATE]'] = date('jS F, Y', $_SERVER['REQUEST_TIME']);
			$results['[REQUEST_TIME]'] = date('g:i a', $_SERVER['REQUEST_TIME']);
			$results['[HTTP_HOST]'] = array_shift(explode('/', str_replace('http://', '', $website_info['url'])));
			$results['[STAFF_NAME]'] = '';
			foreach($newsletter as $k => $v)
			{
				$results['[' . strtoupper($k) . ']'] = $v;
				$option_list = newsletterGetOptionList($k);
				if (count($option_list) > 0) $results['[' . strtoupper($k) . ']'] = $option_list[$v];
			}
			foreach($results as $k => $v) $results[$k] = str_replace("\r", "", $v);
			return $results;
		}
	}
}

if(!defined('QUERY_NEWSLETTER')) define('QUERY_NEWSLETTER',90403);
?>