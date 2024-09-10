<?php namespace Config;

/***
*
* This file contains example values to alter default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Audit.php
*	2. Change any values
*	3. Remove any lines to fallback to defaults
*
***/

class Audit extends \Decoda\Audit\Config\Audit
{
	// Session key in that contains the integer ID of a logged in user
	public $sessionUserId = "logged_in";

    public $sessionCompanyId = "logged_in";

	// Whether to continue instead of throwing exceptions
	public $silent = true;
}