<?php
namespace app\models;
use app\cache\CacheService;
moduleOverride('blogcomment',    $_SERVER['DOCUMENT_ROOT'] . '/webadmin/owner/blogs/query_blogcomment.inc.php');
class TwigBloags
{
    public $id;

    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    private $_blogs = null;
    public function __get($field)
    {
        global $DB;
        if (is_null($this->_icon)) {
            moduleLoad('blogcomment');
            $cacheService = new CacheService();
            $this->_blogs = $cacheService->getData('blogs', $this->id);
            //@todo blogFromDatabase($this->id);
             if($this->_blogs == false){    
                $this->_blogs = $DB->query_first(sprintf(
                    "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
                    "`blogs`",
                    "id",
                    $this->id
                ), MYSQLI_ASSOC);
                $cacheService->updateData('blogs', $this->id,$this->_blogs);
            }
        }
        return $this->_blogs[$field] ?? null;
    }

    public function __tostring()
    {
        return '[Blog id:' . $this->id . ']';
    }

    public static function list($list) {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigBloags($v);
        }
        return $list;
    }
    public static function search($criteria = [])
    {
        if (array_key_exists('id', $criteria)) {
            if (is_array($criteria['id'])) {
                return self::list($criteria['id']);
            }
            return self::list([$criteria['id']]);
        }
        die("Not Implemented");
    }
    public function page_height(){
        return $this->page_height;
    }

    public function title()
    {
        return $this->title;
    }
    public function createdat()
    {
        return date('d/m/Y',strtotime($this->created_at));
    }
    public function blog()
    {
        $blog = $this->blog;
        if (empty($blog)) {
            $blog = '';
        }else{

         $blog ='<div id="displayless-'.$this->id.'">'.$blog.'</div><div class="media-heading"><a  dataid="'.$this->id.'" href="'.$this->url().'"  style="font-size:12px;" target="_blank"><i>Read more...</i></a></div>';
        }
        return trim($blog);
    }
     public function keywords()
    {
  
        return ucwords(implode(', ', preg_split('/[,\-\s]+/', $this->blog)));
       
    }
    public function breadcrumbs()
    {
        return [
            ['title' => 'Home', 'url' => '/'],
            ['title' => $this->title(), 'url' => $this->url()],
        ];
    }
     public function description()
    {
        $blog = $this->blog;
        if (empty($blog)) {
            $blog = '';
        }
        return trim($blog);
    }
    public function long_description()
    {
        $blog = $this->long_description;
        if (empty($blog)) {
            $blog = '';
        }
        return trim($blog);
    }
    public static function getProductBlogs($id, $limit = 5, $offset = 0)
    {
        global $DB;

        $result = [];
        //@todo moduleLoad('blogcomment'); blogSort(blogQuery(['products' => (string)$id, 'status' => 'y']), 'created_at')
        if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s WHERE `status` = 'y' AND FIND_IN_SET('%d', `products`) ORDER BY `created_at` DESC LIMIT %d",
            "id",
            "`blogs`",
            $id,
            $limit
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = new TwigBloags($row['id']);
            }
            $DB->free_result($query);
        }

        return $result;
    }
    public function date_long()
    {
        return date(_('jS F, Y'), strtotime($this->created_at));
    }
    public function image()
    {
        return  $this->__get('thumb_url') ?? null;
    }
    public function headerimage(){
        return  $this->__get('header_image') ?? null;
    }
    public function url($productid = 0)
    {
        return sprintf(
            '/owner-news/%s-%d-%d.html',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->title), '-')),
            $productid,
            $this->id
        );
    }
    public function getid(){
        return $this->id;
    }
    public function HTMLBlogCommentArea($blog_id, $reply_id ,$product_id = 0)
    {
        global $_GLOBALS;
        $name = strtoupper(substr($_GLOBALS["member"]["first_name"], 0, 1)) . strtoupper(substr($_GLOBALS["member"]["last_name"], 0, 1)) . " - " . ' <i class="flagicon-'.strtolower($_GLOBALS["member"]["country"]).'"></i>';

        $btn_label = ($reply_id > 0) ? "Replyad" : "Comment";

        $html = "<div class='form-group'>";
        $html .= "<small>Comment as : <b>" . $name . "</b></small>";
        $html .= "<textarea class='form-control' rows='5' name='comment'></textarea>";
        $html .= "</div>";
        $html .= "<button type='button' class='btn btn-primary' onclick='comment(this, {$blog_id}, {$reply_id} , {$product_id})'>{$btn_label}</button>";

        return $html;
    }
    public function HTMLBlogComment($blog_id, $reply_id, $name, $comment, $date,$product_id=0)
    {
        $html = "<div class='comments'>";

        $html .= "<div class='comments-header'>";
        $html .= "<small><b>{$name}</b><span style='color: #777;'> &bull; {$date}</span></small>";
        $html .= "</div>";

        $html .= "<div class='comments-body'>";
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

    public function HTMLBlogComments($blog_id, $reply_id){
        moduleLoad('member'); //memberFromDatabase

        $comments = ($reply_id > 0) ? replyCommentsFromDatabase($reply_id) : blogCommentsFromDatabase($blog_id);

        $html = "<div class='comments-container'>";

        if (count($comments) > 0) {
            

            foreach ($comments as $comment) {
                $member = memberFromDatabase($comment["member_id"]);
                if($member['avatar_image']){
                    $image = '<img src="/avatar/svgavatars/ready-avatars/'.$member['avatar_image'].'" style="vertical-align:top;" width="32" height="32">';
                }else{
                    $image = '<img src="/svg/person-32.svg" style="vertical-align:top;" width="32" height="32">';
                }
                $name = $image.' '.strtoupper(substr($member["first_name"], 0, 1)) . strtoupper(substr($member["last_name"], 0, 1)) . " - " .' <i class="flagicon-'.strtolower($member["country"]).'"></i>';

                if (1 == $comment["staff"]) {
                    // $name = "Staff";
                    $name = '<img src="/img/favicon.ico" alt="logo" style="width:17px;"/>'; 
                }

                $html .= HTMLBlogComment($blog_id, $comment["id"], $name, $comment["comment"], time_ago($comment["created"]),$comment['product_id']);

                // If there is reply
                $html .= HTMLBlogComments($blog_id, $comment["id"]);
            }

           
        }
         $html .= "</div>";
        return $html;
    }
    private function time_ago($timestamp)
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
    public function blogCommentsCount(){
        return blogCommentsCount($this->id);
    }
    // Updated code on 15 aug 2024
    public function blogMemberCommentsCount(){
        return blogMemberCommentsCount();
    }

    public static function memberCommentBlogs($id, $limit = 5, $offset = 0)
    {
        global $DB;

        $result = [];
        //@todo moduleLoad('blogcomment'); blogSort(blogQuery(['products' => (string)$id, 'status' => 'y']), 'created_at')
        if ($query = $DB->query(sprintf("SELECT b.id AS blog_id FROM `blogs` AS b
                                            INNER JOIN `comment` AS c 
                                                ON b.id = c.blog_id
                                            WHERE c.member_id = %d
                                            GROUP BY b.id
                                            ORDER BY b.created_at DESC
                                            LIMIT %d",
                                                $id,
                                                $limit
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = new TwigBloags($row['blog_id']);
            }
            $DB->free_result($query);
        }

        return $result;
    }
    public function HTMLMemberBlogComments($blog_id, $reply_id){
        moduleLoad('member'); //memberFromDatabase

        $comments = ($reply_id > 0) ? replyCommentsFromDatabase($reply_id) : blogMemberCommentsFromDatabase($blog_id);

        $html = "<div class='comments-container'>";

        if (count($comments) > 0) {
            

            foreach ($comments as $comment) {
                $member = memberFromDatabase($comment["member_id"]);

                $name = strtoupper(substr($member["first_name"], 0, 1)) . strtoupper(substr($member["last_name"], 0, 1)) . " - " . $member["country"];

                if (1 == $comment["staff"]) {
                    $name = "Staff";
                }

                $html .= HTMLBlogComment($blog_id, $comment["id"], $name, $comment["comment"], time_ago($comment["created"]));

                // If there is reply
                $html .= HTMLBlogComments($blog_id, $comment["id"]);
            }

           
        }
         $html .= "</div>";
        return $html;
    }

    public function memberBlogComments() {
        return blogMemberCommentsFromDatabase($this->id);
    }
}
