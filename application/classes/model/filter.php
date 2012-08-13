<?php defined('SYSPATH') or die('No direct script access');

/**
 * Model for the river_filters table
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Model_Filter extends ORM {

	/**
	 * One-to-many relationship definition
	 * @var array
	 */
	protected $_has_many = array(
		'filter_parameters' => array()
	);

	/**
	 * Auto-update columns for creation
	 * @var string
	 */
	protected $_created_column = array('column' => 'filter_date_add', 'format' => 'Y-m-d H:i:s');

	/**
	 * Gets the parameters for the current filter
	 */
	public function get_parameters()
	{
		return $this->filter_parameters->find_all();
	}

	/**
	 * Returns the parameters for the current filter as a key-value array
	 *
	 * @return array
	 */
	public function get_parameters_array()
	{
		$parameters = $this->get_parameters();

		$params_array = array();
		foreach ($parameters as $param)
		{
			$params_array[] = array(
				"id" => $param->id,
				"type" => $param->parameter_type,
				"value" => $param->parameter
			);
		}

		return $params_array;
	}

	/**
	 * Overrides the default delete behaviour by first
	 * deleting the filter parameters before purging
	 * the parent filter
	 */
	public function delete()
	{
		// Execute pre-delete events
		Swiftriver_Event::run("swiftriver.filter.pre_delete", $this);

		// Delete the filter parameters
		if ($this->filter_parameters->count_all() > 0)
		{
			$this->filter_parameters->delete();
		}
		parent::delete();
	}

	/**
	 * Adds a new or updates an existing filter parameter. If the specified
	 * parameter id is invalid, a new parameter is created
     *
	 * @param  array  $param_data Parameter to be added
	 * @param  int    $parameter_id ID of the parameter to the be updated
	 * @return mixed  Filter_Parameter on success, FALSE otherwise
	 */
	public function update_parameter($param_data, $parameter_id = 0)
	{
		try
		{
			$parameter_orm = $this->filter_parameters
			    ->where('id', '=', $parameter_id)
			    ->find();

			if ( ! $parameter_orm->loaded())
			{				
				$parameter_orm->filter_id = $this->id;
				$parameter_orm->parameter_type = $param_data["type"];
				$parameter_orm->parameter = $param_data["value"];
				$parameter_orm->save();
			}
			else
			{
				$parameter_orm->parameter = $param_data["value"];
				$parameter_orm->update();
			}

			return $parameter_orm;
		}
		catch (Database_Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
			return FALSE;
		}
	}

}