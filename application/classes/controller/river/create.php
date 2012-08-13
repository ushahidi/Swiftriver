<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Create River Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_River_Create extends Controller_River {
	
	/**
	 * Which step in the river creation process?
	 * @var step
	 */
	protected $step = 'name';
	
	/**
	 * Account Owner
	 * @var string
	 */
	protected $account_path = NULL;

	/**
	 * This steps content/form
	 * @var string
	 */
	protected $step_content = NULL;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Only account owners are alllowed here
		if ( ! $this->account->is_owner($this->visited_account->user->id) OR $this->anonymous)
		{
			throw new HTTP_Exception_403();
		}

		// The main create template
		$this->template->content = View::factory('pages/river/create')
			->bind('account_path', $this->account_path)
			->bind('step', $this->step)
			->bind('step_content', $this->step_content)
			->bind('open_url', $this->open_url)
			->bind('filters_url', $this->filters_url)
			->bind('view_url', $this->view_url);

		// Account Path
		$this->account_path = $this->user->account->account_path;

		// URLs
		$river_id = $this->request->param('id', 0);
		$this->river = ORM::factory('river', $river_id);
		if ($this->river->loaded())
		{
			$base_url = URL::site().$this->account_path.'/river/create';
			$this->open_url = $base_url.'/add_channels/'.$river_id;
			$this->filters_url = $base_url.'/add_filters/'.$river_id;
			$this->view_url = $base_url.'/finish/'.$river_id;
		}
		else
		{
			$this->open_url = '#';
			$this->filters_url = "#";
			$this->view_url = '#';
		}

		// Redirect to the index page
		if ($this->request->action() !== "index" AND ! $this->river->loaded())
		{
			$this->request->redirect(URL::site().$this->account_path.'/river/create');
		}

		// Redirect to the landing page if river creation is complete
		if ($this->river->loaded() AND $this->river->river_create_complete)
		{
			$this->request->redirect($this->river->get_base_url());
		}
	}

	/**
	 * Create a New River
	 * Step 1
	 * @return	void
	 */
	public function action_index()
	{
		$this->step_content = View::factory('pages/river/create/name')
			->bind('post', $post)
			->bind('errors', $errors);

		// Check for form submission
		if ($_POST AND CSRF::valid($_POST['form_auth_id']))
		{

			$post = Arr::extract($_POST, array('river_name', 'river_public'));
			try
			{
				$river = Model_River::create_new($post['river_name'], $post['river_public'], $this->user->account);

				// Redirect to the /create/open/<id> to open channels
				$this->request->redirect(URL::site().$this->account_path.'/river/create/add_channels/'.$river->id);
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('validation');
			}
			catch (Database_Exception $e)
			{
				$errors = array(__("A river with the name ':name' already exists", 
					array(':name' => $post['river_name'])
				));
			}
		}		
	}

	/**
	 * Create a New River
	 * Step 2 - Add channels to the river
	 * @return	void
	 */
	public function action_add_channels()
	{
		$this->step = 'channels';

		$this->step_content = View::factory('pages/river/settings/channels');
		$this->step_content->channels_config = json_encode(Swiftriver_Plugins::channels());
		$this->step_content->channels = json_encode($this->river->get_channels(TRUE));
		$this->step_content->base_url = $this->river->get_base_url().'/settings/channels';

	}

	/**
	 * Renders the filters step
	 */
	public function action_add_filters()
	{
		$this->step = 'filters';
		$this->step_content = View::factory('pages/river/settings/filters');
		$this->step_content->base_url = $this->river->get_base_url()."/settings/filters";
		$this->step_content->filters_config = json_encode(Swiftriver::get_content_filters());
		$this->step_content->filters = json_encode($this->river->get_filters(TRUE));
	}

	/**
	 * River creation complete
	 */
	public function action_finish()
	{
		$this->step = 'finish';

		$this->step_content = View::factory('pages/river/create/finish')
			->bind('river_name', $river_name);

		$river_name = $this->river->river_name;

		// Redirect to the river's home page
		if ($_POST AND CSRF::valid($_POST['form_auth_id']))
		{
			$this->river->complete_creation();
			$this->request->redirect($this->river->get_base_url());
		}

	}	
}