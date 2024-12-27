<?php

	require_once "DBInit.php";
	require_once "Post.php";

	class User {

		public int $id;
		public string $name;
		public array $posts;

		public function __construct (mixed $data) {
			$this->id = $data["id"];
			$this->name = $data["name"];
		}

		public static function create (string $name, string $password): User {

			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			
			$db = DBInit::getInstance();

			$query = $db->prepare("INSERT INTO users (name, password) VALUES (:name, :password)");
			$query->bindParam(":name", $name);
			$query->bindParam(":password", $password_hash);
			$query->execute();

			return User::findByName($name);
		}

		public static function find_by_id (int $id): User | null {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM users WHERE id = :id");
			$query->bindParam(":id", $id, PDO::PARAM_INT);
			$query->execute();
			$result = $query->fetch();

			if ($result == false)
				return null;

			return new User($result);
		}

		public static function findByName (string $name): User | null {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM users WHERE name = :name");
			$query->bindParam(":name", $name, PDO::PARAM_STR);
			$query->execute();
			$result = $query->fetch();

			if ($result == false)
				return null;

			return new User($result);
		}

		public static function login (string $name, string $password): User | null {

			$db = DBInit::getInstance();

			$query = $db->prepare("SELECT * FROM users WHERE name = :name");
			$query->bindParam(":name", $name, PDO::PARAM_STR);
			$query->execute();
			$result = $query->fetch();

			if ($result == false)
				return null;
			if (!password_verify($password, $result["password"]))
				return null;

			$user = new User($result);
			$_SESSION["user"] = $user;
			return $user;
		}

	}
