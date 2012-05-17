<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DM Flexidown
 *
 * Flexible Markdown fieldtype
 *
 * @package		ExpressionEngine
 * @subpackage	Fieldtypes
 * @category	Fieldtypes
 * @author		DM Logic
 * @link		http://dmlogic.com
 * 
 * This code uses php-markdown. Please see license here:
 * http://michelf.com/projects/php-markdown/license/
 */
class Dm_flexidown_ft extends EE_Fieldtype {

	// general Fieldtype vars
	public $info = array(
		'name'		=> 'Flexidown',
		'version'	=> '0.1'
	);

	public $has_array_data = FALSE;
	
	// ------------------------------------------------------------------------
	
	// available formatting types
	private $formatting_types = array(
		'markdown' => 'Markdown',
		'html' => 'HTML',
		'br' => 'Auto &lt;br /&gt;'
	);
	
	// the default height of the textarea entry box
	private $default_height = 10;

	// ------------------------------------------------------------------------

	/**
	 * display_field
	 * 
	 * Display the field in the CMS
	 * 
	 * @param string $data
	 * @return string
	 */
	public function display_field($data) {
		
		$out = '<p>Default formatting for this field is <strong>'.$this->formatting_types[$this->settings['fxd_default_formatting']].'</strong></p>';

		$out .= form_textarea(array(
			'name'	=> $this->field_name,
			'id'	=> $this->field_name,
			'value'	=> $data,
			'rows'	=> $this->settings['fxd_form_control_rows']
		));
		
		return $out;
	}

	// --------------------------------------------------------------------

	/**
	 * replace_tag
	 * 
	 * @param string $data
	 * @param array $params
	 * @param string $tagdata
	 * @return string
	 */
	public function replace_tag($data, $params = '', $tagdata = '') {
		
		$format = 'replace_'.$this->settings['fxd_default_formatting'];
		
		// delegate to the formatting method if it's valid
		if(method_exists($this, $format)) {
			$data = $this->$format($data,$params);
		
		// or stick with the data we have
		} else {
			$data = $this->format_output($data,$params);
		}
		
		return $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * replace_markdown
	 * 
	 * Format data as Markdown
	 * 
	 * @param string $data
	 * @param array $params
	 * @return type 
	 */
	public function replace_markdown($data, $params = '') {
		
		// process the Markdown
		// @todo: Would be nice to have this pre-rendered to save time
		require(PATH_THIRD . '/dm_flexidown/library/markdown.php');
		$data = Markdown($data);
		
		// apply any callback
		return $this->format_output($data,$params);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * replace_html
	 * 
	 * Format as EE HTML
	 * 
	 * @param string $data
	 * @param array $params
	 * @return string
	 */
	public function replace_html($data, $params = '') {
		
		// process the HTML formatting
		// @todo: Would be nice to have this pre-rendered to save time
		$this->EE->load->library('typography');
		$data = $this->EE->typography->auto_typography($data);
		
		// apply any callback
		return $this->format_output($data,$params);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * replace_br
	 * 
	 * Format with line breaks
	 * 
	 * @param string $data
	 * @param array $params
	 * @return string
	 */
	public function replace_br($data, $params = '') {
		
		$data = nl2br($data);
		
		// apply any callback
		return $this->format_output($data,$params);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * replace_raw
	 * 
	 * No formatting at all
	 * 
	 * @param string $data
	 * @param array $param
	 * @return string 
	 */
	public function replace_raw($data, $params = '') {
		
		// apply any callback
		return $this->format_output($data,$params);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Allow formatting of data by a user-defined function
	 * 
	 * @param string $data
	 * @param array $params
	 * @return string
	 */
	private function format_output($data, $params) {
		
		// look for a requested callback
		if(!empty($params) && isset($params['callback'])) {
			
			// instantiate the class
			require_once(PATH_THIRD . '/dm_flexidown/library/flexi_format.php');
			$formatter = new Flexi_format();
			
			// separate multiple callbacks
			$callbacks = explode('|',$params['callback']);
			
			foreach($callbacks as $cb) {
		
				// run our callback method if it exists
				if(method_exists($formatter, $cb)) {
					$data = $formatter->$cb($data);
				}
			}
		}
				
		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * display_settings
	 * 
	 * Display the field settings in the CMS
	 * 
	 * @param array $data 
	 */
	public function display_settings($data) {
		
		$field_rows	= (!empty($data['fxd_form_control_rows'])) ? $data['fxd_form_control_rows'] : $this->default_height;
		$field_formatting = (!empty($data['fxd_default_formatting'])) ? $data['fxd_default_formatting'] : '';

		// how many rows
		$this->EE->table->add_row(
			lang('textarea_rows', 'fxd_form_control_rows'),
			form_input(array('id'=>'fxd_form_control_rows','name'=>'fxd_form_control_rows', 'size'=>4,'value'=>$field_rows))
		);
		
		// default formatting
		$this->EE->table->add_row(
			lang('deft_field_formatting', 'fxd_default_formatting'),
			form_dropdown('fxd_default_formatting', $this->formatting_types, $field_formatting, 'id="fxd_default_formatting"')
		);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * save_settings
	 * 
	 * @return array
	 */
	public function save_settings() {
		
		return array(
			'fxd_form_control_rows' => $this->EE->input->post('fxd_form_control_rows'),
			'fxd_default_formatting'  => $this->EE->input->post('fxd_default_formatting'),
		);
	}
}