<?php
namespace app\models;
use app\cache\CacheService;

class Twig360View
{
    public $id;

    public function __construct($id = 0)
    {

        $this->id = $id;
    }
    private $_view = null;
    public function __get($field)
    {
        if (is_null($this->_view)) {
            moduleLoad('product');
            $cacheService = new CacheService();
            $this->_view = $cacheService->getData('360_view', $this->id);
            if($this->_view == false){ 
                $this->_view = $this->view360GenericsFromDatabase($this->id,'`360_view`','id','id');
                $cacheService->updateData('360_view', $this->id,$this->_view);
            }
            $this->_view=$this->_view[$this->id];
        }
        return $this->_view[$field] ?? null;
    }
    public  function get360SideImages(){
          if($this->__get('side_images')){
                    global $DB;
                    $images=$DB->query_first(sprintf(
                        "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
                        "`360_side_images`",
                        "id",
                        $this->__get('side_images')
                    ), MYSQLI_ASSOC);
                    $imagedtata=[];
                    $imagedata['image_left']=basename($images['img_left']);
                    $imagedata['image_right']=basename($images['img_right']);
                    return $imagedata;
            }else{
                return [];
            }
    }
    public function get360setting(){
        return $this->view360GenericFromDatabase(
            1,
            "`360_view_setting`"
        );
    }
    public function get360marker(){

        return $this->view360getActiveMarker(
            $this->__get('id'),
            "`360_marker`",
            "id",
            "view_id",
            "ORDER BY `id` ASC"
        );
     
    }
    public function view360MarkerLocation($marker_id, $image_id) {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d AND `image_id` = %d LIMIT 1",
            "`360_view_marker_location`",
            "marker_id",
            $marker_id,
            $image_id
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return 0;
    }
    public function get360View(){
        
            return $this->view360GenericsFromDatabase(
                $this->__get('id'),
                "`360_images`",
                "id",
                "view_id",
                "ORDER BY `rank` ASC"
            );
      
    }
    public function getvideoid($link){
        $video_id = explode("?v=", $link); 
        if (empty($video_id[1]))
        $video_id = explode("/v/", $link); 
        $video_id = explode("&", $video_id[1]); // Deleting any other params
        $video_id = $video_id[0];
        return $video_id;
    }
     public function removedoublecode($data){
        return str_replace('""', '', $data);
    }
    private function  view360GenericsFromDatabase($id, $table, $primary_key = 'id', $secondary_key = 'product_id',$orderby='') {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
            $table,
            $secondary_key,
            $id,
            $orderby
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
                $result[] = $row;
            } else {
                $result[$row[$primary_key]] = $row;
            }
        }
        $DB->free_result($query);
        return $result;
    }
    private   function view360GenericFromDatabase($id, $table, $primary_key = 'id') {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
    }
    private  function view360getActiveMarker($id, $table, $primary_key = 'id', $secondary_key = 'product_id',$orderby='')
    {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND status=1 %s",
            $table,
            $secondary_key,
            $id,
            $orderby
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
                $result[] = $row;
            } else {
                $result[$row[$primary_key]] = $row;
            }
        }
        $DB->free_result($query);
        return $result;

    } 
}
