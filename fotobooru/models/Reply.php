<?php

	require_once "DBInit.php";

	class Reply {
		
		public int $id;
		public User $poster;
		public string $content;
		public int $created_at;

		public function __construct (mixed $data) {
			$this->id = $data["id"];
			$this->poster = User::find_by_id($data["poster_id"]);
			$this->content = htmlspecialchars($data["content"]);
			$this->created_at = strtotime($data["created_at"]);
		}

		public static function compare (Reply $reply1, Reply $reply2): int {

			$c1 = $reply1->created_at;
			$c2 = $reply2->created_at;

			if ($c1 == $c2)
				return 0;

			return $c1 > $c2
				? 1
				: -1;
		}

		/**
		 * @return array<Reply>
		 */
		public static function get_post_replies (int $post_id): array {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM replies WHERE post_id = :post_id");
			$query->bindParam(":post_id", $post_id, PDO::PARAM_INT);
			$query->execute();

			$replies = [];

			foreach ($query->fetchAll() as $reply) {
				array_push($replies, new Reply($reply));
			}

			usort($replies, "Reply::compare");

			return $replies;
		}

		public static function find_by_id (int $id): Reply | null {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM replies WHERE id = :id");
			$query->bindParam(":id", $id, PDO::PARAM_INT);
			$query->execute();
			$result = $query->fetch();

			if ($result == false)
				return null;

			return new Reply($result);
		}

		public static function create (Post $post, User $poster, string $content): Reply {

			$db = DBInit::getInstance();

			$query = $db->prepare("INSERT INTO replies (post_id, poster_id, content) VALUES (:post_id, :poster_id, :content)");
			$query->bindParam(":post_id", $post->id, PDO::PARAM_INT);
			$query->bindParam(":poster_id", $poster->id, PDO::PARAM_INT);
			$query->bindParam(":content", $content, PDO::PARAM_STR);
			$query->execute();

			return Reply::find_by_id($db->lastInsertId());
		}

		public static function delete_post_replies (int $post_id) {

			$db = DBInit::getInstance();

			$query = $db->prepare("DELETE FROM replies WHERE post_id = :post_id");
			$query->bindParam(":post_id", $post_id, PDO::PARAM_INT);
			$query->execute();
		}
		
		//

		public function delete () {

			$db = DBInit::getInstance();

			$query = $db->prepare("DELETE FROM replies WHERE id = :id");
			$query->bindParam(":id", $this->id, PDO::PARAM_INT);
			$query->execute();
		}
	}
