<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A4_non_embedded certificate type
 *
 * @package    mod
 * @subpackage certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}

$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
$x = 10;
$y = 30;
$sealx = 250;
$sealy = 165;
$sigx = 47;
$sigy = 165;
$custx = 47;
$custy = 155;
$wmarkx = 40;
$wmarky = 31;
$wmarkw = 228;
$wmarkh = 117;
$brdrx = 0;
$brdry = 0;
$brdrw = 297;
$brdrh = 210;
$codey = 175;

// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);

// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(61, 61, 60);
$user_name = fullname($USER);
$modifier = 1;
$user_name_length = mb_strlen($user_name);
$min_user_length_for_modifier = 25;
if($user_name_length > $min_user_length_for_modifier){
  $modifier = 1 + $user_name_length / $min_user_length_for_modifier;
}

certificate_print_text($pdf, $x, $y + 45, 'C', 'helvetica', '', 45 - (5 * $modifier), $user_name);

$pdf->SetTextColor(0, 176, 240);
$course_name = format_string($course->fullname);
$modifier = 1;
$course_name_length = mb_strlen($course_name);
$min_course_length_for_modifier = 45;
if($course_name_length > $min_course_length_for_modifier){
  $modifier = 1 + $course_name_length / $min_course_length_for_modifier;
}
certificate_print_text($pdf, $x, $y + 80, 'C', 'robotolighti', '', 40 - (5 * $modifier), $course_name);

$date_course_h = 120;
$date_course_text = 20;
certificate_print_text($pdf, $x + 11, $y + $date_course_h, 'L', 'praxisltlight', '', $date_course_text, get_string('coursegrade', 'certificate') . ':');
certificate_print_text($pdf, $x + 215, $y + $date_course_h, 'L', 'praxisltlight', '', $date_course_text, 'Date');


$pdf->SetTextColor(61, 61, 60);
$date = certificate_get_date($certificate, $certrecord, $course);

certificate_print_text($pdf, $x + 11 + 41, $y + $date_course_h, 'L', 'praxisltlight', '', $date_course_text, certificate_get_grade($certificate, $course, null, true));
certificate_print_text($pdf, $x + 215 + 15, $y + $date_course_h, 'L', 'praxisltlight', '', $date_course_text, $date);

?>