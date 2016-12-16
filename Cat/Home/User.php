<?php

namespace Cat\Home;

class User extends \Cat\Controller {

	static $lru;
	public function index() {

		if (self::$lru == null) {
			self::$lru = new \Ypf\Cache\Lrucache(10);
		}

		$id = $this->request->get("id");
		$res = self::$lru->cache($id, function () use ($id) {
			$user = new \Model\Login\User();
			echo "miss\n";
			return $user->getUserById($id);
		}, 10);
		//$res = $user->getUserById($id);
		//$res = ["a" => 123];
		$output = json_encode($res);
		$this->response->setOutput($output);
	}

}
