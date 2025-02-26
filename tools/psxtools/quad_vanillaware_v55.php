<?php
/*
[license]
Copyright (C) 2019 by Rufas Wan

This file is part of Web2D Games.
    <https://github.com/rufaswan/Web2D_Games>

Web2D Games is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Web2D Games is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Web2D Games.  If not, see <http://www.gnu.org/licenses/>.
[/license]
 */
require 'common.inc';
require 'common-json.inc';
require 'common-quad.inc';
require 'class-pixlines.inc';
require 'quad.inc';

php_req_extension('json_decode', 'json');

$gp_json = '';
$gp_s4_flag = 0;
$gp_s5_flag = 0;
$gp_s6_flag = 0;
$gp_s8_flag = 0;

function s4_flags( $is )
{
	global $gp_json, $gp_s4_flag;
	switch ( $gp_json['tag'] )
	{
			case 'ps2_grim':
			case 'ps2_odin':
				if ( $is === 'is_skip' )  return ($gp_s4_flag & 0x02);
				if ( $is === 'is_tex'  )  return ($gp_s4_flag & 0x04) === 0;
				return 0;

			case 'nds_kuma':
			case 'wii_mura':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':

			case 'psp_gran':
			case 'vit_mura':
			case 'vit_drag':
			case 'vit_odin':
				return 0;
	} // switch ( $tag )
	return 0;
}

function s5_flags( $is )
{
	global $gp_json, $gp_s5_flag;
	switch ( $gp_json['tag'] )
	{
			case 'ps2_grim':
			case 'ps2_odin':
				if ( $is === 'is_attack' )  return ($gp_s5_flag & 0x01);
				if ( $is === 'is_damage' )  return ($gp_s5_flag & 0x02);
				return 0;

			case 'nds_kuma':
			case 'wii_mura':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':

			case 'psp_gran':
			case 'vit_mura':
			case 'vit_drag':
			case 'vit_odin':
				return 0;
	} // switch ( $tag )
	return 0;
}

function s6_flags( $is )
{
	global $gp_json, $gp_s6_flag;
	switch ( $gp_json['tag'] )
	{
			case 'ps2_grim':
			case 'ps2_odin':
				if ( $is === 'is_blend' )  return ($gp_s6_flag & 0x04) === 0;
				return 0;

			case 'nds_kuma':
			case 'wii_mura':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':

			case 'psp_gran':
			case 'vit_mura':
			case 'vit_drag':
			case 'vit_odin':
				return 0;
	} // switch ( $tag )
	return 0;
}

function s8_flags( $is )
{
	global $gp_json, $gp_s8_flag;
	switch ( $gp_json['tag'] )
	{
			case 'ps2_grim':
			case 'ps2_odin':
				if ( $is === 'is_flipx' )  return ($gp_s8_flag & 0x01);
				if ( $is === 'is_flipy' )  return ($gp_s8_flag & 0x02);
				if ( $is === 'is_loop'  )  return ($gp_s8_flag & 0x04);
				if ( $is === 'is_end'   )  return ($gp_s8_flag & 0x08);
				if ( $is === 'is_skip'  )  return ($gp_s8_flag & 0x40);
				if ( $is === 'is_sfx'   )  return ($gp_s8_flag & 0x80);
				if ( $is === 'is_s4s5'  )  return ($gp_s8_flag & 0x400) === 0;
				return 0;

			case 'nds_kuma':
			case 'wii_mura':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':

			case 'psp_gran':
			case 'vit_mura':
			case 'vit_drag':
			case 'vit_odin':
				return 0;
	} // switch ( $tag )
	return 0;
}
//////////////////////////////
function s6s4_lines( $dir )
{
	global $gp_json;
	ob_start();

	$grid = new PixLines;
	foreach ( $gp_json['s6'] as $s6k => $s6v )
	{
		if ( empty($s6v) )
			continue;
		printf("s6[%d].flags  %s\n", $s6k, $s6v['bits']);

		$fn = sprintf('%s/%04d.clut', $dir, $s6k);
		$grid->new();

		for ( $i=0; $i < $s6v['s4'][1]; $i++ )
		{
			$s4k = $s6v['s4'][0] + $i;
			$s4v = $gp_json['s4'][$s4k];
			printf("  s4[%d].flags  %s\n", $s4k, $s4v['bits']);

			$s2k = $s4v['s0s1s2'][2];
			$s2v = $gp_json['s2'][$s2k];

			$grid->addquad($s2v, "\x0e");
		} // for ( $i=0; $i < $s6v['s4'][1]; $i++ )

		for ( $i=0; $i < $s6v['s5'][1]; $i++ )
		{
			$s5k = $s6v['s5'][0] + $i;
			$s5v = $gp_json['s5'][$s5k];
			printf("  s5[%d].flags  %s\n", $s5k, $s5v['bits']);

			$s3k = $s5v['s3'];
			$s3v = $gp_json['s3'][$s3k];

			$grid->addquad($s3v['rect'], "\x0d");
		} // for ( $i=0; $i < $s6v['s5'][1]; $i++ )

		$img = $grid->draw();
		save_clutfile($fn, $img);
	} // foreach ( $gp_json['s6'] as $s6k => $s6v )

	$txt = ob_get_clean();
	save_file("$dir/pixlines.txt", $txt);
	return;
}

