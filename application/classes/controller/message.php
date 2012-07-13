<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Message Controller
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

class Controller_Message extends Controller_Swiftriver {

	public function before()
	{
		parent::before();

		// CHECK: Are you viewing own messages?
		if ($this->user->username != $this->request->param('account'))
			throw new HTTP_Exception_404();

		$this->id = intval($this->request->param('id'));
	}
	
	public function action_inbox()
	{
		// CHECK: Are we reading a specific message?
		if ($this->id)
			return $this->action_inbox_read();

		$this->messages = ORM::factory('message')
			->where('recipient_id', '=', $this->user->id)
			->order_by('read', 'asc')
			->order_by('timestamp', 'desc')
			->find_all()
			->as_array();

		$link_inbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'inbox'
		));

		$link_outbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'outbox'
		));

		$link_create = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'create'
		));

		$count = 0;
		foreach ($this->messages as $message)
		{
			if ( ! $message->read)
			{
				$count++;
			}
		}

		$this->template->header->title = $this->user->username." / Inbox";
		$this->template->content = View::factory('pages/message/inbox')
			->bind('link_inbox', $link_inbox)
			->bind('link_outbox', $link_outbox)
			->bind('link_create', $link_create)
			->bind('new', $count)
			->bind('messages', $this->messages);
	}

	public function action_inbox_read()
	{
		$this->message = ORM::factory('message', $this->id);

		// CHECK: Are you the recipient of this message?
		if ( ! $this->message->is_recipient())
			throw new HTTP_Exception_404();

		$this->message->read = 1;
		$this->message->save();

		$location = 'Inbox';
		$link_inbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'inbox'
		));

		$link_create = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'create'
		));

		$this->template->header->title = $this->message->subject;
		$this->template->content = View::factory('pages/message/read')
			->bind('link_back', $link_inbox)
			->bind('link_create', $link_create)
			->bind('location', $location)
			->bind('message', $this->message);
	}

	public function action_outbox()
	{
		// CHECK: Are we reading a specific message?
		if ($this->id)
			return $this->action_outbox_read();

		$this->messages = ORM::factory('message')
			->where('sender_id', '=', $this->user->id)
			->order_by('timestamp', 'desc')
			->find_all()
			->as_array();

		$link_inbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'inbox'
		));

		$link_outbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'outbox'
		));

		$this->template->header->title = $this->user->username." / Outbox";
		$this->template->content = View::factory('pages/message/outbox')
			->bind('link_inbox', $link_inbox)
			->bind('link_outbox', $link_outbox)
			->bind('messages', $this->messages);
	}

	public function action_outbox_read()
	{
		$this->message = ORM::factory('message', $this->id);

		// CHECK: Are you the sender of this message?
		if ( ! $this->message->is_sender())
			throw new HTTP_Exception_404();

		$location = 'Outbox';
		$link_outbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'outbox'
		));

		$link_create = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'create'
		));

		$this->template->header->title = $this->message->subject;
		$this->template->content = View::factory('pages/message/read')
			->bind('link_back', $link_outbox)
			->bind('location', $location)
			->bind('message', $this->message);
	}

	public function action_create()
	{
		if (isset($_POST['a']))
			return $this->action_send();

		$link_inbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'inbox'
		));

		$link_outbox = route::url('messages', array(
			'account' => $this->request->param('account'),
			'action' => 'outbox'
		));

		$this->template->content = View::factory('pages/message/create')
			->bind('link_inbox', $link_inbox)
			->bind('link_outbox', $link_outbox);
	}

	public function action_send()
	{
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type', 'application/json');

		// CHECK: Do we have all necessary data?
		if ( ! isset($_POST['r']) OR ! isset($_POST['s']) OR ! isset($_POST['b']))
		{
			$this->response->status(400);
			echo json_encode("Something went wrong!");
			return;
		}

		// CHECK: Is the recipient, subject and message at least one character?
		if (strlen($_POST['r']) < 1 OR
		    strlen($_POST['s']) < 1 OR
		    strlen($_POST['b']) < 1)
		{
			$this->response->status(400);
			echo json_encode("You didn't fill in one of the fields!");
			return;
		}

		$recipient = ORM::factory('user')
			->where('username', '=', $_POST['r'])
			->find();

		// CHECK: Is the recipient a real user?
		if (is_null($recipient) OR $recipient->username != $_POST['r'])
		{
			$this->response->status(400);
			echo json_encode("The recipient isn't a real user!");
			return;
		}

		// CHECK: Is the recipient not you?
		if ($this->user->username == $recipient->username)
		{
			$this->response->status(400);
			echo json_encode("You can't send messages to yourself!");
			return;
		}

		$last = ORM::factory('message')
			->order_by('timestamp', 'desc')
			->find();

		// CHECK: Was this message just sent?
		if ($last->recipient == $_POST['r'] AND
		    $last->subject == $_POST['s'] AND
		    $last->message == $_POST['b'])
		{
			$this->response->status(400);
			echo json_encode("The message was already sent!");
			return;
		}

		// CHECK: Has another message been recently sent?
		if (time() - strtotime($last->timestamp) < 30)
		{
			$this->response->status(400);
			echo json_encode("You're sending messages too quickly!");
			return;
		}

		$message = ORM::factory('message');
		$message->recipient_id = $recipient->id;
		$message->sender_id = $this->user->id;
		$message->subject = HTML::entities($_POST['s']);
		$message->message = HTML::entities($_POST['b']);
		$message->save();

		echo json_encode("Message sent successfully!");
	}
} // End Message