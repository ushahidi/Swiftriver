<?php defined('SYSPATH') or die('No direct script access');
/**
 * River Filter Settings Controller
 * This controller provides the user with a way of specifying a "permanent"
 * set of filters that are to be applied to the drops before they hit the river
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Controller_River_Filters extends Controller_River_Settings {
	
	public function action_index()
	{
		$this->template->header->title = $this->river->river_name."~" .__("Filter Settings");

		$this->active = 'filters';
		$this->settings_content = View::factory('pages/river/settings/filters');
		$this->settings_content->base_url = $this->river->get_base_url()."/settings/filters";
		$this->settings_content->filters_config = json_encode(Swiftriver::get_content_filters());
		$this->settings_content->filters = json_encode($this->river->get_filters(TRUE));

	}

	/**
	 * REST endpoint for adding, deleting and updating
	 * the river filters
	 */
	public function action_manage()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		switch ($this->request->method())
		{
			case 'POST':
				// Add a new filter
				$filter_array = json_decode($this->request->body(), TRUE);
				$filter_orm = $this->river->get_filter($filter_array["filter"]);

				if ( ! $filter_orm->filter_enabled)
				{
					$filter_orm->filter_enabled = TRUE;
					$filter_orm->save();
				}

				echo json_encode(array(
					"id" => $filter_orm->id,
					"filter" => $filter_orm->filter,
					"enabled" => (bool) $filter_orm->filter_enabled,
					"parameters" => $filter_orm->get_parameters_array()
				));
			break;

			case 'PUT':
				// Modification of filters
				$filter_array = json_decode($this->request->body(), TRUE);

				$filter_id = $this->request->param('id', 0);
				$filter_orm = ORM::factory('river_filter', $filter_id);
				if ($filter_orm->loaded())
				{
					$filter_orm->filter_enabled = $filter_array["enabled"];
					$filter_orm->save();
				}
				
			break;

			case 'DELETE':
				$filter_id = $this->request->param('id', 0);
				ORM::factory('river_filter', $filter_id)->delete();
			break;
		}
	}

	/**
	 * Filter parameters RESTful API
	 */
	public function action_parameters()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		$filter_orm = $this->river->get_filter_by_id($this->request->param('id', 0));
		if ( ! $filter_orm->loaded())
		{
			throw new HTTP_Exception_400(__("An invalid river filter has been specified"));
		}

		// Load the individual parameter
		$param_id = $this->request->param('id2', 0);

		switch ($this->request->method())
		{
			case 'POST':
			case 'PUT':
				// Extract the request data
				$param_data = json_decode($this->request->body(), TRUE);

				if (($param_orm = $filter_orm->update_parameter($param_data, $param_id)) !== FALSE)
				{
					echo json_encode(array(
						"id" => $param_orm->id,
						"filter" => $param_data["filter"],
						"value" => $param_orm->parameter
					));
				}
				else
				{
					throw new HTTP_Exception_400(__("The filter parameter has already been added"));
				}
			break;

			case 'DELETE':
				// Delete the river filter parameter
				ORM::factory('river_filter_parameter', $param_id)->delete();
			break;
		}

	}
}