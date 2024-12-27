<?php

	require_once "Constants.php";

	class Validator {

		public static function is_field_set ($array, $field) {
			return isset($array[$field]) && !empty($array[$field]);
		}

		public static function require_fields ($array, $fields) {

			foreach ($fields as $field) {
				if (!Validator::is_field_set($array, $field)) {
					Router::respond(400, "Missing '" . $field . "' field.");
					return false;
				}
			}

			return true;
		}

		public static function parse_int (string $input): int | null {
			
			if (is_numeric($input)) {
				return intval($input);
			}

			Router::respond(400, "Invalid value type. Expected int.");
		}

		// $type = "any" | "image" | "video" | ...
		public static function validate_file (string $file_name, $type = "any"): bool {

			if (!isset($_FILES[$file_name])) {
				Router::respond(400, "No file uploaded.");
				return false;
			}

			if ($_FILES[$file_name]["size"] > Constants::$MAX_FILE_SIZE) {
				Router::respond(413, "Uploaded file is too big.");
				return false;
			}

			if ($type != "any" && !str_starts_with($_FILES[$file_name]["type"], $type)) {
				Router::respond(400, "Invalid file type.");
				return false;
			}

			return true;
		}
	}
