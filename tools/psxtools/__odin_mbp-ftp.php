<?php
/*
[license]
[/license]
 */
require "common.inc";
require "common-guest.inc";
require "common-quad.inc";

define("SCALE", 1.0);
//define("DRY_RUN", true);

$gp_pix = array();

function sectquad( &$mbp, $pos, $name, $SCALE )
{
	$qax = str2int($mbp, $pos+ 0, 2, true);
	$qay = str2int($mbp, $pos+ 2, 2, true);
	$qbx = str2int($mbp, $pos+ 4, 2, true);
	$qby = str2int($mbp, $pos+ 6, 2, true);
	$qcx = str2int($mbp, $pos+ 8, 2, true);
	$qcy = str2int($mbp, $pos+10, 2, true);
	$qdx = str2int($mbp, $pos+12, 2, true);
	$qdy = str2int($mbp, $pos+14, 2, true);
	$qex = str2int($mbp, $pos+16, 2, true);
	$qey = str2int($mbp, $pos+18, 2, true);
	$qfx = str2int($mbp, $pos+20, 2, true);
	$qfy = str2int($mbp, $pos+22, 2, true);
	$qgx = str2int($mbp, $pos+24, 2, true);
	$qgy = str2int($mbp, $pos+26, 2, true);
	$qhx = str2int($mbp, $pos+28, 2, true);
	$qhy = str2int($mbp, $pos+30, 2, true);

	if ( $qcx != $qgx )
		php_notice("qcx != qgx [%x,%x]", $qcx, $qgx);
	if ( $qcy != $qgy )
		php_notice("qcy != qgy [%x,%x]", $qcy, $qgy);
	if ( $qhx != 0 || $qhy != 0 )
		php_notice("qhx,qhy not zero [%x,%x]", $qhx, $qhy);

	$inv16 = 1.0 / 0x10;
	// qbx,qby is the center point of quad cdef
	//  qbx == average(qcx,qex)
	//  qby == average(qcy,qey)
	$qcx *= $SCALE * $inv16;
	$qcy *= $SCALE * $inv16;
	$qdx *= $SCALE * $inv16;
	$qdy *= $SCALE * $inv16;
	$qex *= $SCALE * $inv16;
	$qey *= $SCALE * $inv16;
	$qfx *= $SCALE * $inv16;
	$qfy *= $SCALE * $inv16;

	$cdef = array(
		array($qcx,$qcy,1),
		array($qdx,$qdy,1),
		array($qex,$qey,1),
		array($qfx,$qfy,1),
	);

	printf("== sectquad( %x , $name , %.2f )\n", $pos, $SCALE);
	printf("    a %7d,%7d  \n", $qax, $qay);
	printf("    b %7.2f,%7.2f  \n", $qbx*$inv16, $qby*$inv16);
	quad_dump($cdef, "1423", "cdef");
	return $cdef;
}

