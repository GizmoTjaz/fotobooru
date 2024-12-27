<?php

	require_once "Router.php";
	require_once "utils/Validator.php";
	require_once "models/Reply.php";

	class ReplyController {

		public static function get_all_for_post (string $id) {

			$post_id = Validator::parse_int($id);
			$post = Post::find_by_id($post_id);

			if ($post != null) {

				$replies = Reply::get_post_replies($post->id);
	
				Router::respond_with_json(200, $replies);
			} else {
				Router::respond(404, "Post not found.");
			}
		}

		public static function create (string $id) {
			
			if (!UserController::is_logged_in()) {
				Router::respond(401, "You must be logged in.");
				return;
			}

			$body = json_decode(file_get_contents("php://input"), true);

			if ($body == null) {
				Router::respond(400, "Invalid body data.");
				return;
			}
			
			$user = UserController::get_current_user();
			$post_id = Validator::parse_int($id);
			$post = Post::find_by_id($post_id);

			if ($post != null) {

				$content = mb_substr($body["content"], 0, 1024);

				if (strlen($content) == 0) {
					Router::respond(400, "Reply cannot be empty.");
					return;
				}

				$reply = Reply::create($post, $user, $content);
				Router::respond_with_json(200, $reply);

			} else {
				Router::respond(404, "Post not found.");
			}
		}

		public static function delete (string $id) {

			if (!UserController::is_logged_in()) {
				Router::respond(401, "You must be logged in.");
				return;
			}

			$reply_id = Validator::parse_int($id);
			$reply = Reply::find_by_id($reply_id);

			if ($reply == null) {
				Router::respond(404, "Reply not found.");
				return;
			}
			
			if ($reply->poster->id != UserController::get_current_user()->id) {
				Router::respond(403, "This reply was not created by you.");
				return;
			}

			$reply->delete();
			Router::respond(200, "Successfully deleted reply.");
		}
	}

