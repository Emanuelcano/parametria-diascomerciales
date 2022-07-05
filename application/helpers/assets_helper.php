<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function asset($url, $protocol = NULL)
{
	$rand = (ENVIRONMENT === 'production') ? '' : '?r='.mt_rand();
	return base_url($url, $protocol) . $rand;
}
