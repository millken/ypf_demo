<?php

namespace Cat;

class Controller extends \Ypf\Core\Controller {
	public function display($template) {
		$output = $this->view->fetch($template);
		//$this->log->template($output);
		$this->response->SetOutput($output);
	}
}
