<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__) . '/../backend/modules/extensions/engine/helper.php';

/**
 * Tests for BackendExtensionsHelper
 *
 * @author Davy Hellemans <davy.hellemans@wijs.be>
 */
class BackendExtensionsHelperTest extends PHPUnit_Framework_TestCase
{
	public function testIsMaliciousFile()
	{
		$badStrings = array(
			'I hear that eval is evil',
			'You should use Spoon methods in stead of move_uploaded_file',
			'If you unlink my website, I will kick your ass'
		);

		foreach($badStrings as $string)
		{
			$this->assertTrue(BackendExtensionsHelper::isMaliciousFile($string));
		}

		$goodStrings = array(
			'Some call him an asshole, others call him a visionary',
			'We advise you to use Fork functionality to move uploaded files',
			'It would be pretty cool if foobar was an actual bar'
		);

		foreach($goodStrings as $string)
		{
			$this->assertFalse(BackendExtensionsHelper::isMaliciousFile($string));
		}
	}
}
