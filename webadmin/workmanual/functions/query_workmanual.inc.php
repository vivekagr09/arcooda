<?php
function workmanualSort($workmanual_list,$sort_by)
{
	global $DB;
	if(!is_array($workmanual_list) || count($workmanual_list)<2)
		return $workmanual_list;

	$fields=array();
	$query=$DB->query("SHOW COLUMNS FROM `highwaygroup`.`workmanuals`");
	while($row=$DB->fetch_array($query,MYSQLI_NUM))
		$fields[$row[0]]=$row;
	$DB->free_result($query);

	$temp_list=array();
	if(isset($fields[$sort_by]))
	{
		$query=$DB->query("SELECT w.manual_id FROM `highwaygroup`.`workmanuals` w WHERE w.manual_id IN(".implode(',',$workmanual_list).") ORDER BY `".$sort_by."` ASC");
		while($row=$DB->fetch_assoc($query))
			$temp_list[]=$row['manual_id'];
		$DB->free_result($query);
	}

	if(count($temp_list)>0) $workmanual_list=$temp_list;
	return $workmanual_list;
}

//-----------------------------------------------------------------------------

function workmanualSearch($criteria='')
{
	global $DB;
	$results=array();

	$sql=" SELECT w.manual_id FROM `highwaygroup`.`workmanuals` w";
	//Etc
	$sql.=" GROUP BY w.manual_id";
	$sql.=" ORDER BY w.manual_id ASC";

	$query=$DB->query($sql);
	while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
		$results[]=$row['manual_id'];
	$DB->free_result($query);

	return $results;
}

function workmanualChapterSearch($criteria='')
{
	global $DB;
	$results=array();

	$sql=" SELECT wc.chapter_id FROM `highwaygroup`.`workmanual_chapters` wc";
	$whereAND=array();
	if($criteria['manual_id']!='') $whereAND[]="wc.manual_id=".intval($criteria['manual_id']);
	if(count($whereAND)>0) $sql.=" WHERE ".implode(" AND ",$whereAND);
	$sql.=" GROUP BY wc.chapter_id";
	$sql.=" ORDER BY wc.chapter_rank ASC";

	$query=$DB->query($sql);
	while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
		$results[]=$row['chapter_id'];
	$DB->free_result($query);

	return $results;
}

function workmanualPageSearch($criteria='')
{
	global $DB;
	$results=array();

	$sql=" SELECT wp.page_id FROM `highwaygroup`.`workmanual_pages` wp";
	$whereAND=array();
	if($criteria['chapter_id']!='') $whereAND[]="wp.chapter_id=".intval($criteria['chapter_id']);
	if(count($whereAND)>0) $sql.=" WHERE ".implode(" AND ",$whereAND);
	$sql.=" GROUP BY wp.page_id";
	$sql.=" ORDER BY wp.page_rank ASC";

	$query=$DB->query($sql);
	while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
		$results[]=$row['page_id'];
	$DB->free_result($query);

	return $results;
}

//-----------------------------------------------------------------------------

function workmanualExists($manual_id)
{
	global $DB;
	$row=$DB->query_first("SELECT manual_id FROM `highwaygroup`.`workmanuals` WHERE manual_id=".intval($manual_id));
	if($row) return true;
	return false;
}

function workmanualChapterExists($chapter_id)
{
	global $DB;
	$row=$DB->query_first("SELECT chapter_id FROM `highwaygroup`.`workmanual_chapters` WHERE chapter_id=".intval($chapter_id));
	if($row) return true;
	return false;
}

function workmanualPageExists($page_id)
{
	global $DB;
	$row=$DB->query_first("SELECT page_id FROM `highwaygroup`.`workmanual_pages` WHERE page_id=".intval($page_id));
	if($row) return true;
	return false;
}

//-----------------------------------------------------------------------------

function workmanualFromDatabase($manual_id)
{
	global $DB;
	$summary=array();
	if(workmanualExists($manual_id))
		$summary=$DB->query_first("SELECT w.*, UNIX_TIMESTAMP(w.create_date) AS create_time, UNIX_TIMESTAMP(w.change_date) AS change_time FROM `highwaygroup`.`workmanuals` w WHERE w.manual_id=".intval($manual_id)." LIMIT 1",MYSQLI_ASSOC);
	else
	{
		$query=$DB->query("SHOW COLUMNS FROM `highwaygroup`.`workmanuals`");
		while($row=$DB->fetch_array($query,MYSQLI_NUM))
			$summary[$row[0]]=$row[4];
		$DB->free_result($query);
	}
	return $summary;
}

