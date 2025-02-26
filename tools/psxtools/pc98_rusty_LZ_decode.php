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
require 'class-bakfile.inc';

define('NO_TRACE', true);

function rusty_decode( &$file, $st )
{
	$dicz = 0xfff;
	$dicp = 0xfee;
	$dict = str_repeat(ZERO, $dicz+1);
	$dec = '';

	$ed = strlen($file);
	$bylen = 0;
	$bycod = 0;
	while ( $st < $ed )
	{
		trace("%6x  %6x  ", $st, strlen($dec));
		if ( $bylen == 0 )
		{
			$bycod = ord( $file[$st] );
				$st++;
			trace("BYTECODE %2x\n", $bycod);
			$bylen = 8;
			continue;
		}

		$flg = $bycod & 1;
			$bycod >>= 1;
			$bylen--;

		if ( $flg )
		{
			$b1 = $file[$st];
				$st++;
			trace("COPY %2x\n", ord($b1));

			$dec .= $b1;
			$dict[$dicp] = $b1;

			$dicp = ($dicp + 1) & $dicz;
		}
		else
		{
			$b1 = ord( $file[$st+0] );
			$b2 = ord( $file[$st+1] );
				$st += 2;
			$len =  ($b2 & 0x0f) + 3;
			$pos = (($b2 & 0xf0) << 4) | $b1;
			trace("DICT %3x LEN %2x\n", $pos, $len);

			for ( $i=0; $i < $len; $i++ )
			{
				$p = ($pos + $i) & $dicz;
				$b1 = $dict[$p];

				$dec .= $b1;
				$dict[$dicp] = $b1;

				$dicp = ($dicp + 1) & $dicz;
			}
		}
	} // while ( $st < $ed )

	$file = $dec;
	return;
}
//////////////////////////////
function rusty( $fname )
{
	$bak = new BakFile;
	$bak->load($fname);
	if ( $bak->is_empty() )
		return;

	if ( substr($bak->file, 0, 2) !== 'LZ' )
		return;
	rusty_decode( $bak->file, 7 );

	printf("%8x -> %8x  %s\n", $bak->filesize(0), $bak->filesize(1), $fname);
	$bak->save();
	return;
}

for ( $i=1; $i < $argc; $i++ )
	rusty( $argv[$i] );

/*
Neko Project 2 GDC clock 5 MHz error
	- restart emulator and hold End/Help key -> BIOS screen
	- select DIP switch 2 settings
	- change GDC clock from 5 MHz to 2.5 MHz
	- exit
 */
