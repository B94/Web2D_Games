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
define('SCALE', 1.0);
//define('DRY_RUN', true);

$gp_pix  = array();
$gp_clut = array();

// for 2D pixels - 256 width
function loadpix( &$file, $pos )
{
	$pix = substr ($file, $pos);
	$num = str2int($pix, 0, 2);
	printf("= loadpix( %x ) = $num\n", $pos);

	global $gp_pix;
	$gp_pix = array();
	for ( $i=0; $i < $num; $i++ )
	{
		$p = 4 + ($i * 4);
		$p = str2int($pix, $p, 4);
		$w = str2int($pix, $p+0, 2) * 2;
		$h = str2int($pix, $p+2, 2);
			$p += 4;

		$siz = $w * $h;
		$rip = substr($pix, $p, $siz);
		bpp4to8($rip);

		$gp_pix[$i]['p'] = $rip;
		$gp_pix[$i]['w'] = $w * 2;
		$gp_pix[$i]['h'] = $h;
	}
	return;
}

// for 2D pixels - variable width
function loadsrc( &$meta, $off, &$pix )
{
	$w  = ord( $meta[$off+0] );
	$h  = ord( $meta[$off+1] );
	$b8 = ord( $meta[$off+2] );
		$off += 4;

	if ( $b8 & 1 )
	{
		$siz = $w * $h;
		$src = substr($meta, $off, $siz);
	}
	else
	{
		$siz = (int)($w / 2 * $h);
		$src = substr($meta, $off, $siz);
		bpp4to8($src);
	}

	$pix['src']['w'] = $w;
	$pix['src']['h'] = $h;
	$pix['src']['pix'] = $src;
	return;
}
//////////////////////////////
function sectparts( &$meta, $off, $fn, $p256, $phdz, $pofz )
{
	$p = ord( $meta[$off] );
	$num = $p & 0x7f;
	$big = $p >> 7;
	printf("=== sectparts( %x , $fn , %d , $phdz , $pofz ) = $num & %d\n", $off, $p256, $big);
	if ( $num == 0 )
		return;

	$rx = 0; // relative
	$ry = 0;
	$rot = 0;
	$vflip = false;

	$data = array();
	$id = 0;
	$pos = $off + $phdz + ($num * $pofz);
	while ( $id < $num )
	{
		// break @ sub_8001dc10
		$b1 = ord( $meta[$pos] );
			$pos++;
		printf("%6x  %2x = ", $pos-1, $b1);

		if ( ($b1 & 0x80) == 0 ) // 0xxx xxxx
		{
			// loc_8001dd34
			// dx,dy
			if ( $big )
			{
				$dx = sint16( $meta[$pos+0] . $meta[$pos+1] );
				$dy = sint16( $meta[$pos+2] . $meta[$pos+3] );
				$pos += 4;
			}
			else
			{
				$dx = sint8( $meta[$pos+0] );
				$dy = sint8( $meta[$pos+1] );
				$pos += 2;
			}
			$m1 = array($b1, $dx, $dy, $rot, $rx, $ry, $vflip);

			// sx,sy,w,h  or  pixel data
			$p1 = $off + $phdz + ($id * $pofz);
			if ( $p256 )
			{
				$p2 = str2int($meta, $p1, 2);
				$m2 = substr ($meta, $p2, 5);
			}
			else
				$m2 = substr ($meta, $p1, 4);

			printf("part %x  id %x\n", $p1, $id);
			array_unshift($data, array($m1,$m2));
			$id++;
			$vflip = false;
			continue;
		}

		if ( ($b1 & 0x40) == 0 ) // 10xx xxxx
		{
			// loc_8001dce4
			if ( $b1 & 4 ) // 10xx x1xx
			{
				$vflip = true;
				echo "flip  "; // sw v0, 0x14(s1)
			}

			if ( $b1 & 1 ) // 10xx xxx1
			{
				$b2 = ord( $meta[$pos] );
					$pos++;
				printf("8-s1 %x  ", $b2); // sb v0, 8(s1)
			}

			if ( $b1 & 2 ) // 10xx xx1x
			{
				$b2 = ord( $meta[$pos] );
					$pos++;
				printf("9-s1 %x  ", $b2); // sb v0, 9(s1)
			}
			echo "\n";
			continue;
		}

		// ($b1 & 7) << 3

		// sub_8002332c
		$rx = 0; // sb  0, 0(v0)
		$ry = 0; // sb  0, 1(v0)
		//$v2 = 0; // sh  0, 2(v0)
		//$v4 = 0; // sh  0, 4(v0)
		$rot = 0; // sh  0, 6(v0)

		if ( $b1 & 0x20 ) // 111x xxxx
		{
			$rx = sint8( $meta[$pos+0] ); // sb v1, 0(v0)
			$ry = sint8( $meta[$pos+1] ); // sb v1, 1(a0)
				$pos += 2;
			echo "rx $rx  ry $ry  ";
		}

		if ( ($b1 & 0x10) == 0 ) // 1110 xxxx
		{
			// loc_8001dcc8
			$rot = 0; // sb 0, 6(v0)
			echo "rot 0\n";
			continue;
		}

		// 1111 xxxx
		$rot = ord( $meta[$pos+0] ); // sh a0, 6(v0)
			$pos++;
		echo "rot $rot\n";

	} // while ( $id < $num )

	$ceil = int_ceil( CANV_S * SCALE, 2 );
	$pix = copypix_def($ceil,$ceil);

	global $gp_pix, $gp_clut;
	foreach ( $data as $v )
	{
		list($m1,$dx,$dy,$rot,$rx,$ry,$vflip) = $v[0];
		$m2 = $v[1];

		if ( $rot == 0 )
		{
			$pix['rotate'] = array(0,0,0);
			$dx = (int)(($dx + $rx) * SCALE);
			$dy = (int)(($dy + $ry) * SCALE);
		}
		else
		{
			$pix['rotate'] = array($rot, (int)($dx * SCALE), (int)($dy * SCALE));
			$dx = (int)($rx * SCALE);
			$dy = (int)($ry * SCALE);
		}
		$pix['dx'] = $dx + $ceil/2;
		$pix['dy'] = $dy + $ceil/2;

		$pix['hflip'] = $m1 & 0x40;
		$pix['vflip'] = $vflip;
		$cid = $m1 & 0x0f;

		if ( $p256 )
		{
			$sx = ord( $m2[1] );
			$sy = ord( $m2[2] );
			$w  = ord( $m2[3] );
			$h  = ord( $m2[4] );

			$m20 = ord( $m2[0] );
			$tid = $m20 >> 1;
			flag_watch("m20", $m20 & 1);

			$pix['src']['w'] = $w;
			$pix['src']['h'] = $h;
			$pix['src']['pix'] = rippix8($gp_pix[$tid]['p'], $sx, $sy, $w, $h, $gp_pix[$tid]['w'], $gp_pix[$tid]['h']);
		}
		else
		{
			$m20 = str2int($m2, 0, 2) * 4;
			$m22 = str2int($m2, 2, 2);

			loadsrc($meta, $m20, $pix);
			$sx = 0;
			$sy = 0;
			$w = $pix['src']['w'];
			$h = $pix['src']['h'];
		}
		$pix['src']['pal'] = substr($gp_clut, $cid*0x40, 0x40);
		$pix['bgzero'] = 0;
		scalepix($pix, SCALE, SCALE);

		printf("%4d , %4d , %4d , %4d , %4d , %4d", $dx, $dy, $sx, $sy, $w, $h);
		printf(" , %08b , %02x\n", $m1, $m20);
		copypix($pix);
	} // foreach ( $data as $v )

	savepix($fn, $pix, true);
	return;
}
//////////////////////////////
function sectanim( &$meta, $dir )
{
	// REMOVED : use xeno_INFO_meta0.php for decoding
	return;
}

