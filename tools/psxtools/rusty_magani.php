<?php
require "common.inc";

$gp_clut = "";

function mag_decode( &$file, $w, $h, $pb1, $pb4, $pc )
{
	printf("== mgx_decode( %x , %x , %x , %x , %x )\n", $w, $h, $pb1, $pb4, $pc);
	$pix = "";
	$bycod = 0;
	$bylen = 0;

	$action = array_pad(array(), $w/8, 0);
	$actpos = 0;

	$actdx = array(0,1,2,4, 0,1, 0,1,2, 0,1,2, 0,1,2,  0);
	$actdy = array(0,0,0,0, 1,1, 2,2,2, 4,4,4, 8,8,8, 16);

	$bak = $pb4;
	while ( $pb1 < $bak )
	{
		if ( $bylen == 0 )
		{
			$bycod = ord( $file[$pb1] );
				$pb1++;
			$bylen = 8;
			printf("%6x BYTECODE %2x\n", $pb1-1, $bycod);
		}

		$flg = $bycod & 0x80;
			$bycod <<= 1;
			$bylen--;

		if ( $flg )
		{
			$act = ord( $file[$pb4] );
				$pb4++;
			$action[ $actpos ] ^= $act;
			printf("%6x ACT[%d] ^ %2x\n", $pb4-1, $actpos, $act);
		}

		printf("-- ACT %2x\n", $action[$actpos]);
		$by = array();
		$by[] = ($action[$actpos] >> 4) & BIT4;
		$by[] = ($action[$actpos] >> 0) & BIT4;
		$actpos = ($actpos + 1) % ($w/8);

		foreach ( $by as $b )
		{
			if ( $b == 0 )
			{
				$pix .= $file[$pc+0] . $file[$pc+1];
				if ( ! isset( $file[$pc+1] ) )
					return $pix;
				$pc += 2;
				printf("---- COPY %x\n", $pc-2);
			}
			else
			{
				$p = ($actdy[$b] * $w/4) + $actdx[$b];
				printf("---- REF  %x  [-%d,-%d]\n", $p, $actdx[$b], $actdy[$b]);
				$p = strlen($pix) - ($p*2);
				if ( ! isset( $pix[$p+1] ) )
					return $pix;
				$pix .= substr($pix, $p, 2);
			}
		} // foreach ( $by as $b )

	} // while (1)
	return $pix;
}

function sectmag( &$file, $fname, $pos )
{
	printf("== sectmag( $fname , %x )\n", $pos);

	$x1 = str2int($file, $pos+ 4, 2);
	$y1 = str2int($file, $pos+ 6, 2);
	$x2 = str2int($file, $pos+ 8, 2);
	$y2 = str2int($file, $pos+10, 2);
	$w = int_ceil($x2-$x1, 8);
	$h = int_ceil($y2-$y1, 8);

	$b1 = str2int($file, $pos+12, 4);
	$b2 = str2int($file, $pos+16, 4);
	$b3 = str2int($file, $pos+20, 4); // size
	$b4 = str2int($file, $pos+24, 4);
	$b5 = str2int($file, $pos+28, 4); // size

	global $gp_clut;
	$gp_clut = "";
	for ( $i=0; $i < 0x30; $i += 3 )
	{
		$p = $pos + 32 + $i;
		// in GRB order
		$gp_clut .= $file[$p+1] . $file[$p+0] . $file[$p+2] . BYTE;
	}

	$pix = mag_decode($file, $w, $h, $pos+$b1, $pos+$b2, $pos+$b4 );
	//save_file("$fname.pix", $pix);

	$sz = $w * $h / 2;
	while ( strlen($pix) < $sz )
		$pix .= ZERO;

	$clut = "CLUT";
	$clut .= chrint(16, 4);
	$clut .= chrint($w, 4);
	$clut .= chrint($h, 4);
	$clut .= $gp_clut;

	for ( $i=0; $i < $sz; $i++ )
	{
		$b = ord( $pix[$i] );
		$b1 = ($b >> 4) & BIT4;
		$b2 = ($b >> 0) & BIT4;
		$clut .= chr($b1) . chr($b2);
	}

	save_file("$fname.clut", $clut);
	return;
}
//////////////////////////////
function ani_part( &$file, $dir, $id, $pos )
{
	printf("== sectpart( $dir , $id , %x )\n", $pos);
	$w = str2int($file, $pos+2, 2) * 8;
	$h = str2int($file, $pos+4, 2);
		$pos += 6;

	$bk = $w * $h / 8;
	printf("size %x x %x = %x\n", $w, $h, $bk);

	$pix = "";
	for ( $i=0; $i < $bk; $i++ )
	{
		$b1 = ord( $file[$pos + 0*$bk] );
		$b2 = ord( $file[$pos + 1*$bk] );
		$b3 = ord( $file[$pos + 2*$bk] );
		$b4 = ord( $file[$pos + 3*$bk] );
			$pos++;

		$j = 8;
		while ( $j > 0 )
		{
			$j--;
			$b11 = ($b1 >> $j) & 1;
			$b21 = ($b2 >> $j) & 1;
			$b31 = ($b3 >> $j) & 1;
			$b41 = ($b4 >> $j) & 1;
			$bj = ($b41 << 3) | ($b31 << 2) | ($b21 << 1) | ($b11 << 0);
			$pix .= chr($bj);
		}
	} // for ( $i=0; $i < $bk; $i++ )

	global $gp_clut;
	$clut = "CLUT";
	$clut .= chrint(16, 4);
	$clut .= chrint($w, 4);
	$clut .= chrint($h, 4);
	$clut .= ( empty($gp_clut) ) ? grayclut(16) : $gp_clut;
	$clut .= $pix;

	$fn = sprintf("$dir/%04d.clut", $id);
	save_file($fn, $clut);
	return;
}

function sectani( &$file, $fname )
{
	printf("== sectani( $fname )\n");

	$dir = str_replace('.', '_', $fname);
	$cnt = str2int($file, 0, 2) / 2;
	for ( $i=0; $i < $cnt; $i++ )
	{
		$p = str2int($file, $i*2, 2);
		ani_part( $file, $dir, $i, $p );
	}
	return;
}
//////////////////////////////
function rusty( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	// for *.mag
	$mgc = substr0($file, 0, chr(0x1a));
	if ( substr($mgc, 0, 6) == "MAKI02" )
		return sectmag($file, $fname, strlen($mgc)+1);

	// for *.ani
	if ( stripos($fname, '.ani') !== false )
		return sectani($file, $fname);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	rusty( $argv[$i] );

/*
visual.com
	vs1_00.mag vs1_01.mag vs1_02.mag vs1_03.mag vs1_04.mag vs1.ani
	vs2_00.mag vs2_01.mag vs2_02.mag vs2.ani
	vs3_10.mag vs3_11.mag vs3_12.mag vs3_13.mag vs3_14.mag vs3_20.mag vs3_3.mag vs3.ani
	vs4_00.mag vs4_01.mag vs4_02.mag vs4.ani
	vs5_00.mag vs5_01.mag vs5_02.mag vs5.ani
	vs6_00.mag vs6_01.mag vs6_02.mag vs6_03.mag vs6.ani
	ed01.mag ed02.mag vsending.ani
	ed03.mag ed04.mag ed05.mag ed06.mag ed07.mag ed08.mag ed09.mag ed10.mag ed11.mag
 */
