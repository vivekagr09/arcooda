<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class GenericController extends MY_Controller {
	
	public function track_device() {
		print_r($_COOKIE); die;
		echo "Device traking started <br/>";
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		echo $ipAddress;
		echo json_encode($this->GetClientMac());
		var_dump($this->os_x());
		var_dump($this->unix_os());
		var_dump($this->GetClientMac());
		$string=exec('getmac');
		$mac=substr($string, 0, 17); 
		echo $mac;
	}

	function GetClientMac(){

	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';

	    $macCommandString   =   "arp " . $ipaddress . " | awk 'BEGIN{ i=1; } { i++; if(i==3) print $3 }'";

	    $mac = exec($macCommandString);

	    return ['ip' => $ipaddress, 'mac' => $mac];
	}

	public function win_os(){ 
	    ob_start();
	    system('ipconfig-a');
	    $mycom=ob_get_contents(); // Capture the output into a variable
	    ob_clean(); // Clean (erase) the output buffer
	    $findme = "Physical";
	    $pmac = strpos($mycom, $findme); // Find the position of Physical text
	    $mac=substr($mycom,($pmac+36),17); // Get Physical Address

	    return $mac;
   	}

   	public function unix_os(){
	    ob_start();
	    system('ifconfig -a');
	    $mycom = ob_get_contents(); // Capture the output into a variable
	    ob_clean(); // Clean (erase) the output buffer
	    $findme = "Physical";
	    //Find the position of Physical text 
	    $pmac = strpos($mycom, $findme); 
	    $mac = substr($mycom, ($pmac + 37), 18);

	    return $mac;
    }

    public function os_x($value='')
    {
    	//Simple & effective way to get client mac address
		// Turn on output buffering
		ob_start();
		//Get the ipconfig details using system commond
		system('ipconfig /all');

		// Capture the output into a variable

		    $mycom=ob_get_contents();

		// Clean (erase) the output buffer

		    ob_clean();

		$findme = "Physical";
		//Search the "Physical" | Find the position of Physical text
		$pmac = strpos($mycom, $findme);

		// Get Physical Address
		$mac=substr($mycom,($pmac+36),17);
		//Display Mac Address
		echo $mac;
    }
}