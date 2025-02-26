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

define('CANV_S', 0x200);
//define('DRY_RUN', true);

// servant/tt_xxx.bin are loaded to RAM 80170000
// servant/ft_xxx.bin are loaded to RAM 8007efec
// bin/weaponx.bin    are loaded to RAM 8017a000
// offsets here are RAM pointers
function ramint( &$file, $pos, $ram )
{
	$int = str2int($file, $pos, 3);
	if ( $int )
		$int -= $ram;
	else
		printf("ERROR ramint zero @ %x\n", $pos);
	return $int;
}
//////////////////////////////
function sectparts( &$meta, &$src, &$clut, $pos, $dir )
{
	$num = ord( $meta[$pos+0] );
		$pos += 4;
	printf("=== sectparts( %x, $dir ) = $num\n", $pos);

	$data = array();
	for ( $i=0; $i < $num; $i++ )
	{
		$bin = substr($meta, $pos, 0x16);
		array_unshift($data, $bin);
		$pos += 0x16;
	}
	if ( empty($data) )
		return;

	$pix = copypix_def(CANV_S,CANV_S);

	foreach ( $data as $v )
	{
		// 0  2  4 6 8   a   c  e  10 12 14
		// dx dy w h cid tid x1 y1 x2 y2 f
		$dx = sint16( $v[0] . $v[1] );
		$dy = sint16( $v[2] . $v[3] );
		$pix['dx'] = $dx + (CANV_S / 2);
		$pix['dy'] = $dy + (CANV_S / 2);

		$w  = str2int($v,  4, 2);
		$h  = str2int($v,  6, 2);
		$sx = str2int($v, 12, 2);
		$sy = str2int($v, 14, 2);

		$cid = str2int($v,  8, 2);
		$tid = str2int($v, 10, 2);

		$pix['src']['w'] = $w;
		$pix['src']['h'] = $h;
		$pix['src']['pix'] = rippix8($src[$tid], $sx, $sy, $w, $h, 0x80, 0x80);
		$pix['src']['pal'] = substr($clut, $cid*0x40, 0x40);
		$pix['src']['pal'][3] = ZERO;
		//$pix['bgzero'] = 0;

		$v20 = str2int($v, 20, 2);
		//$pix['vflip'] = $v20 & 2;
		//$pix['hflip'] = $v20 & 4;

		printf("%4d , %4d , %4d , %4d , %4d , %4d", $dx, $dy, $sx, $sy, $w, $h);
		printf(" , $cid , $tid , %04x\n", $v20);
		copypix_fast($pix);
	} // foreach ( $data as $v )

	savepix($dir, $pix, true);
	return;
}

function sectmeta( &$meta, &$src, $dir, $ram )
{
	printf("=== sectmeta( $dir , %x )\n", $ram);

	$pos = ramint($meta, 0x40, $ram);
	if ( $pos != 0 )
		return printf("ERROR 0x40 is not ZERO\n");

	$addr = array();
	$st = 0x44;
	$clut_pos = 0;
	while (1)
	{
		if ( $meta[$st+3] !== "\x80" )
			break;
		$pos = ramint($meta, $st, $ram);
			$st += 4;

		$addr[]   = $pos;
		$clut_pos = $pos;
	} // while (1)

	$num = ord( $meta[$clut_pos] );
	$clut_pos += (4 + $num * 0x16);
	while ( $clut_pos % 4 )
		$clut_pos++;
	printf("add CLUT @ %x\n", $clut_pos);
	$clut = substr($meta, $clut_pos, 0x20*0x100);
		$clut = pal555($clut);

	foreach ( $addr as $ak => $av )
	{
		$fn = sprintf('%s/%04d', $dir, $ak);
		sectparts( $meta, $src, $clut, $av, $fn );
	}
	return;
}
//////////////////////////////
function sotn( $dir )
{
	if ( ! is_dir($dir) )
		return;
	if ( ! file_exists("$dir/setup.txt") )
		return;

	$setup = array();
	foreach ( file("$dir/setup.txt") as $v )
	{
		$v = preg_replace('|[\s]+|', '', $v);
		if ( empty($v) )
			continue;
		list($k,$v) = explode('=', $v);
		$setup[$k] = $v;
	}

	$file = file_get_contents("$dir/serv.1");
	$src = array();
	$src[] = rippix4($file,    0, 0, 0x80, 0x80, 0x100, 0x80);
	$src[] = rippix4($file, 0x80, 0, 0x80, 0x80, 0x100, 0x80);
	if ( isset( $file[0x4000] ) )
	{
		$file = substr($file, 0x4000);
		$src[] = rippix4($file, 0, 0, 0x80, 0x80, 0x80, 0x80);
	}
	$meta = file_get_contents("$dir/serv.2");

	$ram = hexdec( $setup['ramint'] );
	sectmeta($meta, $src, $dir, $ram);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	sotn( $argv[$i] );
