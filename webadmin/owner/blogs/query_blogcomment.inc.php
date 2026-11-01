<?php
if (!defined('BLOG_COMMENT_TABLENAME')) {
    define('BLOG_COMMENT_TABLENAME', '`comment`');
}
if (!defined('BLOG_COMMENT_PRIMARYKEY')) {
    define('BLOG_COMMENT_PRIMARYKEY', 'id');
}
if (!defined('BLOG_COMMENT_SECONDARYKEY')) {
    define('BLOG_COMMENT_SECONDARYKEY', 'blog_id');
}
/**
 * Return array containing row with given id.
 * @param mixed $id
 * @return mixed
 */
function blogFromDatabase($id)
{
    global $DB;

    if ($row = $DB->query_first(sprintf(
        "SELECT *, UNIX_TIMESTAMP(`updated_at`) AS `last_modified_unix` FROM %s WHERE `%s` = %d",
        "`blogs`",
        "id",
        $id
    ), MYSQLI_ASSOC)) {
        return $row;
    }

    return false;
}
if (!function_exists('blogCommentFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function blogCommentFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad($id, BLOG_COMMENT_TABLENAME, BLOG_COMMENT_PRIMARYKEY)) {
            return $row;
        }

        return null;
    }
}

if (!function_exists('blogsFromDatabase')) {
    /**
     * @param mixed[] $criteria
     *
     * @return mixed[][]
     */
    function blogsFromDatabase($criteria = array())
    {
        global $DB;

        $whereAND = [];
        if (array_key_exists('search', $criteria)) {
            $whereOR = [];
            foreach ((array)$criteria["search"] as $k => $v) {
                $whereOR[] = sprintf("`%s` = %s", $k, $DB->escape($v));
            }
            if (count($whereOR) > 1) {
                $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
            } elseif (count($whereOR)) {
                $whereAND[] = implode(" OR ", $whereOR);
            }
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s%s ORDER BY `%s` DESC%s",
            "`blogs`",
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : '',
            'id',
            (($criteria["limit"] ?? 0) > 0) ? sprintf(
                " LIMIT %d, %d",
                $criteria["offset"] ?? 0,
                $criteria["limit"] ?? 0
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
        }

        return $result;
    }
}
if (!function_exists('blogCommentHide')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function blogCommentHide($id)
    {
        global $DB;

        return $DB->modelSet($id, 'visible', 0, BLOG_COMMENT_TABLENAME, BLOG_COMMENT_PRIMARYKEY);
    }
}
if (!function_exists('blogCommentsCount')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function blogCommentsCount($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM %s WHERE `%s` = %d AND `visible` = 1",
            BLOG_COMMENT_TABLENAME,
            BLOG_COMMENT_SECONDARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return $row['total'];
        }

        return 0;
    }
}
if (!function_exists('blogCommentsCountApproval')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function blogCommentsCountApproval($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM %s WHERE `%s` = %d AND `is_approve` = 0",
            BLOG_COMMENT_TABLENAME,
            BLOG_COMMENT_SECONDARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return $row['total'];
        }

        return 0;
    }
}
if (!function_exists('blogCommentsFromDatabase')) {
    /**
     * @param int $blog_id
     *
     * @return mixed[][]
     */
    function blogCommentsFromDatabase($blog_id)
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d AND `reply_comment_id` = 0 AND `visible` = 1 ORDER BY `%s` DESC",
            BLOG_COMMENT_TABLENAME,
            BLOG_COMMENT_SECONDARYKEY,
            $blog_id,
            BLOG_COMMENT_PRIMARYKEY
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('allBlogCommentsFromDatabase')) {
    /**
     * @param int $blog_id
     *
     * @return mixed[][]
     */
    function allBlogCommentsFromDatabase($blog_id)
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d AND `visible` = 1 ORDER BY `%s` DESC",
            BLOG_COMMENT_TABLENAME,
            BLOG_COMMENT_SECONDARYKEY,
            $blog_id,
            BLOG_COMMENT_PRIMARYKEY
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('blogCommentsSearchFromDatabase')) {
    /**
     * @param mixed[] $criteria
     *
     * @return mixed[][]
     */
    function blogCommentsSearchFromDatabase($criteria = array())
    {
        global $DB;

        $whereAND = [];
        if (array_key_exists('search', $criteria)) {
            $whereOR = array();
            foreach ((array)$criteria["search"] as $k => $v) {
                $whereOR[] = "`" . $k . "` = " . $DB->escape($v);
            }
            if (1 == count($whereOR)) {
                $whereAND[] = implode(" OR ", $whereOR);
            } elseif (count($whereOR)) {
                $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
            }
            $whereAND[] = implode(" AND ", $whereOR);
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s %s ORDER BY `id` DESC",
            BLOG_COMMENT_TABLENAME,
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('blogCommentShow')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function blogCommentShow($id)
    {
        global $DB;

        return $DB->modelSet($id, 'visible', 1, BLOG_COMMENT_TABLENAME, BLOG_COMMENT_PRIMARYKEY);
    }
}
if (!function_exists('blogCommentApprove')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function blogCommentApprove($id,$status)
    {
        global $DB;

        return $DB->modelSet($id, 'is_approve', $status, BLOG_COMMENT_TABLENAME, BLOG_COMMENT_PRIMARYKEY);
    }
}
if (!function_exists('blogCommentToDatabase')) {
    /**
     * @param int     $id
     * @param mixed[] $data
     *
     * @return int
     */
    function blogCommentToDatabase($id, &$data)
    {
        global $DB;

        $data[BLOG_COMMENT_SECONDARYKEY] = intval($id);
        $data['created']                 = formatDateTime();
        $data['visible']                 = 1;

        return $DB->modelSave($data, BLOG_COMMENT_TABLENAME, BLOG_COMMENT_PRIMARYKEY);
    }
}

if (!function_exists('blogMemberCommentsCount')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function blogMemberCommentsCount()
    {
        global $DB,$_GLOBALS;

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM %s WHERE `%s` = %d AND `visible` = 1",
            BLOG_COMMENT_TABLENAME,
            'member_id',
            $_GLOBALS['member']['member_id']
        ), MYSQLI_ASSOC)) {
            return $row['total'];
        }

        return 0;
    }
}