function load_tm2( &$pix, $tid , $pfx )
{
	global $gp_pix;
	if ( ! isset( $gp_pix[$tid] ) )
	{
		$fn = sprintf("%s.%d.tm2", $pfx, $tid);
		$ftp = load_clutfile($fn);
		if ( $ftp === 0 )
			return php_error("NOT FOUND %s", $fn);

		$gp_pix[$tid] = array();
		if ( isset( $ftp['cc'] ) )
		{
			$gp_pix[$tid]['w'] = $ftp['w'];
			$gp_pix[$tid]['h'] = $ftp['h'];
			$gp_pix[$tid]['d'] = clut2rgba($ftp['pal'], $ftp['pix'], false);
		}
		else
		{
			$gp_pix[$tid]['w'] = $ftp['w'];
			$gp_pix[$tid]['h'] = $ftp['h'];
			$gp_pix[$tid]['d'] = $ftp['pix'];
		}
	} // if ( ! isset( $gp_pix[$tid] ) )

	printf("== load_tm2( $tid , $pfx ) = %x x %x\n", $gp_pix[$tid]['w'], $gp_pix[$tid]['h']);
	$pix['src']['w'] = $gp_pix[$tid]['w'];
	$pix['src']['h'] = $gp_pix[$tid]['h'];
	$pix['src']['pix'] = &$gp_pix[$tid]['d'];
	$pix['src']['pal'] = "";
	return;
}
//////////////////////////////
function sectpart( &$mbp, $dir, $pfx, $id6, $no6 )
{
	printf("== sectpart( $dir , $pfx , %x , %x )\n", $id6, $no6);

	// ERROR : computer run out of memory
	// required CANV_S is too large for *.mbp
	//   auto canvas size detection
	//   auto move center point 0,0 from middle-center to top-left
	//   auto trim is DISABLED
	$data = array();
	$CANV_S = 0;
	$is_mid = false;
	for ( $i4=0; $i4 < $no6; $i4++ )
	{
		$p4 = ($id6 + $i4) * $mbp[4]['k'];

		// 0 1 2  3    4   6 8 c  10    12    14    16
		// - - -  tid  s1  - - -  s2-0  s2-6  s2-c  s2-2
		//$u0 = str2int($mbp[4]['d'], $p4+ 0, 1);
		//$u1 = str2int($mbp[4]['d'], $p4+ 1, 1);
		//$u2 = str2int($mbp[4]['d'], $p4+ 2, 1);

		$tid = str2int($mbp[4]['d'], $p4+ 3, 1);
		$s1  = str2int($mbp[4]['d'], $p4+ 4, 2); // sx,sy
		$s2  = str2int($mbp[4]['d'], $p4+16, 2); // dx,dy
		//if ( $s1 == 0 )
			//continue;

		$sqd = sectquad($mbp[1]['d'], $s1*$mbp[1]['k'], "mbp 1 $s1", 1);
		$dqd = sectquad($mbp[2]['d'], $s2*$mbp[2]['k'], "mbp 2 $s2", SCALE);
		$dv = array($tid, $sqd, $dqd);
		$data[] = $dv;
		//array_unshift($data, $dv);
		printf("DATA  %d\n", $tid);

		// detect origin and canvas size
		for ( $i=0; $i < 4; $i++ )
		{
			$s1 = abs( $dqd[$i][0] );
			$s2 = abs( $dqd[$i][1] );
			if ( $s1 > $CANV_S )  $CANV_S = $s1 + 1;
			if ( $s2 > $CANV_S )  $CANV_S = $s2 + 1;
			if ( $dqd[$i][0] < 0 || $dqd[$i][1] < 0 )
				$is_mid = true;
		} // for ( $i=0; $i < 4; $i++ )
		printf("CANV_S  %d\n", $CANV_S);

	} // for ( $i4=0; $i4 < $no6; $i4++ )
	if ( empty($data) )
		return;

	$ceil = ( $is_mid ) ? int_ceil($CANV_S*2, 16) : int_ceil($CANV_S, 16);
	$pix = COPYPIX_DEF();
	$pix['rgba']['w'] = $ceil;
	$pix['rgba']['h'] = $ceil;
	$pix['rgba']['pix'] = canvpix($ceil,$ceil);

	$origin = ( $is_mid ) ? $ceil / 2 : 0;
	printf("ORIGIN  %d\n", $origin);

	foreach ( $data as $dv )
	{
		list($tid, $sqd, $dqd) = $dv;

		$pix['alpha'] = "alpha_over";

		$pix['src']['vector'] = $sqd;
		for ( $i=0; $i < 4; $i++ )
		{
			$dqd[$i][0] += $origin;
			$dqd[$i][1] += $origin;
		}
		$pix['vector'] = $dqd;

		load_tm2($pix, $tid, $pfx);
		copyquad($pix, 4);
	} // foreach ( $data as $dv )

	savepix($dir, $pix, false);
	return;
}

function sectspr( &$mbp, $pfx )
{
	// s6-s4-s1,s2 [18-18-20,20]
	$len6 = strlen( $mbp[6]['d'] );
	for ( $i6=0; $i6 < $len6; $i6 += $mbp[6]['k'] )
	{
		// 0 4 8 c  10 11  12 13  14  15 16 17
		// - - - -  id     -  -   no  -  -  -
		$id6 = str2int($mbp[6]['d'], $i6+0x10, 2);
		$no6 = str2int($mbp[6]['d'], $i6+0x14, 1);
		if ( $no6 == 0 )
			continue;

		$dir = sprintf("$pfx/%04d", $i6/$mbp[6]['k']);
		sectpart($mbp, $dir, $pfx, $id6, $no6);

	} // for ( $i6=0; $i6 < $len6; $i6 += $mbp[6]['k'] )
	return;
}
//////////////////////////////
function sectanim( &$mbp, $pfx )
{
	$anim = "";
	// s9-sa-s8 [30-8-20]
	$len9 = strlen( $mbp[9]['d'] );
	for ( $i9=0; $i9 < $len9; $i9 += $mbp[9]['k'] )
	{
		// 0 4 8 c  10
		// - - - -  name
		// 28 29  2a  2b 2c 2d 2e 2f
		// id     no  -  -  -  -  -
		$name = substr0($mbp[9]['d'], $i9+0x10);
		$id9  = str2int($mbp[9]['d'], $i9+0x28, 2);
		$no9  = str2int($mbp[9]['d'], $i9+0x2a, 1);

		for ( $ia=0; $ia < $no9; $ia++ )
		{
			$pa = ($id9 + $ia) * $mbp[10]['k'];

			// 0 1  2 3  4 5 6 7
			// id   no   - - - -
			$ida = str2int($mbp[10]['d'], $pa+0, 2);
			$noa = str2int($mbp[10]['d'], $pa+2, 2);

			$ent = array();
			for ( $i8=0; $i8 < $noa; $i8++ )
			{
				$p8 = ($ida + $i8) * $mbp[8]['k'];

				// 0   2 4  6   8 c 10 14 18 1c
				// id  - -  no  - - -  -  -  -
				$id8 = str2int($mbp[8]['d'], $p8+0, 2);
				$no8 = str2int($mbp[8]['d'], $p8+6, 2);

				$ent[] = "$id8-$no8";

			} // for ( $i8=0; $i8 < $noa; $i8++ )

			$anim .= sprintf("%s_%d = ", $name, $ia);
			$anim .= implode(' , ', $ent);
			$anim .= "\n";

		} // for ( $ia=0; $ia < $no9; $ia++ )

	} // for ( $i9=0; $i9 < $len9; $i9 += $mbp[9]['k'] )

	save_file("$pfx/anim.txt", $anim);
	return;
}
//////////////////////////////
function mbpdbg( &$meta, $name, $blk )
{
	printf("== mbpdbg( $name , %x )\n", $blk);
	$buf = debug_block( $meta, $blk );
	//echo "$buf\n";
	save_file("$name.txt", $buf);
	return;
}

