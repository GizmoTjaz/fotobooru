<?php

	include_once "models/User.php";

	class Router {

		// Displays a given view and sets the $variables array into scope.
		public static function render ($file, $variables = array()) {

			extract($variables);

			ob_start();
			include($file);
			$renderedView = ob_get_clean();

			echo $renderedView;
		}

		public static function redirect ($url) {
			header("Location: " . $url);
		}

		public static function respond ($code, $message) {
			http_response_code($code);
			echo $message;
			die();
		}

		public static function respond_with_json ($code, $data) {
			header("Content-Type: application/json; charset=utf-8");
			Router::respond($code, json_encode($data));
		}
	}
