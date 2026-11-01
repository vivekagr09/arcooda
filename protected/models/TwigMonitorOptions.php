<?php
namespace app\models;

class TwigMonitorOptions
{
    /**
     * @var int $id
     */
    public $product_id;

    /**
     * @param int $id
     */
    public function __construct($product_id = 0)
    {
        $this->product_id = $product_id;
    }

    /**
     * @var mixed[]|null $banner
     */
    private $monitorOptions = null;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (is_null($this->monitorOptions)) {
            moduleLoad('product');
            $this->monitorOptions = productMonitorOptionsFromDatabase($this->product_id);
        }
        if (is_array($this->monitorOptions) && array_key_exists($field, $this->monitorOptions)) {
            return $this->monitorOptions[$field];
        }
        return null;
    }

    public function monitor_size()
    {
        return $this->__get('monitor_size');
    }
     public function aspect_ratio_default()
    {
        return $this->__get('aspect_ratio_default');
    }

 	public function aspect_ratio_software()
    {
        return $this->__get('aspect_ratio_software');
    }
    public function monitor_brightness()
    {
        return $this->__get('monitor_brightness');
    }
    public function monitor_contrast_ratio()
    {
        return $this->__get('monitor_contrast_ratio');
    }
    public function monitor_viewing_angle()
    {
        return $this->__get('monitor_viewing_angle');
    }
    public function monitor_display_color()
    {
        return $this->__get('monitor_display_color');
    }
    public function monitor_refresh_rate()
    {
        return $this->__get('monitor_refresh_rate');
    }
    public function monitor_panel_type()
    {
        return $this->__get('monitor_panel_type');
    }
    public function monitor_display_surface()
    {
        return $this->__get('monitor_display_surface');
    }
    public function monitor_power()
    {
        return $this->__get('monitor_power');
    }
    public function monitor_low_resolution_support()
    {
        return $this->__get('monitor_low_resolution_support');
    }
    public function monitor_low_resolution_support_value()
    {
        return $this->__get('monitor_low_resolution_support_value');
    }
    public function monitor_pc_resolution()
    {
        return $this->__get('monitor_pc_resolution');
    }
    public function input_vga()
    {
        return $this->__get('input_vga');
    }
    public function input_dvi()
    {
        return $this->__get('input_dvi');
    }
    public function input_hdmi()
    {
        return $this->__get('input_hdmi');
    }
    public function input_displayport()
    {
        return $this->__get('input_displayport');
    }
    public function input_usb()
    {
        return $this->__get('input_usb');
    }
    public function monitor_vesa_mount()
    {
        return $this->__get('monitor_vesa_mount');
    }
    public function monitor_vesa_mount_value()
    {
        return $this->__get('monitor_vesa_mount_value');
    }
    public function osd_buton_option()
    {
        return $this->__get('osd_buton_option');
    }
    public function rgb_board_option()
    {
        return $this->__get('rgb_board_option');
    }
    public function cable_management_option()
    {
        return $this->__get('cable_management_option');
    }
    public function ddcci_option()
    {
        return $this->__get('ddcci_option');
    }
    public function freesync_Pro_option()
    {
        return $this->__get('freesync_Pro_option');
    }
    public function ultra_hdr_option()
    {
        return $this->__get('ultra_hdr_option');
    }
    public function ultra_vivid_option()
    {
        return $this->__get('ultra_vivid_option');
    }
    public function over_drive_option()
    {
        return $this->__get('over_drive_option');
    }
    public function over_scan_option()
    {
        return $this->__get('over_scan_option');
    }
    public function osd_board_option()
    {
        return $this->__get('osd_board_option');
    }
    public function screen_rotate_option()
    {
        return $this->__get('screen_rotate_option');
    }
    public function screen_mirror_option()
    {
        return $this->__get('screen_mirror_option');
    }
    public function low_resolution_option()
    {
        return $this->__get('low_resolution_option');
    }
    public function low_resolution_analysis_option()
    {
        return $this->__get('low_resolution_analysis_option');
    }
    public function audio_input_jack()
    {
        return $this->__get('audio_input_jack');
    }
    public function audio_output_jack()
    {
        return $this->__get('audio_output_jack');
    }
    public function audio_output_jack_comment()
    {
        return $this->__get('audio_output_jack_comment');
    }
    public function audio_input_jack_comment()
    {
        return $this->__get('audio_input_jack_comment');
    }
    public function best_uses()
    {
        return $this->__get('best_uses');
    }
    public function notes()
    {
        return $this->__get('notes');
    }
    public function monitor_low_resolution_rotate(){
        return $this->__get('monitor_low_resolution_rotate');
    }
    public function screen_rotate_option_value(){
        return $this->__get('screen_rotate_option_value');
    }
    public function mounting_brackets(){
        return $this->__get('mounting_brackets');
    }
    public function mounting_brackets_value(){
        return $this->__get('mounting_brackets_value');
    }
    public function input_usb_type_c(){
        return $this->__get('input_usb_type_c');
    }
    public function convertToInch($value)
    {
        return (bccomp($value, '0.0', 1)) ? (bcmul($value, '0.393701', 2) . ' in'):'0.0 in';
    }
    public function screen_width(){
        return $this->__get('screen_width');
    }
    public function screen_height(){
        return $this->__get('screen_height');
    }
    public function screen_width_brackets(){
        return $this->__get('screen_width_brackets');
    }
    public function screen_height_brackets(){
        return $this->__get('screen_height_brackets');
    }
    public function screen_depth_brackets(){
        return $this->__get('screen_depth_brackets');
    }
    public function panel_link(){
        return $this->__get('panel_link');
    }
    public function model_number(){
        return $this->__get('model_number');
    }
    public function ad_board_model(){
        return $this->__get('ad_board_model');
    }
    public function power_board_model(){
        return $this->__get('power_board_model');
    }
    public function panel_ppi(){
        return $this->__get('panel_ppi');
    }
    public function lagtest_bodnar_top(){
        return $this->__get('lagtest_bodnar_top');
    }
    public function lagtest_bodnar_bottom(){
        return $this->__get('lagtest_bodnar_bottom');
    }
    public function lagtest_bodnar_middle(){
        return $this->__get('lagtest_bodnar_middle');
    }
    public function lagtest_bodnar_date(){
        return $this->__get('lagtest_bodnar_date');
    }
    public function lagtest_sluet_top(){
        return $this->__get('lagtest_sluet_top');
    }
    public function lagtest_sluet_middle(){
        return $this->__get('lagtest_sluet_middle');
    }
    public function lagtest_sluet_bottom(){
        return $this->__get('lagtest_sluet_bottom');
    }
    public function lagtest_sluet_date(){
        return $this->__get('lagtest_sluet_date');
    }
    public function displayport_mst_option(){
        return $this->__get('displayport_mst_option');
    }
    public function screen_clone_option(){
        return $this->__get('screen_clone_option');
    }
    public function pinp_option(){
        return $this->__get('pinp_option');
    }
    public function multi_display(){
        return $this->__get('multi_display');
    }
    public function integrated_speakers(){
        return $this->__get('integrated_speakers');
    }
    
}
