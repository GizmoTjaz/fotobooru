<?php

	require_once "utils/Constants.php";
	require_once "Router.php";
	require_once "utils/Validator.php";
	require_once "models/Post.php";

	$snowflake = new \Godruoyi\Snowflake\Snowflake;
	$mime_repo = new \Dflydev\ApacheMimeTypes\FlatRepository;

	class PostController {

		public static function get (string $id) {

			$post_id = Validator::parse_int($id);
			$post = Post::find_by_id($post_id);

			if ($post) {
				Router::respond_with_json(200, $post);
			} else {
				Router::respond(404, "Post not found.");
			}
		}

		public static function get_all () {
			Router::respond_with_json(200, Post::get_all());
		}

		public static function get_all_by_uploader (string $id) {

			$uploader_id = Validator::parse_int($id);
			$uploader = User::find_by_id($uploader_id);

			if ($uploader != null) {
				Router::respond_with_json(200, Post::get_all_by_uploader($uploader->id));
			} else {
				Router::respond(404, "User not found.");
			}
		}

		public static function create () {
			
			global $snowflake, $mime_repo;

			if (!UserController::is_logged_in()) {
				Router::respond(401, "You must be logged in.");
				return;
			}

			if (!Validator::validate_file("media", "image")) {
				Router::respond(400, "Invalid file."); // Unnecessary, but just in case
				return;
			}

			$temp_location = $_FILES["media"]["tmp_name"];
			$mime = mime_content_type($temp_location);

			if ($mime == false) {
				Router::respond(500, "Failed while uploading the file.");
				return;
			}

			$id = $snowflake->id();
			$exts = $mime_repo->findExtensions($mime);

			if (count($exts) == 0) {
				Router::respond(500, "Failed while uploading the file.");
				return;
			}

			$file_name = "$id." . $exts[0];
			$file_location = Constants::get_post_media_directory() . "/$file_name";

			if (move_uploaded_file($temp_location, $file_location)) {

				$post = Post::create(UserController::get_current_user(), $file_name);

				if ($post != null) {
					Router::respond_with_json(200, $post);
				} else {
					unlink($file_location);
					Router::respond(500, "Failed while uploading the file.");
				}
			} else {
				unlink($temp_location);
				Router::respond(500, "Failed while uploading the file.");
			}
		}

		public static function delete (string $id) {

			if (!UserController::is_logged_in()) {
				Router::respond(401, "You must be logged in.");
				return;
			}

			$post_id = Validator::parse_int($id);
			$post = Post::find_by_id($post_id);

			if ($post == null) {
				Router::respond(404, "Post not found.");
				return;
			}
			
			if ($post->uploader->id != UserController::get_current_user()->id) {
				Router::respond(403, "This post was not created by you.");
				return;
			}

			$post->delete();
			Router::respond(200, "Successfully deleted post.");
		}
	}

