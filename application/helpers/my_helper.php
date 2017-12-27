<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_notification_details')){
 function get_notification_details(){
       //get main CodeIgniter object
   $ci =& get_instance();

       //load databse library
   $ci->load->database();

       //get data from database
   $query = $ci->db->get_where('Notification',array('view_flag'=> 0));

   if($query->num_rows() > 0){
     $result = $query->result();
     return $result;
   }else{
     return false;
   }
 }
}

function MakeThumb($source_path, $target_path, $width, $height)
{
  $CI =& get_instance();

  $CI->load->library('image_lib');

  $config['image_library']    = 'gd2';
  $config['source_image']     = $source_path;
  $config['new_image']        = $target_path;
  $config['quality']          = '100%';
  $config['maintain_ratio']   = TRUE;
  $config['create_thumb']     = FALSE;
  $config['overwrite']        = TRUE;
  $config['height']           = $height;
  $config['width']            = $width;

  // $CI->load->library('image_lib', $config);
  // $CI->load->library('image_lib');
  $CI->image_lib->initialize($config);

  if (!$CI->image_lib->resize())
  {
    echo $CI->image_lib->display_errors();
  }
  $CI->image_lib->resize();
  $CI->image_lib->clear();
}

// $time is convert into STRTOTIME
// e.g : $time = TimeCalculate(strtotime($Time['Entry_Date']));
function TimeCalculate($time)
{

    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
      31536000 => 'year',
      2592000 => 'month',
      604800 => 'week',
      86400 => 'day',
      3600 => 'hour',
      60 => 'minute',
      1 => 'second'
    );

  foreach ($tokens as $unit => $text) {
    if ($time < $unit) continue;
    $numberOfUnits = floor($time / $unit);
    if($numberOfUnits == 1){
      $numberOfUnits = 'few';
    }
    return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
  }

}