function sect1( &$file, $dir, $mp, $pp )
{
	// 2 - 3d (data , seds)
	//     4 - data (clut + texture , ??? , ??? , ???)
	// 3 - 2d (anim , parts , clut)
	// 4 - 2d (anim , parts , clut , seds)
	// 5 - 2d (anim , parts , clut , seds , wds)
	// 6 - 2d (anim , parts , clut , file , seds , wds)
	$num = str2int($file, $mp+0, 2);
	printf("=== sect1( $dir , %x , %x ) = $num\n", $mp, $pp);

	switch ( $num )
	{
		case 2:
			echo "SKIP $dir is 3D model\n";
			return;
		case 3:
		case 4:
		case 5:
		case 6:
			$p1 = str2int($file, $mp+ 4, 4);
			$p2 = str2int($file, $mp+ 8, 4);
			$p3 = str2int($file, $mp+12, 4); // palette
			$p4 = str2int($file, $mp+16, 4); // end  4+,extra
			if ( ($p4-$p3) == 4 ) // palette == 0
				return;

			$s1 = substr($file, $mp+$p1, $p2-$p1);
			$s2 = substr($file, $mp+$p2, $p3-$p2);
			$s3 = substr($file, $mp+$p3, $p4-$p3);
			//sectanim($s1, $dir);

			global $gp_clut;
			$pal = substr($s3, 4);
			$gp_clut = pal555($pal);

			$p256 = ord( $s2[1] ) >> 7;
			if ( $p256 )
			{
				echo "DETECT fixed 256 width pixels\n";
				if ( $pp == 0 )
					return printf("ERROR spr 2 + p256 = no pixel data!\n");
				loadpix($file, $pp);
				$phdz = 4;
				$pofz = 2;
			}
			else
			{
				echo "DETECT variable width pixels\n";
				if ( $s2[2] == ZERO && $s2[3] == ZERO )
					return printf("ERROR expecting int16 , get int32 = 3d models?\n");
				$phdz = 6;
				$pofz = 4;
			}

			$num = ord( $s2[0] );
			for ( $i=0; $i < $num; $i++ )
			{
				$p = 2 + ($i * 2);
				$off = str2int($s2, $p, 2);
				$fn = sprintf('%s/%04d', $dir, $i);
				sectparts( $s2, $off, $fn, $p256, $phdz, $pofz );
			}

			save_file("$dir/0.meta", $s1);
			save_file("$dir/1.meta", $s2);
			save_file("$dir/2.meta", substr($s3,4));
			return;
	}
	return;
}

