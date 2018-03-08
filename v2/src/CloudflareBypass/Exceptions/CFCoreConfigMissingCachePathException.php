<?php

namespace CloudflareBypass\Exceptions;

use Exception;

class CFCoreConfigMissingCachePathException extends Exception
{
	/**
	 * Message
	 *
	 * @var string
	 */
	protected $message = "'cache_path' is required";

}