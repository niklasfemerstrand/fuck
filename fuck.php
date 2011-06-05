<?php
/**
 * FUCK is an acronym for Fuck Useless Cellphone Keypads and is a project written
 * to generate alternatives to the ITU-T E.161 keypad. FUCK opens a textfile,
 * counts the occurance of each character and generates a keypad optimized for the
 * input text.
 *
 * Alphabetical keypads, such as the ITU-T E.161 standard keypad, is nowhere
 * a sufficient way of writing. For more information about the ITU-T E.161
 * standard, please review the links below:
 *
 *   http://www.itu.int/rec/T-REC-E.161-200102-I/en
 *   https://secure.wikimedia.org/wikipedia/en/wiki/E.161
 *
 * Copyright (c) 2011 Niklas A. Femerstrand <qnrq@pipemail.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if(!defined('STDIN'))
	die("Please run FUCK in a CLI environment.\n");

echo "Path (local or url) to the file to analyze: ";
while (FALSE !== ($file = fgets(STDIN))) {
   break;
}

$file = preg_replace("/\r|\n/", "", $file);
$lorem = strtolower(file_get_contents($file)) or die('404\n');

// Standard ITU-T E.161 alphabetical keypad
$matrix = array(1 => " ",    2 => "abc",   3 => "def",
				4 => "ghi",  5 => "jkl",   6 => "mno",
				7 => "pqrs", 8 => "tuv",   9 => "wxyz");

// Button presses required for each letter
//@todo auto generate this array so we can measure statistics with the newly
//      generated layout and compare the two
$presses = array(" " => 1, "a" => 1, "b" => 2, "c" => 3,
				 "d" => 1, "e" => 2, "f" => 3, "g" => 1,
				 "h" => 2, "i" => 3, "j" => 1, "k" => 2,
				 "l" => 3, "m" => 1, "n" => 2, "o" => 3,
				 "p" => 1, "q" => 2, "r" => 3, "s" => 4,
				 "t" => 1, "u" => 2, "v" => 3, "w" => 1,
				 "x" => 2, "y" => 3, "z" => 4);

/**
 * $newLayout[x][y], x = keypad number
 *                   y = letter y from list ordered by usage
 */
$newLayout = array(1 => array(0,3,18),   2 => array(1,4,19),
				   3 => array(2,5,20),   4 => array(6,9,21),
				   5 => array(7,10,22),  6 => array(8,11,23),
				   7 => array(12,15,24), 8 => array(13,16,25),
				   9 => array(14,17,26));

$totalClicks = 0;
$usage = array();

foreach($matrix as $val)
{
	$arr = str_split($val);

	foreach($arr as $v)
	{
		$occurances = substr_count($lorem, $v);
		$total += $occurances * $presses[$v];
		$usage[$occurances] = $v;
		echo "{$v}: {$occurances}\n";
	}
}

echo "=================================\n";
echo "Clicks required on alphabetical keypad: {$total}\n\n";
echo "Generated keypad:\n\n";

// Generate new layout order and sort per $newLayout
krsort($usage);
$usage = array_values($usage);
$b = 0;
$newMatrix = array();

foreach($newLayout as $x)
{
	$b++;
	echo "  {$b}: ";

	foreach($x as $y)
	{
		$newMatrix[$b] .= $usage[$y];
		echo $usage[$y];
	}

	echo "\n";
}