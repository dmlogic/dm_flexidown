<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Additional formatting methods for use with DM Flexidown
 */

class Flexi_format {
	
	public function sample_callback($data) {
		
		return '<div class="wrapper">'.$data.'</div>';
		
	}	
}