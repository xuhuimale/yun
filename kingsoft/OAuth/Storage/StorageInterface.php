<?php

/**
 * OAuth storage handler interface
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/Kingsoft
 * @package Kingsoft\OAuth
 * @subpackage Storage
 */
namespace Kingsoft\OAuth\Storage;

interface StorageInterface
{
	public function get($type);
	public function set($token, $type);
}
