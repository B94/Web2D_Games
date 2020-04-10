<?php
require "common.inc";

define("CANV_S", 0x180);
$gp_tim = array();

function sint16( $s )
{
	$int = ordint($s);
	if ( $int >> 15 )
		return $int - BIT16 - 1;
	return $int;
}

function loadtim( $tim_fn )
{
	$file = file_get_contents($tim_fn);
	if ( empty($file) )  return;

	global $gp_tim;
	$gp_tim = psxtim($file);
	return;
}

function bg_alp( $pix, $div )
{
	$r = ord($pix[0]) / $div;
	$g = ord($pix[1]) / $div;
	$b = ord($pix[2]) / $div;

	$a = ( $r > $g ) ? $r : $g;
	$a = ( $b > $a ) ? $b : $a;
	$alp = $a / BIT8;

	$r = int_max($r / $alp, BIT8);
	$g = int_max($g / $alp, BIT8);
	$b = int_max($b / $alp, BIT8);
	return chr($r) . chr($g) . chr($b) . chr($a);
}

// callback for copypix()
function wm_alp3( $fg, $bg )
{
	$nfg = "";
	for ( $i=0; $i < 4; $i++ )
	{
		$p1 = ord( $fg[$i] );
		$p1 = (int)($p1 / 5);
		$nfg .= chr($p1);
	}
	return alpha_add( $nfg, $bg );
}

// callback for copypix()
function wm_alp1( $fg, $bg )
{
	return alpha_add( $fg, $bg );
}
//////////////////////////////
function sect1( &$file, $off, $fn )
{
	printf("=== sect1( %x , %s )\n", $off, $fn);
	$num = ord( $file[$off] );
		$off++;
	echo "num $num\n";
	if ( $num == 0 || $num & 0x80 )
		return;

	$data = array();
	while ( $num )
	{
		$num--;
		if ( $file[$off+0] != BYTE || $file[$off+1] != BYTE )
		{
			$s = substr($file, $off, 12);
			array_unshift($data, $s);
		}
		$off += 12;
	}
	if ( empty($data) )  return;

	$pix = COPYPIX_DEF;
	$pix['rgba']['w'] = CANV_S;
	$pix['rgba']['h'] = CANV_S;
	$pix['rgba']['pix'] = canvpix(CANV_S,CANV_S);

	global $gp_tim;
	foreach ( $data as $v )
	{
		zero_watch( "v7", $v[7] );

		// 0   1   2  3  4 5 6  7 8 9 a   b
		// dx1 dy1 sx sy w h cn - r - dx2 dy2
		$dx = sint16( $v[0] . $v[10] );
		$pix['dx'] = $dx + (CANV_S / 2);

		$dy = sint16( $v[1] . $v[11] );
		$pix['dy'] = $dy + (CANV_S / 2);

		$sx = ord($v[2]);
		$sy = ord($v[3]);
		$w  = ord($v[4]);
		$h  = ord($v[5]);
		$cn = ord($v[6]);

		$pix['src']['w'] = $w;
		$pix['src']['h'] = $h;
		$pix['src']['pix'] = rippix8($gp_tim['pix'], $sx, $sy, $w, $h, $gp_tim['w'], $gp_tim['h']);
		$pix['src']['pal'] = $gp_tim['clut'][$cn];

		$pix['rotate'] = 0x100 - ord($v[8]);

		$p9 = ord($v[9]);
		$pix['alpha'] = "";
		if ( $p9 == 1 ) // mask / 1 + image
			$pix['alpha'] = "wm_alp1";
		if ( $p9 == 3 ) // mask / 5 + image
			$pix['alpha'] = "wm_alp3";

		printf("%d , %d , $sx , $sy , $w , $h , $cn , %d , $p9\n",
			$pix['dx'], $pix['dy'], $pix['rotate']);
		copypix($pix);
	} // foreach ( $data as $v )

	savpix($fn, $pix);
	return;
}

function mana( $fname )
{
	// for /wm/wm*/*.dat and /wm/wm*/*.tim pair
	$pfx = substr($fname, 0, strrpos($fname, '.'));
	if ( ! file_exists("$pfx.dat") )  return;
	if ( ! file_exists("$pfx.tim") )  return;

	$file = file_get_contents("$pfx.dat");
	if ( empty($file) )  return;
	echo "\n=== $pfx.dat/tim pair ===\n";

	loadtim("$pfx.tim");
	$dir = "{$pfx}_dattim";

	$prv = 0;
	$st = 0x40;
	$id = 1;
	while (1)
	{
		$off = str2int($file, $st, 2);
		if ( $off == 0 )
			return;
		if ( ! isset($file[$off]) )
			return;
		if ( $off < $prv )
			return;

		$fn = sprintf("$dir/%04d", $id);
		sect1( $file, $off, $fn );

		$prv = $off;
		$st += 2;
		$id++;
	}
	return;
}

for ( $i=1; $i < $argc; $i++ )
	mana( $argv[$i] );

/*
v7 != ZERO
	npckan00
	npckan01
v8 != ZERO
	drl2_etc
	esl_af
	hel_af
	jul_lan
	min_af/s
	roah1
v9 != ZERO
	dom_af
	fig_af
	manal
	mek_af/bom/lan
	noruaf
	roah1/2
 */
