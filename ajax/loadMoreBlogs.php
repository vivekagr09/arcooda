<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('blogcomment');
$from   = max(0, intval($_GET["from"] ?? 0));
$limit  = 5;
$blogs  = blogsFromDatabase(['limit' => $limit, 'offset' => $from]);
$offset = $from;
function url($id,$name)
{
    return sprintf(
        '/blog/%s-%d',
        urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-')),
        $id
    );
}  
if (count($blogs) > 0) {
    foreach ($blogs as $blog) {
        echo "<div class='well well-sm'>";
        echo '<div class="media">';
        echo "<div class='media-left media-top' style='padding-right: 10px;'>";
        if (array_key_exists('thumb_url', $blog)) {
            echo "<img src='/blogimage/" . $blog['thumb_url'] . "?crop=true&width=200&height=200' class='media-object' width='96px' height='96px'>";
        }
        echo "</div>";

        echo "<div class='media-body'>";
        echo "<h4 class='media-heading'><a href='" . url($blog['id'],$blog['title']) . "'>{$blog['title']}</a></h4>";
       
        echo '<div id="displayless-'.$blog["id"].'" >'.$blog['blog'].'</div><div class="media-heading"><a  dataid="'.$blog["id"].'" href="'.url($blog['id'],$blog['title']) .'" style="font-size:12px;"><i>read more</i></a></div>';
        echo "</div>";
        echo "</div>";
        echo "<div class='media-footer' style='text-align: right; border-top: 1px solid #e0e0e0;'>";
        echo "<small>" . date("d/m/Y", strtotime($blog["created_at"])) . "</small>";
        echo "<small style='color: #c0c0c0;'>&nbsp;&bull;&nbsp;</small>";
        echo "<a href='#' onclick='return openComment(this);'><small>Comments: ".blogCommentsCount($blog['id'])."</small></a>";
        echo "</div>";
        echo '<div class="comment-area" style="display: none;">';
        echo HTMLBlogCommentArea($blog['id'],0);
        echo '<div style="border-top: 1px solid #e0e0e0; margin-top: 10px; padding-top: 10px;">';
        echo HTMLBlogComments($blog['id'], 0);
        echo '</div></div>';
        echo "</div>";

        $offset++;
    }

    if (count(blogsFromDatabase(['limit' => $limit, 'offset' => $offset])) > 0) {
        echo "<div class='load-more-blogs' style='text-align: center; margin-bottom: 10px;'>";
        echo "<button type='button' class='btn btn-primary' onclick='loadMoreBlogs(this, {$offset});'>Load More</button>";
        echo "</div>";
    } else {
        echo "<div style='text-align: center; margin-bottom: 10px; color: #777;'>End of blogs</div>";
    }
} else {
    echo "<div style='text-align: center; margin-bottom: 10px; color: #777;'>End of blogs</div>";
}
