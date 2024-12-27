<?php

	class Ratelimit {

		public static function isOnCooldown (string $endpoint_name = "", int | float $time = .3): bool {

			if (isset($_SESSION["cooldown_$endpoint_name"])) {
				if (abs($_SESSION["cooldown_$endpoint_name"] - time()) <= $time) {
					return true;
				}
			}
		
			$_SESSION["cooldown_$endpoint_name"] = time();
			return false;
		}

	}
