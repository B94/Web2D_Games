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
require 'common-guest.inc';
require 'common-json.inc';
require 'quad_vanillaware.inc';

$gp_share = array();

// Legend
//   f4 = 32-bit float
//   1  =  8-bit int
//   2  = 16-bit int
//   4  = 32-bit int
//   c* = char[] / string
//   c4 = RGBA color
//////////////////////////////
function FMBS_s0( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim':
			case 'ps2_odin':
				$s0 = ps2_quad20c($s);
				break;

			case 'nds_kuma':
			case 'wii_mura':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':
				$s0 = nds_quad18c($s);
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = $s0;
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s0.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s1( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim':
			case 'ps2_odin':
				$s1 = ps2_quad20p($s, $ord);
				break;

			case 'nds_kuma':
			case 'wii_mura':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':
				$s1 = nds_quad30p($s, $ord);
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = $s1;
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s1.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s2( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim':
			case 'ps2_odin':
				$s2 = ps2_quad20p($s, $ord);
				break;

			case 'nds_kuma':
			case 'wii_mura':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':
				$s2 = nds_quad30p($s, $ord);
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = $s2;
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s2.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s0s1s2( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$dat0 = array();
	$dat1 = array();
	$dat2 = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		switch ( $gp_share['tag'] )
		{
			case 'psp_gran':
				list($s0,$s1,$s2) = psp_quad60pc($s, $ord);
				break;

			case 'vit_mura':
			case 'vit_drag':
			case 'vit_odin':
				list($s0,$s1,$s2) = psp_quad60pc($s, $ord);
				break;
		} // switch ( $gp_share['tag'] )

		$dat0[$i] = $s0;
		$dat1[$i] = $s1;
		$dat2[$i] = $s2;
	} // for ( $i=0; $i < $sc; $i++ )

	// s0s1 = unused
	$fn = sprintf('%s/s2.txt', $gp_share['dir']);
	save_file($fn, $text);
	return array($dat0, $dat1, $dat2);
}
//////////////////////////////
function FMBS_s3( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim':
			case 'ps2_odin':
			case 'nds_kuma':
			case 'wii_mura':
			case 'psp_gran':
			case 'vit_mura':
			case 'vit_drag':
			case 'vit_odin':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':
				break;
		} // switch ( $gp_share['tag'] )
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s3.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s4( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		$flags    = '';
		$blend_id = 0;
		$tex_id   = 0;
		$s0_id = 0;
		$s1_id = 0;
		$s2_id = 0;

		// c , v66+
		// 10 , v72+
		// 14 , v76+

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// 1 - 1 1 2   - - 2   2   2   2   2   2   2   2
			case 'ps2_odin': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// 1 - 1 1 2   - - 2   2   2   2   2   2   2   2
				$b00 = $ord($s, 0x00, 2); // flags
				$b02 = $ord($s, 0x02, 1); // 0 1 2  blend id
				$b03 = $ord($s, 0x03, 1); // tex id
				$b04 = $ord($s, 0x04, 2); // s1 id
				// 00 00
				$b08 = $ord($s, 0x08, 2); // s0 id
				$b0a = $ord($s, 0x0a, 2);
				$b0c = $ord($s, 0x0c, 2);
				$b0e = $ord($s, 0x0e, 2);
				$b10 = $ord($s, 0x10, 2); // s2 id
				$b12 = $ord($s, 0x12, 2);
				$b14 = $ord($s, 0x14, 2);
				$b16 = $ord($s, 0x16, 2);

				$flags    = sprintf('#%04x', $b00);
				$blend_id = $b02;
				$tex_id   = $b03;
				$s0_id = $b08;
				$s1_id = $b04;
				$s2_id = $b10;
				break;

			case 'nds_kuma': // c
				// 0 1 2 3 4 5 6 7 8 9 a b
				// 1 - - 1 2   - - 1 - 2
			case 'wii_mura': // c
				// 0 1 2 3 4 5 6 7 8 9 a b
				// - 1 1 1 2   - - 2   2
				$b00 = $ord($s, 0x00, 2); // flags
				$b02 = $ord($s, 0x02, 1); // blend id
				$b03 = $ord($s, 0x03, 1); // tex id
				$b04 = $ord($s, 0x04, 2); // s1 id
				// 00 00
				$b08 = $ord($s, 0x08, 2); // s0 id
				$b0a = $ord($s, 0x0a, 2); // s2 id

				$flags    = sprintf('#%04x', $b00);
				$blend_id = $b02;
				$tex_id   = $b03;
				$s0_id = $b08;
				$s1_id = $b04;
				$s2_id = $b0a;
				break;

			case 'psp_gran': // c
				// 0 1 2 3 4 5 6 7 8 9 a b
				// 1 - 1 1 2   2   2   2
			case 'vit_mura': // c
				// 0 1 2 3 4 5 6 7 8 9 a b
				// 1 - 1 1 - - 2   2   2
			case 'vit_drag': // c
				// 0 1 2 3 4 5 6 7 8 9 a b
				// 1 - 1 1 2   2   2   2
				$b00 = $ord($s, 0x00, 2); // flags
				$b02 = $ord($s, 0x02, 1); // blend id
				$b03 = $ord($s, 0x03, 1); // tex id
				$b04 = $ord($s, 0x04, 2);
				$b06 = $ord($s, 0x06, 2); // s1 id *unused*
				$b08 = $ord($s, 0x08, 2); // s0 id *unused*
				$b0a = $ord($s, 0x0a, 2); // s2 id

				$flags    = sprintf('#%04x', $b00);
				$blend_id = $b02;
				$tex_id   = $b03;
				$s0_id = $b0a;
				$s1_id = $b0a;
				$s2_id = $b0a;
				break;

			case 'vit_odin': // 10
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - - 1 1 1 1 - 2   2   2
				$b00 = $ord($s, 0x00, 4);
				$b04 = $ord($s, 0x04, 1);
				$b05 = $ord($s, 0x05, 1); // flags
				$b06 = $ord($s, 0x06, 1); // 0 1 2 6  blend id
				$b07 = $ord($s, 0x07, 1); // tex id
				$b08 = $ord($s, 0x08, 2);
				$b0a = $ord($s, 0x0a, 2); // s1 id *unused*
				$b0c = $ord($s, 0x0c, 2); // s0 id *unused*
				$b0e = $ord($s, 0x0e, 2); // s2 id

				$flags    = sprintf('#%04x', $b05);
				$blend_id = $b06;
				$tex_id   = $b07;
				$s0_id = $b0e;
				$s1_id = $b0e;
				$s2_id = $b0e;
				break;

			case 'ps3_drag': // c
				// 0 1 2 3 4 5 6 7 8 9 a b
				// - 1 1 1 2   2   2   2
				$b00 = $ord($s, 0x00, 2); // flags
				$b02 = $ord($s, 0x02, 1); // 0 1 2 6  blend id
				$b03 = $ord($s, 0x03, 1); // tex id
				$b04 = $ord($s, 0x04, 2);
				$b06 = $ord($s, 0x06, 2); // s1 id
				$b08 = $ord($s, 0x08, 2); // s0 id
				$b0a = $ord($s, 0x0a, 2); // s2 id

				$flags    = sprintf('#%04x', $b00);
				$blend_id = $b02;
				$tex_id   = $b03;
				$s0_id = $b08;
				$s1_id = $b06;
				$s2_id = $b0a;
				break;

			case 'ps3_odin': // 10
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// - - 2   1 1 1 1 - 1 2   2   2
			case 'ps4_drag': // 10
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - - 1 1 1 2   2   2   2
			case 'ps4_odin': // 10
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 1 1 1 1 1 - 2   2   2
				$b00 = $ord($s, 0x00, 4);
				$b04 = $ord($s, 0x04, 1);
				$b05 = $ord($s, 0x05, 1); // flags
				$b06 = $ord($s, 0x06, 1); // blend id
				$b07 = $ord($s, 0x07, 1); // tex id
				$b08 = $ord($s, 0x08, 2);
				$b0a = $ord($s, 0x0a, 2); // s1 id
				$b0c = $ord($s, 0x0c, 2); // s0 id
				$b0e = $ord($s, 0x0e, 2); // s2 id

				$flags    = sprintf('#%04x', $b05);
				$blend_id = $b06;
				$tex_id   = $b07;
				$s0_id = $b0c;
				$s1_id = $b0a;
				$s2_id = $b0e;
				break;

			case 'ps4_sent': // 14
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// 2   - - 1 1 1 1 4       2   2   2   - -
				$b00 = $ord($s, 0x00, 4);
				$b04 = $ord($s, 0x04, 1);
				$b05 = $ord($s, 0x05, 1); // flags
				$b06 = $ord($s, 0x06, 1); // 0 1 2 3  blend id
				$b07 = $ord($s, 0x07, 1); // tex id
				$b08 = $ord($s, 0x08, 4);
				$b0c = $ord($s, 0x0c, 2); // s1 id
				$b0e = $ord($s, 0x0e, 2); // s0 id
				$b10 = $ord($s, 0x10, 2); // s2 id

				$flags    = sprintf('#%04x', $b05);
				$blend_id = $b06;
				$tex_id   = $b07;
				$s0_id = $b0e;
				$s1_id = $b0c;
				$s2_id = $b10;
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = array(
			$blend_id,$tex_id ,
			$s0_id,$s1_id,$s2_id ,
			$flags ,
		);
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s4.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s5( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim':
			case 'ps2_odin':
			case 'nds_kuma':
			case 'wii_mura':
			case 'psp_gran':
			case 'vit_mura':
			case 'vit_drag':
			case 'vit_odin':
			case 'ps3_drag':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':
				break;
		} // switch ( $gp_share['tag'] )
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s5.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s6( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		$x1 = 0;
		$y1 = 0;
		$x2 = 0;
		$y2 = 0;
		$s4_set_id = 0;
		$s4_set_no = 0;
		$s5_set_id = 0;
		$s5_set_no = 0;
		$flags     = '';

		// 18 , s4 set no++ , flags-- , v6b+
		// 1c , v6e+

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// f4      f4      f4      f4      2   1 - 1 1 1 -
			case 'ps2_odin': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// f4      f4      f4      f4      2   2   1 1 1 -
			case 'nds_kuma': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// f4      f4      f4      f4      2   1 - 1 1 1 -
			case 'wii_mura': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// f4      f4      f4      f4      2   2   1 1 - 1
				$b00 = float32( $ord($s,0x00,4) ); // rect.left
				$b04 = float32( $ord($s,0x04,4) ); // rect.top
				$b08 = float32( $ord($s,0x08,4) ); // rect.right
				$b0c = float32( $ord($s,0x0c,4) ); // rect.bottom
				$b10 = $ord($s, 0x10, 2); // s4 set id
				$b12 = $ord($s, 0x12, 2); // s5 set id
				$b14 = $ord($s, 0x14, 1); // s4 set no
				$b15 = $ord($s, 0x15, 1); // s5 set no
				$b16 = $ord($s, 0x16, 2); // flags

				$x1 = $b00;
				$y1 = $b04;
				$x2 = $b08;
				$y2 = $b0c;
				$s4_set_id = $b10;
				$s4_set_no = $b14;
				$s5_set_id = $b12;
				$s5_set_no = $b15;
				$flags     = sprintf('#%04x', $b16);
				break;

			case 'psp_gran': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// f4      f4      f4      f4      2   2   1 - 1 1
			case 'vit_mura': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// f4      f4      f4      f4      2   2   1 - 1 1
				$b00 = float32( $ord($s,0x00,4) ); // rect.left
				$b04 = float32( $ord($s,0x04,4) ); // rect.top
				$b08 = float32( $ord($s,0x08,4) ); // rect.right
				$b0c = float32( $ord($s,0x0c,4) ); // rect.bottom
				$b10 = $ord($s, 0x10, 2); // s4 set id
				$b12 = $ord($s, 0x12, 2); // s5 set id
				$b14 = $ord($s, 0x14, 2); // s4 set no
				$b16 = $ord($s, 0x16, 1); // s5 set no
				$b17 = $ord($s, 0x17, 1); // flags

				$x1 = $b00;
				$y1 = $b04;
				$x2 = $b08;
				$y2 = $b0c;
				$s4_set_id = $b10;
				$s4_set_no = $b14;
				$s5_set_id = $b12;
				$s5_set_no = $b16;
				$flags     = sprintf('#%04x', $b17);
				break;

			case 'vit_drag': // 1c
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b
				// f4      f4      f4      f4      3     - 2   1 - 1 1 - -
			case 'vit_odin': // 1c
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b
				// f4      f4      f4      f4      2   - - 2   1 - 1 1 - -
			case 'ps3_drag': // 1c
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b
				// f4      f4      f4      f4      - 3     2   - 1 1 1 - -
			case 'ps3_odin': // 1c
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b
				// f4      f4      f4      f4      - 3     2   2   1 1 - -
			case 'ps4_drag': // 1c
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b
				// f4      f4      f4      f4      3     - 2   1 - 1 1 - -
			case 'ps4_odin': // 1c
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b
				// f4      f4      f4      f4      3     - 2   2   1 1 - -
			case 'ps4_sent': // 1c
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b
				// f4      f4      f4      f4      3     - 1 - 2   1 1 - -
				$b00 = float32( $ord($s,0x00,4) ); // rect.left
				$b04 = float32( $ord($s,0x04,4) ); // rect.top
				$b08 = float32( $ord($s,0x08,4) ); // rect.right
				$b0c = float32( $ord($s,0x0c,4) ); // rect.bottom
				$b10 = $ord($s, 0x10, 4); // s4 set id
				$b14 = $ord($s, 0x14, 2); // s5 set id
				$b16 = $ord($s, 0x16, 2); // s4 set no
				$b18 = $ord($s, 0x18, 1); // s5 set no
				$b19 = $ord($s, 0x19, 1); // flags

				$x1 = $b00;
				$y1 = $b04;
				$x2 = $b08;
				$y2 = $b0c;
				$s4_set_id = $b10;
				$s4_set_no = $b16;
				$s5_set_id = $b14;
				$s5_set_no = $b18;
				$flags     = sprintf('#%04x', $b19);
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = array(
			$x1,$y1,$x2,$y2 ,
			$s4_set_id,$s4_set_no ,
			$s5_set_id,$s5_set_no ,
			$flags ,
		);
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s6.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s7( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		$fog      = '';
		$move_x   = 0;
		$move_y   = 0;
		$move_z   = 0;
		$rotate_x = 0;
		$rotate_y = 0;
		$rotate_z = 0;
		$scale_x  = 0;
		$scale_y  = 0;

		// 24 , v66+

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      f4      f4      - - - - f4      f4      f4      f4      f4
			case 'ps2_odin': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      f4      f4      - - - - f4      f4      f4      f4      f4
				$b00 = float32( $ord($s,0x00,4) ); // red
				$b04 = float32( $ord($s,0x04,4) ); // green
				$b08 = float32( $ord($s,0x08,4) ); // blue
				$b0c = float32( $ord($s,0x0c,4) ); // alpha
				$b10 = float32( $ord($s,0x10,4) ); // move x
				$b14 = float32( $ord($s,0x14,4) ); // move y
				$b18 = float32( $ord($s,0x18,4) ); // move z
				$b1c = float32( $ord($s,0x1c,4) ); // rotate x
				$b20 = float32( $ord($s,0x20,4) ); // rotate y
				$b24 = float32( $ord($s,0x24,4) ); // rotate z
				$b28 = float32( $ord($s,0x28,4) ); // scale x
				$b2c = float32( $ord($s,0x2c,4) ); // scale y

				$move_x   = $b10;
				$move_y   = $b14;
				$move_z   = $b18;
				$rotate_x = $b1c;
				$rotate_y = $b20;
				$rotate_z = $b24;
				$scale_x  = $b28;
				$scale_y  = $b2c;
				$fog      = sprintf('#%02x%02x%02x%02x',
					($b00 * 255) & BIT8,
					($b04 * 255) & BIT8,
					($b08 * 255) & BIT8,
					($b0c * 255) & BIT8);
				break;

			case 'nds_kuma': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      - - - - - - - - - - - - - - - - f4      f4      c4
			case 'wii_mura': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      - - - - f4      f4      f4      f4      f4      c4
			case 'psp_gran': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      - - - - f4      f4      f4      f4      f4      c4
			case 'vit_mura': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      - - - - f4      f4      f4      f4      f4      c4
			case 'vit_drag': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      - - - - f4      f4      f4      f4      f4      c4
			case 'vit_odin': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      - - - - f4      f4      f4      f4      f4      c4
			case 'ps3_drag': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      f4      f4      f4      f4      f4      f4      c4
			case 'ps3_odin': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      f4      f4      f4      f4      f4      f4      c4
			case 'ps4_drag': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      f4      f4      f4      f4      f4      f4      c4
			case 'ps4_odin': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      - - - - f4      f4      f4      f4      f4      c4
			case 'ps4_sent': // 24
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// f4      f4      f4      f4      f4      f4      f4      f4      c4
				$b00 = float32( $ord($s,0x00,4) ); // move x
				$b04 = float32( $ord($s,0x04,4) ); // move y
				$b08 = float32( $ord($s,0x08,4) ); // move z
				$b0c = float32( $ord($s,0x0c,4) ); // rotate x
				$b10 = float32( $ord($s,0x10,4) ); // rotate y
				$b14 = float32( $ord($s,0x14,4) ); // rotate z
				$b18 = float32( $ord($s,0x18,4) ); // scale x
				$b1c = float32( $ord($s,0x1c,4) ); // scale y
				$b20 = substr($s,0x20,4);

				$move_x   = $b00;
				$move_y   = $b04;
				$move_z   = $b08;
				$rotate_x = $b0c;
				$rotate_y = $b10;
				$rotate_z = $b14;
				$scale_x  = $b18;
				$scale_y  = $b1c;
				$fog      = '#' . bin2hex($b20);
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = array(
			$move_x,$move_y,$move_z ,
			$rotate_x,$rotate_y,$rotate_z ,
			$scale_x , $scale_y ,
			$fog ,
		);
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s7.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s8( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		$s6_id = 0;
		$s7_id = 0;
		$fps   = 0;
		$flags = '';
		$loop  = 0;
		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 1 1 - - - - - - - 1 - - -
			case 'ps2_odin': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 1 1 - - - 1 - 1 - 2   - -
			case 'nds_kuma': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - - 1 - - - - - - - - - - 1 - 2   - -
			case 'wii_mura': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   - - 2   - 1 1 1 1 1 1 - - - - - - 1 - - - - - -
			case 'psp_gran': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 1 - - - - 1 - 1 - - - - -
			case 'vit_mura': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 - - - - - 1 - - - - - - -
			case 'vit_drag': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 - 1 - - - 1 - - - - - - -
			case 'vit_odin': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 1 1 - - - 1 - 1 - 2   - -
			case 'ps3_drag': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   - - 2   - 1 1 1 1 1 1 - - - - 1 - 1 - - - - - -
			case 'ps3_odin': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   - - 2   2   1 1 1 1 1 1 - - - 1 - 1 - 1 - - 2
			case 'ps4_drag': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 - 1 - - - 1 - - - - - - -
			case 'ps4_odin': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 2   1 1 1 1 1 1 1 - - - 1 - 1 - 2   - -
			case 'ps4_sent': // 20
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - - 2   2   2   - - 1 - 1 1 1 1 1 - 1 - - - 1 - - - - - - -
				$b00 = $ord($s, 0x00, 2); // s6 id
				// 00 00
				$b04 = $ord($s, 0x04, 2); // s7 id
				$b06 = $ord($s, 0x06, 2); // frames
				$b08 = $ord($s, 0x08, 4); // flags
				$b0c = $ord($s, 0x0c, 2); // loop s8 id
				$b0e = $ord($s, 0x0e, 1); // 0 1 2  s5s3 interpolation
				$b0f = $ord($s, 0x0f, 1); // interpolation rate
				$b10 = $ord($s, 0x10, 1); // 0 1 2  s7 interpolation
				$b11 = $ord($s, 0x11, 1); // 0 1 2  s6 interpolation
				$b12 = $ord($s, 0x12, 1); // 0 1 2  s0s1s2 interpolation
				$b13 = $ord($s, 0x13, 1); // n*180
				$b14 = $ord($s, 0x14, 4);
				$b18 = $ord($s, 0x18, 2);
				$b1a = $ord($s, 0x1a, 2); // 0 1  sfx mute
				$b1c = $ord($s, 0x1c, 4); // sfx id

				$s6_id = $b00;
				$s7_id = $b04;
				$fps   = $b06;
				$flags = sprintf('#%08x', $b08);
				$loop  = $b0c;
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = array(
			$s6_id,$s7_id ,
			$fps,$loop ,
			$flags ,
		);
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s8.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_s9( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		$x1 = 0;
		$y1 = 0;
		$x2 = 0;
		$y2 = 0;
		$name = '';
		$sa_set_id = 0;
		$sa_set_no = 0;
		$sa_main   = 0;

		// 30 , v72+

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
			case 'ps2_odin': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
			case 'nds_kuma': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
			case 'wii_mura': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
			case 'psp_gran': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
			case 'vit_mura': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
			case 'vit_drag': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
			case 'ps3_drag': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 1 - - -
				$b00 = float32( $ord($s,0x00,4) ); // rect.left
				$b04 = float32( $ord($s,0x04,4) ); // rect.top
				$b08 = float32( $ord($s,0x08,4) ); // rect.right
				$b0c = float32( $ord($s,0x0c,4) ); // rect.bottom
				$b10 = substr0($s, 0x10);
				$b28 = $ord($s, 0x28, 2); // sa_set_id
				$b2a = $ord($s, 0x2a, 1); // sa_set_no
				$b2b = $ord($s, 0x2b, 1); // sa_set_main
				$b2c = $ord($s, 0x2c, 1); // 0 1

				$x1 = $b00;
				$y1 = $b04;
				$x2 = $b08;
				$y2 = $b0c;
				$name = $b10;
				$sa_set_id = $b28;
				$sa_set_no = $b2a;
				$sa_main   = $b2b;
				break;

			case 'vit_odin': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 2   1 1
			case 'ps3_odin': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 2   1 1
			case 'ps4_drag': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 2   1 1
			case 'ps4_odin': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 2   1 1
			case 'ps4_sent': // 30
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// f4      f4      f4      f4      c[18]                                           2   1 1 2   1 1
				$b00 = float32( $ord($s,0x00,4) ); // rect.left
				$b04 = float32( $ord($s,0x04,4) ); // rect.top
				$b08 = float32( $ord($s,0x08,4) ); // rect.right
				$b0c = float32( $ord($s,0x0c,4) ); // rect.bottom
				$b10 = substr0($s, 0x10);
				$b28 = $ord($s, 0x28, 2); // sa_set_id
				$b2a = $ord($s, 0x2a, 1); // sa_set_no
				$b2b = $ord($s, 0x2b, 1); // sa_set_main
				$b2c = $ord($s, 0x2c, 2);
				$b2e = $ord($s, 0x2e, 1);
				$b2f = $ord($s, 0x2f, 1); // 0 1

				$x1 = $b00;
				$y1 = $b04;
				$x2 = $b08;
				$y2 = $b0c;
				$name = $b10;
				$sa_set_id = $b28;
				$sa_set_no = $b2a;
				$sa_main   = $b2b;
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = array(
			$x1,$y1,$x2,$y2 ,
			$name ,
			$sa_set_id,$sa_set_no,$sa_main ,
		);
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/s9.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_sa( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		$s8_set_id  = 0;
		$s8_set_no  = 0;
		$s8_set_sum = 0;
		$s8_set_st  = 0;

		// 10 , v66+
		// 14 , v6b+
		// 18 , v72+

		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim': // 8
				// 0 1 2 3 4 5 6 7
				// 2   1 - 2   1 -
			case 'ps2_odin': // 8
				// 0 1 2 3 4 5 6 7
				// 2   2   2   1 -
				$b00 = $ord($s, 0x00, 2); // s8_set_id
				$b02 = $ord($s, 0x02, 2); // s8_set_no
				$b04 = $ord($s, 0x04, 2); // s8_set_sum
				$b06 = $ord($s, 0x06, 2); // 0 1  s8_set_st

				$s8_set_id  = $b00;
				$s8_set_no  = $b02;
				$s8_set_sum = $b04;
				$s8_set_st  = $b06;
				break;

			case 'nds_kuma': // 10
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   1 - 2   - - 4       1 - - -
			case 'wii_mura': // 10
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f
				// 2   - 1 - - 2   4       1 - - -
				$b00 = $ord($s, 0x00, 2); // s8_set_id
				$b02 = $ord($s, 0x02, 2); // s8_set_no
				$b04 = $ord($s, 0x04, 4); // s8_set_sum
				$b08 = $ord($s, 0x08, 4);
				$b0c = $ord($s, 0x0c, 2); // 0 1  s8_set_st

				$s8_set_id  = $b00;
				$s8_set_no  = $b02;
				$s8_set_sum = $b04;
				$s8_set_st  = $b0c;
				break;

			case 'psp_gran': // 14
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// 2   2   2   - - 4       4       1 - - -
			case 'vit_mura': // 14
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// 2   1 - 2   - - 4       4       1 - - -
			case 'vit_drag': // 14
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// 2   2   2   - - 4       4       1 - - -
			case 'ps3_drag': // 14
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3
				// 2   2   - - 2   4       4       1 - - -
				$b00 = $ord($s, 0x00, 2); // s8_set_id
				$b02 = $ord($s, 0x02, 2); // s8_set_no
				$b04 = $ord($s, 0x04, 4); // s8_set_sum
				$b08 = $ord($s, 0x08, 4);
				$b0c = $ord($s, 0x0c, 4);
				$b10 = $ord($s, 0x10, 2); // 0 1  s8_set_st

				$s8_set_id  = $b00;
				$s8_set_no  = $b02;
				$s8_set_sum = $b04;
				$s8_set_st  = $b10;
				break;

			case 'vit_odin': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// 2   2   3     - 4       4       1 - 1 1 1 - - -
			case 'ps3_odin': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// 2   2   - 3     4       4       2   1 1 1 - - -
			case 'ps4_drag': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// 2   2   2   - - 4       4       2   1 1 1 - - -
			case 'ps4_odin': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// 2   2   3     - 4       4       2   1 1 1 - - -
			case 'ps4_sent': // 18
				// 0 1 2 3 4 5 6 7 8 9 a b c d e f 0 1 2 3 4 5 6 7
				// 2   2   3     - 4       4       1 - 1 1 1 - - -
				$b00 = $ord($s, 0x00, 2); // s8_set_id
				$b02 = $ord($s, 0x02, 2); // s8_set_no
				$b04 = $ord($s, 0x04, 4); // s8_set_sum
				$b08 = $ord($s, 0x08, 4);
				$b0c = $ord($s, 0x0c, 4);
				$b10 = $ord($s, 0x10, 2);
				$b12 = $ord($s, 0x10, 1);
				$b13 = $ord($s, 0x10, 1); // 0 1  s8_set_st
				$b14 = $ord($s, 0x14, 2);

				$s8_set_id  = $b00;
				$s8_set_no  = $b02;
				$s8_set_sum = $b04;
				$s8_set_st  = $b13;
				break;
		} // switch ( $gp_share['tag'] )

		$data[$i] = array(
			$s8_set_id,$s8_set_no,$s8_set_sum,$s8_set_st ,
		);
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/sa.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}

function FMBS_sb( $id )
{
	global $gp_share;
	$ord = $gp_share['data']['ord'];

	if ( ! isset( $gp_share['data']['sect'][$id] ) )
	{
		php_notice('%s has no sb.txt', $gp_share['tag']);
		return array();
	}

	list($sp,$sc,$sk) = sect_head($id);
	$data = array();
	$text = '';

	for ( $i=0; $i < $sc; $i++ )
	{
		$p = $sp + ($i * $sk);
		$s = substr($gp_share['file'], $p, $sk);
		$text .= sect_data_txt($i, $s);

		switch ( $gp_share['tag'] )
		{
			case 'vit_odin':
			case 'ps3_odin':
			case 'ps4_odin':
			case 'ps4_drag':
			case 'ps4_sent':
				break;
		} // switch ( $gp_share['tag'] )
	} // for ( $i=0; $i < $sc; $i++ )

	$fn = sprintf('%s/sb.txt', $gp_share['dir']);
	save_file($fn, $text);
	return $data;
}
//////////////////////////////
function vanilla( $tag, $fname )
{
	global $gp_data, $gp_share;
	$gp_share = array(
		'tag'  => $tag,
		'data' => '',
		'file' => '',
		'dir'  => '',
		'idx'  => 0,
	);

	if ( ! isset($gp_data[$tag]) )
		return php_error('Unknown tag [%s] = %s', $tag, $fname);
	$gp_share['data'] = $gp_data[$tag];

	$gp_share['file'] = file_get_contents($fname);
	if ( empty($gp_share['file']) )  return;

	$mgc = substr($gp_share['file'], 0, 4);
	if ( $mgc !== 'FMBP' && $mgc !== 'FMBS' )
		return;

	$gp_share['dir'] = str_replace('.', '_', $fname);

	switch ( $gp_share['tag'] )
	{
		case 'ps2_grim':
		case 'ps2_odin':
		case 'nds_kuma':
		case 'wii_mura':
		case 'ps3_drag':
		case 'ps3_odin':
		case 'ps4_odin':
		case 'ps4_drag':
		case 'ps4_sent':
			$json = array(
				'tag' => $gp_share['data']['idtag'],
				'ver' => '55',
				's0' => FMBS_s0(0),
				's1' => FMBS_s1(1),
				's2' => FMBS_s2(2),
				's3' => FMBS_s3(3),
				's4' => FMBS_s4(4),
				's5' => FMBS_s5(5),
				's6' => FMBS_s6(6),
				's7' => FMBS_s7(7),
				's8' => FMBS_s8(8),
				's9' => FMBS_s9(9),
				'sa' => FMBS_sa(10),
				'sb' => FMBS_sb(11),
			);
			break;

		case 'psp_gran':
		case 'vit_mura':
		case 'vit_drag':
			$s012 = FMBS_s0s1s2(8);
			$json = array(
				'tag' => $gp_share['data']['idtag'],
				'ver' => '55',
				's0' => $s012[0],
				's1' => $s012[1],
				's2' => $s012[2],
				's3' => FMBS_s3(0),
				's4' => FMBS_s4(1),
				's5' => FMBS_s5(2),
				's6' => FMBS_s6(3),
				's7' => FMBS_s7(4),
				's8' => FMBS_s8(5),
				's9' => FMBS_s9(6),
				'sa' => FMBS_sa(7),
				'sb' => FMBS_sb(-1),
			);
			break;

		case 'vit_odin':
			$s012 = FMBS_s0s1s2(9);
			$json = array(
				'tag' => $gp_share['data']['idtag'],
				'ver' => '55',
				's0' => $s012[0],
				's1' => $s012[1],
				's2' => $s012[2],
				's3' => FMBS_s3(0),
				's4' => FMBS_s4(1),
				's5' => FMBS_s5(2),
				's6' => FMBS_s6(3),
				's7' => FMBS_s7(4),
				's8' => FMBS_s8(5),
				's9' => FMBS_s9(6),
				'sa' => FMBS_sa(7),
				'sb' => FMBS_sb(8),
			);
			break;
	} // switch ( $tag )

	$txt = json_pretty($json, '');
	save_file("$fname.v55", $txt);
	return;
}
//////////////////////////////
$err = <<<_ERR
{$argv[0]}  TAG  MBP/MBS_FILE...
TAG
  ps2_grim  2007  PS2   GrimGrimoire
  ps2_odin  2007  PS2   Odin Sphere
  nds_kuma  2008  NDS   Kumatanchi
  wii_mura  2009  Wii   Muramasa - The Demon Blade
  ps3_drag  2013  PS3   Dragon's Crown
  ps3_odin  2016  PS3   Odin Sphere Leifthsar
  ps4_odin  2016  PS4   Odin Sphere Leifthsar
  ps4_drag  2018  PS4   Dragon's Crown Pro
  ps4_sent  2019  PS4   13 Sentinels: Aegis Rim

  psp_gran  2011  PSP   Gran Knights History
  vit_mura  2013  Vita  Muramasa Rebirth + DLC
  vit_drag  2013  Vita  Dragon's Crown
  vit_odin  2016  Vita  Odin Sphere Leifthsar

  Upcoming
  swi_sent  2022  Swit  13 Sentinels: Aegis Rim
  swi_grim  2022  Swit  GrimGrimoire OnceMore
  swi_grim  2022  PS4   GrimGrimoire OnceMore

_ERR;

if ( $argc == 1 )  exit($err);
$tag = '';
for ( $i=1; $i < $argc; $i++ )
{
	if ( is_file($argv[$i]) )
		vanilla($tag, $argv[$i]);
	else
		$tag = $argv[$i];
}

/*
		switch ( $gp_share['tag'] )
		{
			case 'ps2_grim':
			case 'ps2_odin':
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

			//case 'con_name':
				break;
		} // switch ( $gp_tag )
 */