if (!function_exists('blogMemberCommentsFromDatabase')) {
    /**
     * @param int $blog_id
     *
     * @return mixed[][]
     */
    function blogMemberCommentsFromDatabase($blog_id)
    {
        global $DB,$_GLOBALS;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d AND `member_id` = %d AND `visible` = 1 ORDER BY `%s` DESC",
            BLOG_COMMENT_TABLENAME,
            BLOG_COMMENT_SECONDARYKEY,
            $blog_id,
            $_GLOBALS['member']['member_id'],
            BLOG_COMMENT_PRIMARYKEY
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('blogCommentSetValue')) {
    function blogCommentSetValue($id, $field, $value)
    {
        global $DB;
        $fields = $DB->table_columns(BLOG_COMMENT_TABLENAME);
        if (array_key_exists($field, $fields)) {
            $DB->query(sprintf(
                "UPDATE %s SET `%s` = %s WHERE `id` = %d LIMIT 1",
                BLOG_COMMENT_TABLENAME,
                $field,
                $DB->escape($value),
                $id
            ));
            return true;
        }
        return false;
    }
}
if (!function_exists('blogCommentDeleteFromDatabase')) {
    /**
     * Remove the blogComment from the database
     * Updates the 'blogComment' table in the database by deleting the
     * the row with id $id
     *
     * @param int $id
     *
     * @return void
     */
    function blogCommentDeleteFromDatabase($id)
    {
        global $DB;

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            BLOG_COMMENT_TABLENAME,
            BLOG_COMMENT_PRIMARYKEY,
            $id
        ));
    }
}

/**
 * @param int $blog_id
 * @param int $reply_id
 *
 * @return string
 */
function HTMLBlogCommentArea($blog_id, $reply_id,$product_id=0)
{
    global $_GLOBALS;

    $name = strtoupper(substr($_GLOBALS["member"]["first_name"], 0, 1)) . strtoupper(substr($_GLOBALS["member"]["last_name"], 0, 1)) . " - " .' <i class="flagicon-'.strtolower($_GLOBALS["member"]["country"]).'"></i>' ;
    $name = (!empty($name) && $name != ' - ') ? $name :'Staff';
    $btn_label = ($reply_id > 0) ? "Reply" : "Comment";

    $html = "<div class='form-group'>";
    $html .= "<small>Comment as : <b>" . $name . "</b></small>";
    $html .= "<textarea class='form-control' rows='5' name='comment'></textarea>";
    $html .= "</div>";
    $html .= "<button type='button' class='btn btn-primary' onclick='comment(this, {$blog_id}, {$reply_id},{$product_id})'>{$btn_label}</button>";

    return $html;
}

/**
 * @param int    $blog_id
 * @param int    $reply_id
 * @param string $name
 * @param string $comment
 * @param string $date
 *
 * @return string
 */
function HTMLBlogComment($blog_id, $reply_id, $name, $comment, $date,$product_id = 0,$id=0)
{
    $comment_colour = '';
    if($reply_id == $id) {
        $comment_colour = 'style="background-color:pink"';
    }
    $html = "<div class='comments'>";

    $html .= "<div class='comments-header' ".$comment_colour.">";
    $html .= "<small><b>{$name}</b><span style='color: #777;'> &bull; {$date}</span></small>";
    $html .= "</div>";

    $html .= "<div class='comments-body' ".$comment_colour.">";
    $html .= htmlspecialchars($comment);
    $html .= "</div>";

    $html .= "<div class='comments-footer'>";
    $html .= "<div class='reply-comment-container'>";
    $html .= "<a href='#' onclick='return openComment(this);'><small>Reply</small></a>";
    $html .= "</div>";

    $html .= "<div class='comment-area' style='margin-top: 10px;'>";
    $html .= HTMLBlogCommentArea($blog_id, $reply_id,$product_id);
    $html .= "</div>";
    $html .= "</div>"; // .comments-footer

    $html .= "</div>"; // .comments

    return $html;
}