function s6_loop( &$quad )
{
	global $gp_json, $gp_s6_flag, $gp_s4_flag, $gp_s5_flag;

	$quad['keyframe'] = array();
	$quad['hitbox']   = array();
	$quad['slot']     = array();
	foreach ( $gp_json['s6'] as $s6k => $s6v )
	{
		if ( empty($s6v) )
			continue;

		$gp_s6_flag = hexdec( $s6v['bits'] );

		// section keyframe
		$s4 = array();
		if ( $s6v['s4'][1] > 0 )
		{
			$layer = array();
			for ( $i=0; $i < $s6v['s4'][1]; $i++ )
			{
				$s4k = $s6v['s4'][0] + $i;
				$s4v = $gp_json['s4'][$s4k];

				$gp_s4_flag = hexdec( $s4v['bits'] );
				if ( s4_flags('is_skip') )
					continue;

				$s2k = $s4v['s0s1s2'][2];
				$s2v = $gp_json['s2'][$s2k];

				$data = array(
					'_debug'   => $s4v['bits'],
					'dstquad'  => $s2v,
				);

				$data['blend_id'] = $s4v['blend'];

				$s0k = $s4v['s0s1s2'][0];
				$s0v = $gp_json['s0'][$s0k];
				if ( $s0v !== '#ffffffff' )
					$data['fogquad'] = $s0v;

				if ( s4_flags('is_tex') )
				{
					$data['tex_id'] = $s4v['tex'];

					$s1k = $s4v['s0s1s2'][1];
					$s1v = $gp_json['s1'][$s1k];
					$data['srcquad'] = $s1v;
				}

				$layer[$i] = $data;
			} // for ( $i=0; $i < $s6v['s4'][1]; $i++ )

			$s4 = array(
				'_debug' => $s6v['bits'],
				'name'   => sprintf('keyframe %d', $s6k),
				'layer'  => $layer,
			);
			list_add( $quad['keyframe'], $s6k, $s4 );
		}

		// section hitbox
		$s5 = array();
		if ( $s6v['s5'][1] > 0 )
		{
			$layer = array();
			for ( $i=0; $i < $s6v['s5'][1]; $i++ )
			{
				$s5k = $s6v['s5'][0] + $i;
				$s5v = $gp_json['s5'][$s5k];

				$s3k = $s5v['s3'];
				$s3v = $gp_json['s3'][$s3k];

				$data = array(
					'_debug'  => $s5v['bits'],
					'hitquad' => $s3v['rect'],
				);
				$gp_s5_flag = hexdec( $s5v['bits'] );

				//if ( s5_flags('is_damage') )  $data['type'] = 'damage';
				//if ( s5_flags('is_attack') )  $data['type'] = 'attack';

				$layer[$i] = $data;
			} // for ( $i=0; $i < $s6v['s5'][1]; $i++ )

			$s5 = array(
				'name'   => sprintf('hitbox %d', $s6k),
				'layer'  => $layer,
			);
			list_add( $quad['hitbox'], $s6k, $s5 );
		}

		// section slot
		if ( ! empty($s4) && ! empty($s5) )
		{
			$slot = array(
				array('type' => 'keyframe' , 'id' => $s6k),
				array('type' => 'hitbox'   , 'id' => $s6k),
			);
			list_add( $quad['slot'], $s6k, $slot );
		}
	} // foreach ( $gp_json['s6'] as $s6k => $s6v )
	return;
}
//////////////////////////////
function s9_loop( &$quad )
{
	global $gp_json;

	$quad['skeleton'] = array();
	foreach ( $gp_json['s9'] as $s9k => $s9v )
	{
		if ( empty($s9v) )
			continue;

		if ( $s9v['sa'][1] > 0 )
		{
			$child = array();
			for ( $i=0; $i < $s9v['sa'][1]; $i++ )
			{
				$child[$i] = array(
					'attach' => array(
						'type' => 'animation',
						'id'   => $s9v['sa'][0] + $i,
					)
				);
			} // for ( $i=0; $i < $s9v['sa'][1]; $i++ )

			$name = str_replace('_', ' ', $s9v['name']);
			$name = strtolower($name);

			$skel = array(
				'name'  => $name,
				'child' => $child,
			);
			list_add( $quad['skeleton'], $s9k, $skel );
		}
	} // foreach ( $gp_json['s9'] as $s9k => $s9v )
	return;
}
//////////////////////////////
function s7_matrix( $s7, $flipx, $flipy )
{
	// in scale - rotate z-y-x - move - flip order
	$m = matrix_scale(4, $s7['scale'][0], $s7['scale'][1]);

	$t = matrix_rotate_z(4, $s7['rotate'][2]);
	if ( $t !== -1 )
		$m = matrix_multi44($m, $t);

	$t = matrix_rotate_y(4, $s7['rotate'][1]);
	if ( $t !== -1 )
		$m = matrix_multi44($m, $t);

	$t = matrix_rotate_x(4, $s7['rotate'][0]);
	if ( $t !== -1 )
		$m = matrix_multi44($m, $t);

	$bx = ( $flipx ) ? -1 : 1;
	$by = ( $flipy ) ? -1 : 1;

	$m[0+3] += ($s7['move'][0] * $bx);
	$m[4+3] += ($s7['move'][1] * $by);
	$m[8+3] +=  $s7['move'][2];

	$m[0+0] *= $bx;
	$m[4+1] *= $by;

	$s = implode(',', $m);
	if ( $s === '1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1' )
		return 0;
	return $m;
}
//////////////////////////////
function is_s8_end( &$k, &$s8loop )
{
	if ( s8_flags('is_loop') )
	{
		$k = $s8loop;
		return false;
	}

	if ( s8_flags('is_end') )
		return true;

	$k++;
	return false;
}

