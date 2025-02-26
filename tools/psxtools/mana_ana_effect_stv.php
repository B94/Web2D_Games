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

$gp_tim = array();

// callback for copypix()
function stv_alp( $fg, $bg )
{
	return alpha_add( $fg, $bg );
}
//////////////////////////////
function sectanim( &$meta, $id, $pos, $flg )
{
	$num = ord($meta[$pos]);
		$pos++;
	if ( $num == 0 )
		return '';

	$ret = array();
	for ( $i=0; $i < $num; $i++ )
	{
		$p = $pos + ($i * 2);
		$b1 = ord($meta[$p+0]);
		$b2 = ord($meta[$p+1]);
		$ret[] = "$b1-$b2";
	}
	if ( $flg )
		$ret[] = 'flag';

	$buf = "anim_{$id} = ";
	$buf .= implode(' , ', $ret);
	return "$buf\n";
}

function sectparts( &$meta, $pos, $fn )
{
	$num = ord( $meta[$pos] );
		$pos++;
	printf("=== sectparts( %x, $fn ) = $num\n", $pos);
	if ( $num == 0 )
		return;

	$data = array();
	while ( $num > 0 )
	{
		$num--;
		$p7 = ord( $meta[$pos+7] );
		if ( ($p7 & 0x21) == 0 )
		{
			$s = substr($meta, $pos, 9);
			array_unshift($data, $s);
		}
		$pos += 9;
	}
	if ( empty($data) )
		return;

	$pix = copypix_def(CANV_S,CANV_S);

	//global $gp_pix, $gp_clut;
	global $gp_tim;
	foreach ( $data as $v )
	{
		zero_watch('v8', $v[8]);

		// 0  1  2  3  4 5 6 7 8
		// dx dy sx sy w h c f -
		$dx = sint8( $v[0] );
		$dy = sint8( $v[1] );
		$pix['dx'] = $dx + (CANV_S / 2);
		$pix['dy'] = $dy + (CANV_S / 2);

		$sx = ord( $v[2] );
		$sy = ord( $v[3] );
		$w  = ord( $v[4] );
		$h  = ord( $v[5] );
		$cn = ord( $v[6] );

		$pix['src']['w'] = $w;
		$pix['src']['h'] = $h;
		$pix['src']['pix'] = rippix8($gp_tim['pix'], $sx, $sy, $w, $h, $gp_tim['w'], $gp_tim['h']);
		$pix['src']['pal'] = $gp_tim['pal'][$cn];
		$pix['bgzero'] = 0;

		$p7 = ord( $v[7] );
		$pix['vflip'] = $p7 & 0x80;
		$pix['hflip'] = $p7 & 0x40;
		$pix['alpha'] = '';
		if ( $p7 & 2 ) // mask
			$pix['alpha'] = 'stv_alp';
		flag_watch('v7', $p7 & 0x3c);

		printf("%4d , %4d , %4d , %4d , %4d , %4d", $dx, $dy, $sx, $sy, $w, $h);
		printf(" , $cn\n");
		copypix($pix);
	} // foreach ( $data as $v )

	savepix($fn, $pix, true);
	return;
}

function mana( $fname )
{
	// for /ANA/EFFECT/*/*.STV + /ANA/EFFECT/RENEF05/WAZA*.SV
	//if ( stripos($fname, ".stv") == false )
		//return;

	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	$dir = str_replace('.', '_', $fname);

	global $gp_tim;
	$off1 = str2int($file, 0, 2);
	$off2 = str2int($file, 4, 2);
	$tim = substr($file, $off1, $off2-$off1);
	$gp_tim = psxtim($tim);

	$off1 = str2int($file, 4, 2);
	$off2 = str2int($file, 6, 2);
	$meta = substr($file, $off1, $off2-$off1);
	//save_file("$dir/meta", $meta);

	$off = str2int($meta, 2, 2);

	// sprite parts data
	$cnt = str2int($meta, $off, 2);
	for ( $i=0; $i < $cnt; $i++ )
	{
		$p = $off + 2 + ($i * 2);
		$p = str2int($meta, $p, 2);
		$fn = sprintf('%s/%04d', $dir, $i);
		sectparts($meta, $p, $fn, $i);
	}

	// sprite animation sequence
	$ed = $off;
	$st = 6;
	$buf = '';
	$m = 0;
	while ( $st < $ed )
	{
		$b1 = str2int($meta, $st, 2);
		$pos = $b1 & 0x7fff;
		$flg = $b1 >> 15;

		$buf .= sectanim($meta, $m, $pos, $flg);
		$st += 2;
		$m++;
	}
	save_file("$dir/anim.txt", $buf);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	mana( $argv[$i] );

/*
more than 1 parts
  antz2.stv
  govz2.stv
  govz3.stv
  vv442.stv
  vv451.stv
  vv471.stv
  vv472.stv
  vv512.stv
  vv531.stv
  vv532.stv
  vv541.stv
  vv544.stv
  vv672.stv
  s3rlv1.stv
  s3ulv1.stv
  vv84.stv
  vv85.stv
  tanken18.stv
  tanke18n.stv
  hammer17.stv
  yari16.stv
  yari17.stv
 */
