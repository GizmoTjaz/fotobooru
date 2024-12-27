<?php

	require_once "utils/Constants.php";
	require_once "DBInit.php";
	require_once "User.php";
	require_once "Reply.php";

	class Post {
		
		public int $id;
		public User $uploader;
		public string $media_url;
		/** @type{array<Reply>} */
		public array $replies;
		public int $created_at;

		public function __construct (mixed $data) {
			$this->id = $data["id"];
			$this->uploader = User::find_by_id($data["uploader_id"]);
			$this->media_url =  Constants::get_post_media_url() . "/" . $data["media"];
			$this->replies = Reply::get_post_replies($this->id);
			$this->created_at = strtotime($data["created_at"]);
		}

		public static function compare (Post $post1, Post $post2): int {

			$c1 = $post1->created_at;
			$c2 = $post2->created_at;

			if ($c1 == $c2)
				return 0;

			return $c1 > $c2
				? -1
				: 1;
		}

		/**
		 * @return array<Post>
		 */
		public static function get_all (): array {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM posts");
			$query->execute();

			$posts = [];

			foreach ($query->fetchAll() as $post) {
				array_push($posts, new Post($post));
			}

			usort($posts, "Post::compare");

			return $posts;
		}

		/**
		 * @return array<Post>
		 */
		public static function get_all_by_uploader (int $uploader_id): array {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM posts WHERE uploader_id = :id");
			$query->bindParam(":id", $uploader_id, PDO::PARAM_INT);
			$query->execute();

			$posts = [];

			foreach ($query->fetchAll() as $post) {
				array_push($posts, new Post($post));
			}

			usort($posts, "Post::compare");

			return $posts;
		}

		public static function find_by_id (int $id): Post | null {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM posts WHERE id = :id");
			$query->bindParam(":id", $id, PDO::PARAM_INT);
			$query->execute();
			$result = $query->fetch();

			if ($result == false)
				return null;

			return new Post($result);
		}

		public static function create (User $uploader, string $media_file_name): Post {

			$db = DBInit::getInstance();

			$query = $db->prepare("INSERT INTO posts (uploader_id, media) VALUES (:uploader_id, :media)");
			$query->bindParam(":uploader_id", $uploader->id, PDO::PARAM_INT);
			$query->bindParam(":media", $media_file_name, PDO::PARAM_STR);
			$query->execute();

			return Post::find_by_id($db->lastInsertId());
		}

		//

		public function delete () {

			$db = DBInit::getInstance();

			Reply::delete_post_replies($this->id);

			$query = $db->prepare("DELETE FROM posts WHERE id = :id");
			$query->bindParam(":id", $this->id, PDO::PARAM_INT);
			$query->execute();

			$media_file = Constants::get_post_media_directory() . "/" . basename($this->media_url);

			if (file_exists($media_file)) {
				unlink($media_file);
			}
		}
	}
