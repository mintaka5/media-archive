<?php
class Ode_Error {
	public static function mail($msg, $line, $file, $email) {
		error_log($msg . "\nLine: " . $line . "\nFile: " . $file . "\nHost: " . $_SERVER['SERVER_NAME'], 1, $email);
	}
}