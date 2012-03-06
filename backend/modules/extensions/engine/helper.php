<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the extensions module.
 *
 * @author Davy Hellemans <davy.hellemans@wijs.be>
 */
class BackendExtensionsHelper
{
	/**
	 * List of functions that are generally disallowed within uploaded modules
	 */
	protected static $disallowedStrings = array(
		'copy',
		'define_syslog_variables',
		'dl',
		'ereg',
		'ereg_replace',
		'eregi',
		'eregi_replace',
		'eval',
		'exec',
		'ini_set',
		'is_uploaded_file',
		'magic_quotes_runtime',
		'move_uploaded_file',
		'mysql_connect',
		'mysql_db_query',
		'mysql_error',
		'mysql_escape_string',
		'mysql_query',
		'print_r',
		'session_is_registered',
		'session_register',
		'set_magic_quotes_runtime',
		'set_socket_blocking',
		'split',
		'spliti',
		'sql_regcase',
		'unlink'
	);

	/**
	 * Checks to see if any disallowed function names are used in this piece of source code.
	 *
	 * @param string $content
	 * @return bool
	 */
	public static function isMaliciousFile($content)
	{
		foreach(self::$disallowedStrings as $string)
		{
			if(preg_match('/\b' . $string . '\b/i', $content))
			{
				return true;
			}
		}

		return false;
	}
}
