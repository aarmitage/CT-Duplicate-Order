<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Cart Throb Repeat Order Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Andrew Armitage and Matt Shearing
 * @link		http://www.armitageonline.co.uk
 */

$plugin_info = array(
	'pi_name'		=> 'CT Repeat Order',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Andrew Armitage and Matt Shearing',
	'pi_author_url'	=> 'http://www.armitageonline.co.uk',
	'pi_description'=> 'Gets product options from previous orders in the the exp_cartthrob_order_items table to be able to submit repeat orders.',
	'pi_usage'		=>  Repeat_order::usage()
);

class Repeat_order {
    
	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		//grab vars from our template
		$order_id = $this->EE->TMPL->fetch_param('order_id');
		$row_order = $this->EE->TMPL->fetch_param('row_order');
				
		//need to query the order_items table
		$order_data = ee()->db->select()
					->from('cartthrob_order_items')
					->where(array('order_id' => $order_id))
					->get();
		
		//we only need to continue if we've got a result from the DB (which we should have, but just to be sure)
		if ($order_data->num_rows() > 0)
		{				
			//we want to loop through the query on per row basis to return all the values in the array
			foreach ($order_data->result_array() as $row)
			//extra field is serialised and base64 encoded
			$item_options[] = array('row_order' => $row['row_order'], 'product_options' => unserialize(base64_decode($row['extra'])));			
			{				
				$results[] = array (
					'order_id' => $row['order_id'],
					$item_options
				);
			}
			//loop through the count of line items within each order
			for ($i = 0; $i < $order_data->num_rows(); $i++){
				//target our product options array
				$options[$i] = ($results[0][0][$i]['product_options']);
				//assign the key/value pairs to variables
				foreach ($options[$i] as $key => $value) {
					if ($row_order == $i) {
						//we don't want the discount field to be repeated, so remove it
						if ($key != 'discount'){
							//assign to EE template vars
							$vars[] = array('repeat_order_key' => $key, 'repeat_order_value' => $value);
						}
					}
				}
			}
			
			//return tag pair
			$this->return_data = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $vars);
			return;
		
		
		}
		//we've can't find a result in the order options table
		else {
			$this->return_data = 'No options could be found for this order.';
			return;
		}


	}//end __construct()
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>

This plugin queries the exp_cartthrob_order_items table in the EE database, specifically targeting the 'extra' field. This stores all the order options within a serialized and Base64 encoded array. Each line item is extracted as an array and the key/value pairs can be used in hidden form fields within a {exp:cartthrob:multi_add_to_cart_form} tag pair.

<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.repeat_order.php */
/* Location: /system/expressionengine/third_party/repeat_order/pi.repeat_order.php */