function loadmbp( &$mbp, $sect, $pfx )
{
	$offs = array();
	$offs[] = strrpos($mbp, "FEOC");
	foreach ( $sect as $k => $v )
	{
		$b1 = str2int($mbp, $v['p'], 4);
		if ( $b1 == 0 )
			continue;
		$offs[] = $b1;
		$sect[$k]['o'] = $b1;
	}
	sort($offs);

	foreach ( $sect as $k => $v )
	{
		if ( ! isset( $v['o'] ) )
			continue;
		$id = array_search($v['o'], $offs);
		$sz = int_floor($offs[$id+1] - $v['o'], $v['k']);
		$dat = substr($mbp, $v['o'], $sz);

		//save_file("$pfx/meta/$k.meta", $dat);
		mbpdbg($dat, "$pfx/meta/$k", $v['k']);

		$sect[$k]['d'] = $dat;
	} // foreach ( $sect as $k => $v )

	$mbp = $sect;
	return;
}
//////////////////////////////
function odin( $fname )
{
	$mbp = load_file($fname);
	if ( empty($mbp) )  return;

	if ( substr($mbp,0,4) != "FMBP" )
		return;

	if ( str2int($mbp, 8, 4) != 0xa0 )
		return printf("DIFF not 0xa0  %s\n", $fname);

	// $siz = str2int($mbp, 4, 3);
	// $hdz = str2int($mbp, 8, 3);
	// $len = 0x10 + $hdz + $siz;
	$pfx = substr($fname, 0, strrpos($fname, '.'));

	global $gp_pix;
	$gp_pix = array();

	//   0 1 2 |     1-0 2-1 3-2
	// 3 4 5 6 | 6-3 5-4 9-5 7-6
	// 7 8 9 a | 8-7 4-8 a-9 s-a
	// staff_dummy.mbp
	//        a0 1a0 1e0 |      8*20 2*20 8*20
	//     - 550   - 2e0 |    - 2*18    - 2*18
	//   310 370 580 850 | 2*30 f*20 f*30 d*8
	// s9[+28] = c+1 => sa
	// sa[+ 0] = e+1 => s8
	// s8[]
	//
	// gwendlyn.mbp
	//            a0   6fa0   8260 |         378*20  96*20 9078*20
	// 129160 157080 19a490 12dee0 |  f8*50 2cd6*18 209*8   376*18
	// 1331f0 13b440 19b4d8 19da58 | 2b7*30  de2*20  c8*30  193*8
	// s9[+28] =  190+3  => sa
	// sa[+ 0] =  ddf+3  => s8
	// s8[+ 0] =  375    => s6 , [+ 4] = 2b6
	// s6[+10] = 2cb9+1d => s4 , [+12] = 208+1 => s5
	// s4[]
	// s5[+ 0] =   f7    => s3
	//
	// s9-sa-s8-s6-s4-?
	$sect = array(
		array('p' => 0x54 , 'k' => 0x20), // 0
		array('p' => 0x58 , 'k' => 0x20), // 1
		array('p' => 0x5c , 'k' => 0x20), // 2
		array('p' => 0x60 , 'k' => 0x50), // 3 area=0
		array('p' => 0x64 , 'k' => 0x18), // 4
		array('p' => 0x68 , 'k' => 0x08), // 5 area=0
		array('p' => 0x6c , 'k' => 0x18), // 6
		array('p' => 0x70 , 'k' => 0x30), // 7
		array('p' => 0x74 , 'k' => 0x20), // 8
		array('p' => 0x78 , 'k' => 0x30), // 9
		array('p' => 0x7c , 'k' => 0x08), // 10
	);
	loadmbp($mbp, $sect, $pfx);

	sectanim($mbp, $pfx);
	sectspr ($mbp, $pfx);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	odin( $argv[$i] );

/*
 */
