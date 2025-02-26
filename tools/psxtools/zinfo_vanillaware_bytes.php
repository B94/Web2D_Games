<?php
require 'common.inc';
require 'common-guest.inc';
require 'quad_vanillaware.inc';

//define('DRY_RUN', true);
$gp_list = array();

function save_mbsmeta( &$file, $fname )
{
	if ( defined('DRY_RUN') )
		return;
	$dir = str_replace('.', '_', $fname);
	foreach ( $file as $sk => $sv )
	{
		if ( ! isset($sv['d']) )
			continue;

		$txt = debug_block($sv['d'], $sv['k']);
		$fn  = sprintf('%s/%02d.txt', $dir, $sk);
		save_file($fn, $txt);
	} // foreach ( $file as $sk => $sv )
	return;
}

function vanilla( $fname, $tag )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	global $gp_data;
	if ( ! isset($gp_data[$tag]) )
		return php_error('NO TAG %s', $fname);

	load_mbsfile($file, $gp_data[$tag]['sect'], $gp_data[$tag]['ord']);
	save_mbsmeta($file, $fname);

	global $gp_list;
	foreach ( $file as $sk => $sv )
	{
		if ( ! isset($sv['d']) )
			continue;

		$st = 0;
		for ( $i=0; $i < $sv['h']; $i++ )
		{
			$pos = $i * $sv['k'];
			for ( $j=0; $j < $sv['k']; $j++ )
			{
				$b = ord( $sv['d'][$pos+$j] );
				$gp_list[$sk][$j][$b] = 1;
			}
		} // for ( $i=0; $i < $sv['h']; $i++ )
	} // foreach ( $file as $sk => $sv )
	return;
}

$tag = '';
for ( $i=1; $i < $argc; $i++ )
{
	if ( is_file($argv[$i]) )
		vanilla($argv[$i], $tag);
	else
		$tag = $argv[$i];
}

ksort($gp_list);
foreach ( $gp_list as $sk => $sv )
{
	foreach ( $sv as $bk => $bv )
	{
		ksort($bv);
		printf('s%x[%2x] = ', $sk, $bk);
		foreach ( $bv as $k => $v )
			printf('%x ', $k);
		echo "\n";
	}
	echo "\n";
} // foreach ( $gp_list as $sk => $sv )