function workmanualChapterFromDatabase($chapter_id)
{
	global $DB;
	$summary=array();
	if(workmanualChapterExists($chapter_id))
		$summary=$DB->query_first("SELECT wc.*, UNIX_TIMESTAMP(wc.create_date) AS create_time, UNIX_TIMESTAMP(wc.change_date) AS change_time FROM `highwaygroup`.`workmanual_chapters` wc WHERE wc.chapter_id=".intval($chapter_id)." LIMIT 1",MYSQLI_ASSOC);
	else
	{
		$query=$DB->query("SHOW COLUMNS FROM `highwaygroup`.`workmanual_chapters`");
		while($row=$DB->fetch_array($query,MYSQLI_NUM))
			$summary[$row[0]]=$row[4];
		$DB->free_result($query);
	}
	return $summary;
}

function workmanualPageFromDatabase($page_id)
{
	global $DB;
	$summary=array();
	if(workmanualPageExists($page_id))
	{
		$summary=$DB->query_first("SELECT wp.*, UNIX_TIMESTAMP(wp.create_date) AS create_time, UNIX_TIMESTAMP(wp.change_date) AS change_time FROM `highwaygroup`.`workmanual_pages` wp WHERE wp.page_id=".intval($page_id)." LIMIT 1",MYSQLI_ASSOC);
		if($summary['page_type']=='')
		{
			$summary['page_type']='semihtml';
			if($summary['image_type']!='')
				$summary['page_type']=reset(explode('/',$summary['image_type']));
		}
	}
	else
	{
		$query=$DB->query("SHOW COLUMNS FROM `highwaygroup`.`workmanual_pages`");
		while($row=$DB->fetch_array($query,MYSQLI_NUM))
			$summary[$row[0]]=$row[4];
		$DB->free_result($query);
	}
	return $summary;
}

//-----------------------------------------------------------------------------

function workmanualDelete($manual_id)
{
	global $DB;
	$chapter_list=array();
	$query=$DB->query("SELECT chapter_id FROM `highwaygroup`.`workmanual_chapters` WHERE manual_id=".intval($manual_id));
	while($row=$DB->fetch_assoc($query))
		$chapter_list[]=$row['chapter_id'];
	if(count($chapter_list)>0) return false;
	$DB->query("DELETE FROM `highwaygroup`.`workmanuals` WHERE manual_id=".intval($manual_id)." LIMIT 1");
	return true;
}

function workmanualChapterDelete($chapter_id)
{
	global $DB;
	$page_list=array();
	$query=$DB->query("SELECT page_id FROM `highwaygroup`.`workmanual_pages` WHERE chapter_id=".intval($chapter_id));
	while($row=$DB->fetch_assoc($query))
		$page_list[]=$row['page_id'];
	if(count($page_list)>1) return false;
	$DB->query("DELETE FROM `highwaygroup`.`workmanual_pages` WHERE chapter_id=".intval($chapter_id)." LIMIT 1");
	$DB->query("DELETE FROM `highwaygroup`.`workmanual_chapters` WHERE chapter_id=".intval($chapter_id)." LIMIT 1");
	return true;
}

function workmanualPageDelete($page_id)
{
	global $DB;
	$DB->query("DELETE FROM `highwaygroup`.`workmanual_pages` WHERE page_id=".intval($page_id)." LIMIT 1");
	return true;
}

//-----------------------------------------------------------------------------

function workmanualFromPost($posted)
{
	global $_GLOBALS;

	$manual_id=$posted['manual_id'];
	$result=workmanualFromDatabase($manual_id);
	$result['manual_id']=intval($manual_id);
	foreach($result as $key=>$value)
		if(array_key_exists($key,$posted) && !is_array($posted[$key]))
			$result[$key]=trim($posted[$key]);
	if($manual_id<=0)
	{
		unset($result['manual_id']);
		$result['create_date']=formatDateTime();
		$result['create_time']=time();
		$result['create_staff']=$_GLOBALS['staff']['id'];
	}
	$result['change_date']=formatDateTime();
	$result['change_time']=time();
	$result['change_staff']=$_GLOBALS['staff']['id'];
	return $result;
}

