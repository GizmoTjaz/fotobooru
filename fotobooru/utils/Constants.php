<?php

	if (!is_dir(Constants::get_post_media_directory()))
		mkdir(Constants::get_post_media_directory());

	class Constants {

		public static int $MAX_FILE_SIZE = 4000000;

		public static function get_uploads_directory () {
			return "$_SERVER[DOCUMENT_ROOT]/uploads";
		}

		public static function get_post_media_directory () {
			return Constants::get_uploads_directory() . "/posts";
		}

		public static function get_post_media_url () {
			return "http://$_SERVER[HTTP_HOST]/uploads/posts";
		}
	}