function sa_loop( &$quad )
{
	global $gp_json, $gp_s8_flag;

	$quad['animation'] = array();
	foreach ( $gp_json['sa'] as $sak => $sav )
	{
		if ( empty($sav) )
			continue;

		// ERROR gwendlyn.mbp , sa 7b , s8 3ce-3de (+1)
		//   0  3cf
		//   ...
		//   5  3d4  loop b <- forward , not loop
		//   7  3d6
		//   ...
		//   b  3da
		//   c  3db  loop 7 <- backward , but entry not added
		//   d  3dc         <- unused
		//   e  3dd  end    <- unused
		$i = 0;
		$time = array();
		$loop = -1;
		$line = array();
		while ( $i < $sav['s8'][1] )
		{
			$s8k = $sav['s8'][0] + $i;
			$s8v = $gp_json['s8'][$s8k];
			$gp_s8_flag = hexdec( $s8v['bits'] );

			if ( isset( $line[$i] ) )
			{
				$loop = $line[$i];
				break;
			}

			$flipx = s8_flags('is_flipx');
			$flipy = s8_flags('is_flipy');

			$s7k = $s8v['s7'];
			$matrix = s7_matrix( $gp_json['s7'][$s7k], $flipx, $flipy );

			$s6k = $s8v['s6'];
			$attach = array();
			if ( s8_flags('is_s4s5') )
			{
				if ( ! empty( $quad['slot'][$s6k] ) )
					$attach = array('type' => 'slot' , 'id' => $s6k);
				else
				if ( ! empty( $quad['keyframe'][$s6k] ) )
					$attach = array('type' => 'keyframe' , 'id' => $s6k);
				else
				if ( ! empty( $quad['hitbox'][$s6k] ) )
					$attach = array('type' => 'hitbox' , 'id' => $s6k);
			}

			$ent = array(
				'_debug' => $s8v['bits'],
				'time'   => $s8v['time'],
			);
			if ( $gp_json['s7'][$s7k]['fog'] !== '#ffffffff' )
				$ent['color'] = $gp_json['s7'][$s7k]['fog'];
			if ( $matrix !== 0 )
				$ent['matrix'] = $matrix;
			if ( ! empty($attach) )
				$ent['attach'] = $attach;

			$line[$i] = count($time);
			$time[] = $ent;
			if ( is_s8_end($i, $s8v['loop']) )
				break;
		} // while ( $i < $sav['s8'][1] )

		$delay = 0;
		while ( ! empty($time) )
		{
			if ( isset( $time[0]['attach'] ) )
				break;
			$delay += $time[0]['time'];
			$loop--;
			array_shift($time);
		} // while ( ! empty($time) )

		$anim = array(
			'delay'    => $delay,
			'name'     => sprintf('animation %d', $sak),
			'timeline' => $time,
			'loop_id'  => $loop,
		);
		list_add( $quad['animation'], $sak, $anim );
	} // foreach ( $gp_json['sa'] as $sak => $sav )
	return;
}
//////////////////////////////
function vanilla_blendmode( $tag )
{
	$blend = array();
	switch ( $tag )
	{
		case 'ps2_grim': // 0 1 2
		case 'ps2_odin': // 0 1 2
			// (A.rgb - B.rgb) * C.a + D.rgb
			// ABCD are 2 bits
			//   0=FG  1=BG  2=0  3=reserved
			$blend[0] = array(
				'name' => '44',
				'mode' => array('FUNC_ADD', 'SRC_ALPHA', 'ONE_MINUS_SRC_ALPHA'),
				'_debug' => '((FG.rgb - BG.rgb) * FG.a) + BG.rgb',
			);
			$blend[1] = array(
				'name' => '48',
				'mode' => array('FUNC_ADD', 'SRC_ALPHA', 'ONE'),
				'_debug' => '(FG.rgb * FG.a) + BG.rgb',
			);
			$blend[2] = array(
				'name' => '42',
				'mode' => array('FUNC_REVERSE_SUBTRACT', 'SRC_ALPHA', 'ONE'),
				'_debug' => '(-FG.rgb * FG.a) + BG.rgb',
			);
			$blend[3] = array(
				'name' => '54',
				'mode' => array('FUNC_ADD', 'DST_ALPHA', 'ONE_MINUS_DST_ALPHA'),
				'_debug' => '((FG.rgb - BG.rgb) * BG.a) + BG.rgb',
			);
			$blend[4] = array(
				'name' => '58',
				'mode' => array('FUNC_ADD', 'DST_ALPHA', 'ONE'),
				'_debug' => '(FG.rgb * BG.a) + BG.rgb',
			);
			$blend[5] = array(
				'name' => '52',
				'mode' => array('FUNC_REVERSE_SUBTRACT', 'DST_ALPHA', 'ONE'),
				'_debug' => '(-FG.rgb * BG.a) + BG.rgb',
			);
			return $blend;

		case 'wii_mura': // 0 1 2
			// D +/- ( ((1 - C) * A) + (C * B) )
			//   D + A = Addition
			//   D - A = Subtraction
			//   C * B = Multiplication
			//   D + C * B = Addition + Multiplication
			//   ((1 - C) * A) + (C * B) = Decal
			//   D + ((1 - C) * A) = Proportional
			//return $blend;

		case 'psp_gran': // 0 1 2
			//return $blend;

		case 'ps3_drag': // 0 1 2 6
		case 'ps3_odin': // 0 1 2 6
		case 'ps4_odin': // 0 1 2 6
		case 'ps4_drag': // 0 1 2 5 6
		case 'ps4_sent': // 0 1 2 3
		case 'vit_mura': // 0 1 2 6
		case 'vit_drag': // 0 1 2 6
		case 'vit_odin': // 0 1 2 6

		default:
		case 'nds_kuma': // 0
		//case 'swi': //
			$blend[0] = array(
				'name' => 'default',
				'mode' => array('FUNC_ADD', 'SRC_ALPHA', 'ONE_MINUS_SRC_ALPHA'),
			);
			return $blend;
	} // switch ( $cons )

	return $blend;
}

function vanilla( $line, $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	global $gp_json;
	$gp_json = json_decode($file, true);
	if ( empty($gp_json) )  return;

	$dir = str_replace('.', '_', $fname);
	echo "JSON $fname\n";

	$quad = load_idtagfile( $gp_json['id3'] );
	$quad['blend'] = vanilla_blendmode( $gp_json['tag'] );
	if ( $line )
		s6s4_lines($dir);

	s6_loop($quad);
	s9_loop($quad);
	sa_loop($quad);

	$quad = json_pretty($quad, '');
	save_file("$fname.quad", $quad);
	return;
}

$line = false;
for ( $i=1; $i < $argc; $i++ )
{
	if ( is_file($argv[$i]) )
		vanilla( $line, $argv[$i] );
	else
		$line = $argv[$i];
}