function workmanualChapterFromPost($posted)
{
	global $_GLOBALS;
	global $DB;
	$chapter_id=$posted['chapter_id'];
	$result=workmanualChapterFromDatabase($chapter_id);
	$result['chapter_id']=intval($chapter_id);
	foreach($result as $key=>$value)
		if(array_key_exists($key,$posted) && !is_array($posted[$key]))
			$result[$key]=trim($posted[$key]);
	if($chapter_id<=0)
	{
		unset($result['chapter_id']);
		$result['create_date']=formatDateTime();
		$result['create_time']=time();
		$result['create_staff']=$_GLOBALS['staff']['id'];
	}
	$result['change_date']=formatDateTime();
	$result['change_time']=time();
	$result['change_staff']=$_GLOBALS['staff']['id'];
	return $result;
}

function workmanualPageFromPost($posted)
{
	global $_GLOBALS;
	global $DB;
	$page_id=$posted['page_id'];
	$result=workmanualPageFromDatabase($page_id);
	$result['page_id']=intval($page_id);
	foreach($result as $key=>$value)
		if(array_key_exists($key,$posted) && !is_array($posted[$key]))
			$result[$key]=trim($posted[$key]);
	if($page_id<=0)
	{
		unset($result['page_id']);
		$result['create_date']=formatDateTime();
		$result['create_time']=time();
		$result['create_staff']=$_GLOBALS['staff']['id'];
	}
	$result['change_date']=formatDateTime();
	$result['change_time']=time();
	$result['change_staff']=$_GLOBALS['staff']['id'];
	return $result;
}

//-----------------------------------------------------------------------------

function workmanualToDatabase($new_workmanual)
{
	global $DB;
	$manual_id=intval($new_workmanual['manual_id']);
	$old_workmanual=workmanualFromDatabase($manual_id);
	$changes=$new_workmanual;
	foreach($old_workmanual as $key=>$value)
		if($changes[$key]==$value)
			unset($changes[$key]);
	foreach($changes as $key=>$value)
		if(!array_key_exists($key,$old_workmanual))
			unset($changes[$key]);
	$fields=array();
	$query=$DB->query("SHOW COLUMNS FROM `highwaygroup`.`workmanuals`");
	while($row=$DB->fetch_array($query,MYSQLI_NUM))
		$fields[$row[0]]=$row;
	$DB->free_result($query);
	$update_vals=array();
	$insert_vals=array();
	foreach($changes as $key=>$value)
		if(array_key_exists($key,$fields))
		{
			if(isset($changes[$key]))
			{
				$update_vals[$key]="`".$key."`=".$DB->escape($value);
				$insert_vals[$key]=$DB->escape($value);
			}
			else
			{
				$update_vals[$key]="`".$key."`=NULL";
				$insert_vals[$key]="NULL";
			}
		}
	if(workmanualExists($manual_id))
	{
		if(count($update_vals)>0)
			$DB->query("UPDATE `highwaygroup`.`workmanuals` SET ".implode(', ',$update_vals)." WHERE manual_id=".$manual_id." LIMIT 1");
	}
	else
	{
		if(count($insert_vals)>0)
			$DB->query("INSERT INTO `highwaygroup`.`workmanuals` (".implode(', ',array_keys($insert_vals)).") VALUES (".implode(', ',array_values($insert_vals)).")");
		$manual_id=$DB->insert_id();
	}
	return $manual_id;
}