function xeno( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$dir = str_replace('.', '_', $fname);
	$num = str2int($file, 0, 4);

	// sprite 2
	$end = str2int($file, 4 + ($num*4), 4);
	$dif = strlen($file) - $end;
	if ( abs($dif) < 8 )
	{
		echo "DETECT sprite 2 = $fname\n";
		sect1($file, $dir, 0, 0);
		return;
	}

	// sprite 1
	$end = str2int($file, 12, 4);
	if ( $end != 0 && str2int($file, 4, 4) == $end )
	{
		echo "DETECT sprite 1 = $fname\n";
		$off = str2int($file, 4, 4);
		for ( $i=0; $i < $num; $i++ )
		{
			$p = 8 + ($i * 12);
			$mp = str2int($file, $p+0, 4);
			$pp = str2int($file, $p+4, 4);
			if ( $pp < $off )
				continue;

			$d = ( $num == 1 ) ? $dir : "$dir/$i";
			sect1($file, $d, $mp, $pp);
		}
		return;
	}
	return;
}

for ( $i=1; $i < $argc; $i++ )
	xeno( $argv[$i] );

/*
spr1 data loaded to 801a52e0
	then appended to 8010791c
spr2 data loaded + appended to 80109db4

over 256x256 canvas
	2625 2701
mixed spr1 + spr2 / ramsus fight (+fei)
	2709
mixed spr2 + 3d models
	3838

DEBUG 2998,0111.png , 1-1ac , 2-2528
	( 898 + 2528 + 6 + (a*4) = 2dee )
*/
