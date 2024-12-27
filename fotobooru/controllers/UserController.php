<?php

	require_once "Router.php";
	require_once "utils/Validator.php";
	require_once "models/User.php";
	require_once "utils/Ratelimit.php";

	class UserController {

		public static function is_logged_in () {
			return isset($_SESSION["user"]);
		}

		public static function get_current_user (): User | null {

			if (isset($_SESSION["user"])) {
				return $_SESSION["user"];
			}

			return null;
		}

		public static function get (string $id) {

			$user_id = Validator::parse_int($id);
			$user = User::find_by_id($user_id);

			if ($user) {
				Router::respond_with_json(200, $user);
			} else {
				Router::respond(404, "User not found.");
			}
		}

		public static function login () {
			
			if (!Validator::require_fields($_POST, [ "name", "password" ]))
				return;
			
			if (preg_match("/^\w+$/", $_POST["name"]) !== 1 || strlen($_POST["name"]) > 20) {
				Router::respond(400, "Invalid name.");
				return;
			}

			$user = User::login($_POST["name"], $_POST["password"]);

			if ($user) {
				$_SESSION["user"] = $user;
				Router::respond_with_json(200, $user);
			} else {
				Router::respond(401, "Invalid credentials.");
			}
		}

		public static function logout () {
			if (UserController::is_logged_in()) {
				session_destroy();
				Router::respond(200, "Successfully logged out.");
			} else {
				Router::respond(400, "You are not currently logged in.");
			}
		}

		public static function register () {

			if (!Validator::require_fields($_POST, [ "name", "password" ]))
				return;

			if (strlen($_POST["name"]) > 20) {
				Router::respond(400, "Names can only be up to 20 characters long.");
				return;
			}

			if (preg_match("/^\w+$/", $_POST["name"]) !== 1) {
				Router::respond(400, "Invalid name.");
				return;
			}

			$existingUser = User::findByName($_POST["name"]);

			if ($existingUser == null) {

				if (Ratelimit::isOnCooldown("registration", 20)) {
					Router::respond(429, "Please wait before making another account.");
					return;
				}
				
				$user = User::create($_POST["name"], $_POST["password"]);
				$_SESSION["user"] = $user;

				Router::respond_with_json(200, $user);
			} else {
				Router::respond(409, "Name already exists.");
			}
		}

	}
