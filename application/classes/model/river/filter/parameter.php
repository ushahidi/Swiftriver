<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for the river_filter_parameters table
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

class Model_River_Filter_Parameter extends ORM {

	/**
	 * Many-to-one relationship definition
	 * @var array
	 */	
	protected $_belongs_to = array(
		'river_filter' => array()
	);


	/**
	 * Overrides the default ORM update behaviour
	 */
	public function update(Validation $validation = NULL)
	{
		if ($this->pk() !== NULL AND $this->changed())
		{
			Kohana::$log->add(Log::INFO, "Updating filter parameter ".$this->id);

			// The filter item is being updated
			Swiftriver_Event::run('swiftriver.filter.parameter.pre_delete', $this);
		}

		return parent::update($validation);

	}

	/**
	 * Overrides the default ORM save
	 *
	 * @param Validation $validation When specified, applies the validation rules before saving
	 *
	 * @return Model_River_Filter_Item
	 */
	public function save(Validation $validation = NULL)
	{
		$result = parent::save($validation);

		Swiftriver_Event::run('swiftriver.filter.parameter.post_save', $this);

		return $result;
	}

	/**
	 * Override the default delete behaviour
	 */
	public function delete()
	{
		Kohana::$log->add(Log::INFO, "Deleting filter parameter ".$this->id);
		Swiftriver_Event::run('swiftriver.filter.parameter.pre_delete', $this);

		parent::delete();
	}
}