function workmanualChapterToDatabase($new_chapter)
{
	global $DB;
	$chapter_id=intval($new_chapter['chapter_id']);
	$old_chapter=workmanualChapterFromDatabase($chapter_id);
	$changes=$new_chapter;
	foreach($old_chapter as $key=>$value)
		if($changes[$key]==$value)
			unset($changes[$key]);
	foreach($changes as $key=>$value)
		if(!array_key_exists($key,$old_chapter))
			unset($changes[$key]);
	$fields=array();
	$query=$DB->query("SHOW COLUMNS FROM `highwaygroup`.`workmanual_chapters`");
	while($row=$DB->fetch_array($query,MYSQLI_NUM))
		$fields[$row[0]]=$row;
	$DB->free_result($query);
	$update_vals=array();
	$insert_vals=array();
	foreach($changes as $key=>$value)
		if(array_key_exists($key,$fields))
		{
			if(isset($changes[$key]))
			{
				$update_vals[$key]="`".$key."`=".$DB->escape($value);
				$insert_vals[$key]=$DB->escape($value);
			}
			else
			{
				$update_vals[$key]="`".$key."`=NULL";
				$insert_vals[$key]="NULL";
			}
		}
	if(workmanualChapterExists($chapter_id))
	{
		if(count($update_vals)>0)
			$DB->query("UPDATE `highwaygroup`.`workmanual_chapters` SET ".implode(', ',$update_vals)." WHERE chapter_id=".$chapter_id." LIMIT 1");
	}
	else
	{
		if(count($insert_vals)>0)
			$DB->query("INSERT INTO `highwaygroup`.`workmanual_chapters` (".implode(', ',array_keys($insert_vals)).") VALUES (".implode(', ',array_values($insert_vals)).")");
		$chapter_id=$DB->insert_id();
	}
	return $chapter_id;
}

function workmanualPageToDatabase($new_page)
{
	global $DB;
	$page_id=intval($new_page['page_id']);
	$old_page=workmanualPageFromDatabase($page_id);
	$changes=$new_page;
	foreach($old_page as $key=>$value)
		if($changes[$key]==$value)
			unset($changes[$key]);
	foreach($changes as $key=>$value)
		if(!array_key_exists($key,$old_page))
			unset($changes[$key]);
	$fields=array();
	$query=$DB->query("SHOW COLUMNS FROM `highwaygroup`.`workmanual_pages`");
	while($row=$DB->fetch_array($query,MYSQLI_NUM))
		$fields[$row[0]]=$row;
	$DB->free_result($query);
	$update_vals=array();
	$insert_vals=array();
	foreach($changes as $key=>$value)
		if(array_key_exists($key,$fields))
		{
			if(isset($changes[$key]))
			{
				$update_vals[$key]="`".$key."`=".$DB->escape($value);
				$insert_vals[$key]=$DB->escape($value);
			}
			else
			{
				$update_vals[$key]="`".$key."`=NULL";
				$insert_vals[$key]="NULL";
			}
		}
	if(workmanualPageExists($page_id))
	{
		if(count($update_vals)>0)
			$DB->query("UPDATE `highwaygroup`.`workmanual_pages` SET ".implode(', ',$update_vals)." WHERE page_id=".$page_id." LIMIT 1");
	}
	else
	{
		if(count($insert_vals)>0)
			$DB->query("INSERT INTO `highwaygroup`.`workmanual_pages` (".implode(', ',array_keys($insert_vals)).") VALUES (".implode(', ',array_values($insert_vals)).")");
		$page_id=$DB->insert_id();
	}
	return $page_id;
}

if (!function_exists('workmanualChunk')) {
    function workmanualChunk($where = '1', $group_by = '')
    {
        global $DB;

		/*
		if (is_array($where)) {
			$where = workmanualQuery($where);
		}
		*/

		$field_types = getTableFields("`workmanuals`");

		$fields = array();
		foreach ($field_types as $k => $v) {
			$fields[$k] = '`' . $k . '`';
		}

		$field_list = array('`manual_id`');
        foreach ((array)$group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field];
            }
        }
        $field_list = array_unique($field_list);

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM `workmanuals` WHERE %s",
            implode(', ', $field_list),
			$where
        ))) {
			while ($row = $DB->fetch_assoc($query)) {
				$pointers = array(&$result);
				foreach ((array)$group_by as $group) {
					$key = $row[$group];
					if ('' == $key) {
						$key = null;
					}
					$new_pointers = array();
					foreach ((substr($field_types[$group]['Type'], 0, 4) == 'set(') ? explode(',', $key) : array($key) as $key2) {
						foreach ($pointers as $key1 => $pointer) {
							if (!is_array($pointers[$key1][$key2])) {
								$pointers[$key1][$key2] = array();
							}
							$new_pointers[] = &$pointers[$key1][$key2];
						}
					}
					$pointers = $new_pointers;
				}
				foreach ($pointers as $pointer_key => $pointer) {
					$pointers[$pointer_key][] = $row['manual_id'];
				}
			}
			$DB->free_result($query);
		}

        return $result;
    }
}