/**
 * @param int $blog_id
 * @param int $reply_id
 *
 * @return string
 */
function HTMLBlogComments($blog_id, $reply_id ,$id = 0)
{ 
    moduleLoad('member'); //memberFromDatabase

    $comments = ($reply_id > 0) ? replyCommentsFromDatabase($reply_id) : blogCommentsFromDatabase($blog_id);

    $html = "";
    $html .= "<div class='comments-container'>";
    if (count($comments) > 0) {
        

        foreach ($comments as $comment) {
            $member = memberFromDatabase($comment["member_id"]);
            if($member['avatar_image']){
                $image = '<img src="/avatar/svgavatars/ready-avatars/'.$member['avatar_image'].'" style="vertical-align:top;" width="32" height="32">';
            }else{
                 $image = '<img src="/svg/person-32.svg" style="vertical-align:top;" width="32" height="32">';
            }
            $name = $image.' '.strtoupper(substr($member["first_name"], 0, 1)) . strtoupper(substr($member["last_name"], 0, 1)) . " - " . ' <i class="flagicon-'.strtolower($member["country"]).'"></i>';

            if (1 == $comment["staff"]) {
                // $name = "Staff";
                $name = '<img src="/img/favicon.ico" alt="logo" style="width:17px;"/>'; 
            }

            $html .= HTMLBlogComment($blog_id, $comment["id"], $name, $comment["comment"], time_ago($comment["created"]),$comment['product_id'],$id);

            // If there is reply
            $html .= HTMLBlogComments($blog_id, $comment["id"],$id);
        }

       
    }
     $html .= "</div>";
    return $html;
}

/**
 * @param string $timestamp
 *
 * @return string
 */
function time_ago($timestamp)
{
    $time_ago        = strtotime($timestamp);
    $current_time    = time();
    $time_difference = $current_time - $time_ago;
    $seconds         = $time_difference;

    $minutes = round($seconds / 60); // value 60 is seconds
    $hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec
    $days    = round($seconds / 86400); //86400 = 24 * 60 * 60;
    $weeks   = round($seconds / 604800); // 7*24*60*60;
    $months  = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60
    $years   = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60

    if ($seconds <= 60) {
        return "Just Now";
    } elseif ($minutes <= 60) {
        if (1 == $minutes) {
            return "one minute ago";
        } else {
            return "$minutes minutes ago";
        }
    } elseif ($hours <= 24) {
        if (1 == $hours) {
            return "an hour ago";
        } else {
            return "$hours hrs ago";
        }
    } elseif ($days <= 7) {
        if (1 == $days) {
            return "yesterday";
        } else {
            return "$days days ago";
        }
    } elseif ($weeks <= 4.3) {
        if (1 == $weeks) {
            return "a week ago";
        } else {
            return "$weeks weeks ago";
        }
    } elseif ($months <= 12) {
        if (1 == $months) {
            return "a month ago";
        } else {
            return "$months months ago";
        }
    } else {
        if (1 == $years) {
            return "one year ago";
        } else {
            return "$years years ago";
        }
    }
}

/**
 * @param int $blog_id
 *
 * @return mixed[][]
 */
function answeredBlogCommentsFromDatabase($blog_id)
{
    global $DB;

    $result = [];
    if ($query = $DB->query(sprintf(
        "SELECT * FROM %s WHERE `%s` = %d AND `reply_comment_id` > 0 AND `visible` = 1 ORDER BY `%s` DESC",
        BLOG_COMMENT_TABLENAME,
        BLOG_COMMENT_SECONDARYKEY,
        $blog_id,
        BLOG_COMMENT_PRIMARYKEY
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }
        $DB->free_result($query);
    }

    return $result;
}

/**
 * @param int $reply_comment_id
 * @param int $limit
 * @param int $offset
 *
 * @return mixed[][]
 */
function replyCommentsFromDatabase($reply_comment_id, $limit = 0, $offset = 0)
{
    global $DB;

    $result = [];
    if ($query = $DB->query(sprintf(
        "SELECT * FROM %s WHERE `reply_comment_id` = %d ORDER BY `%s` DESC%s",
        BLOG_COMMENT_TABLENAME,
        $reply_comment_id,
        BLOG_COMMENT_PRIMARYKEY,
        ($limit > 0) ? sprintf(" LIMIT %d, %d", $offset, $limit) : ''
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }
        $DB->free_result($query);
    }

    return $result;
}

// function blogCommentDeleteFromDatabase($id){
//     global $DB;
//        $DB->query(sprintf(
//            "DELETE FROM %s WHERE `id` = %d LIMIT 1",
//            BLOG_COMMENT_TABLENAME,
//            $id
//        ));
// }
