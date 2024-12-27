<?php

	require __DIR__ . "/vendor/autoload.php";

	require_once "Router.php";
	require_once "controllers/PostController.php";
	require_once "controllers/UserController.php";
	require_once "controllers/ReplyController.php";
	require_once "utils/Ratelimit.php";
	
	session_start();

	$path = isset($_SERVER["PATH_INFO"]) ? trim($_SERVER["PATH_INFO"], "/") : "";

	$urls = [

		// Users
		"users" => function () {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {

				if (Ratelimit::isOnCooldown("registration_attempt", 1)) {
					Router::respond(429, "You are making too many requests.");
					return;
				}

				UserController::register();
			} else {
				Router::respond(400, "Invalid method.");
			}
		},
		"users/:id" => function (string $id) {
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				UserController::get($id);
			} else {
				Router::respond(400, "Invalid method.");
			}
		},
		"users/@me" => function () {
			if ($_SERVER["REQUEST_METHOD"] == "GET") {

				if (Ratelimit::isOnCooldown("current_user", 1)) {
					Router::respond(429, "You are making too many requests.");
					return;
				}

				$current_user = UserController::get_current_user();

				if ($current_user != null) {
					Router::respond_with_json(200, $current_user);
				} else {
					Router::respond(404, "No current user found.");
				}
			} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
				UserController::logout();
			} else {
				Router::respond(400, "Invalid method.");
			}
		},
		"users/login" => function () {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {

				if (Ratelimit::isOnCooldown("login", 1)) {
					Router::respond(429, "You are making too many requests.");
					return;
				}

				UserController::login();
			} else {
				Router::respond(400, "Invalid method.");
			}
		},
		"users/:id/posts" => function (string $id) {
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				PostController::get_all_by_uploader($id);
			} else {
				Router::respond(400, "Invalid method.");
			}
		},

		// Posts
		"posts" => function () {
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				PostController::get_all();
			} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

				if (Ratelimit::isOnCooldown("posting", 20)) {
					Router::respond(429, "Please wait before creating another post.");
					return;
				}

				PostController::create();
			} else {
				Router::respond(400, "Invalid method.");
			}
		},
		"posts/:id" => function (string $id) {
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				PostController::get($id);
			} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

				if (Ratelimit::isOnCooldown("replying", 2)) {
					Router::respond(429, "You are making too many requests.");
					return;
				}

				PostController::delete($id);
			} else {
				Router::respond(400, "Invalid method.");
			}
		},

		// Replies
		"posts/:id/replies" => function (string $id) {
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				ReplyController::get_all_for_post($id);
			} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

				if (Ratelimit::isOnCooldown("replying", 2)) {
					Router::respond(429, "You are making too many requests.");
					return;
				}

				ReplyController::create($id);
			} else {
				Router::respond(400, "Invalid method.");
			}
		},
		"posts/:post_id/replies/:reply_id" => function (string $post_id, string $reply_id) {
			// if ($_SERVER["REQUEST_METHOD"] == "GET") {
			// 	ReplyController::find_by_id($reply_id);
			if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

				if (Ratelimit::isOnCooldown("reply_deletion", 2)) {
					Router::respond(429, "Please wait before deleting a reply again.");
					return;
				}

				ReplyController::delete($reply_id);
			} else {
				Router::respond(400, "Invalid method.");
			}
		},

		"" => function () {
			// Router::redirect("/");
			die();
		}
	];

	try {

		// Disabled because logging in and being redirected to the gallery would cause a ratelimit warning
		// if (Ratelimit::isOnCooldown("api", .2)) {
		// 	Router::respond(429, "You are making too many requests.");
		// 	return;
		// }

		if (isset($urls[$path])) {
			$urls[$path]();
		} else {

			$request_parts = preg_split("/\//", $path);

			if ($request_parts != false) {
				foreach ($urls as $url => $callback) {
				
					if (!str_contains($url, ":"))
						continue;
					
					$match_parts = preg_split("/\//", $url);
					$params = [];
	
					if (count($match_parts) != count($request_parts))
						continue;
	
					$i = -1;
					$match = true;

					foreach ($match_parts as $id) {
						
						$i += 1;

						if (str_starts_with($id, ":")) {
							$params[substr($id, 1)] = $request_parts[$i]; 
						} else if ($id != $request_parts[$i]) {
							$match = false;
							break;
						}

					}

					if ($match) {
						$urls[$url](...array_values($params));
						return;
					}
				}
			}

			Router::respond(404, "No controller for '$path'");
		}
	} catch (Exception $e) {
		Router::respond(404, "An error occurred: <pre>$e</pre>");
	} 
