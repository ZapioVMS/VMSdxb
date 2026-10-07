<?php

$file = 'resources/views/pre_registrations/index.blade.php';
$content = file_get_contents($file);

// Find qrModal HTML
$startHtml = strpos($content, '<!-- QR Code Modal -->');
$endHtml = strpos($content, '</div>', strpos($content, '<!-- End QR Modal -->')) + 6;
// Wait, I didn't see an end comment. Let's just find the next <!-- Add / Edit Pre-Registration Modal -->
$endHtml = strpos($content, '<!-- Add / Edit Pre-Registration Modal -->');

$qrHtml = substr($content, $startHtml, $endHtml - $startHtml);
file_put_contents('resources/views/partials/qr_modal.blade.php', trim($qrHtml) . "\n");

// Replace in index
$content = str_replace($qrHtml, "@include('partials.qr_modal')\n    ", $content);

// Find qrModal JS
$startJs = strpos($content, 'let qrCodeObj = null;');
$endJs = strpos($content, 'function openInviteModal()');

$qrJs = substr($content, $startJs, $endJs - $startJs);
file_put_contents('resources/views/partials/qr_modal_js.blade.php', trim($qrJs) . "\n");

// Replace in index
$content = str_replace($qrJs, "@include('partials.qr_modal_js')\n\n        ", $content);

file_put_contents($file, $content);

// Now update checkin.blade.php
$checkinFile = 'resources/views/visitors/checkin.blade.php';
$checkinContent = file_get_contents($checkinFile);

// Add qr_modal html right before invite_modal html
$checkinContent = str_replace("@include('partials.invite_modal')", "@include('partials.qr_modal')\n    @include('partials.invite_modal')", $checkinContent);

// Add qr_modal js right before invite_modal js
$checkinContent = str_replace("@include('partials.invite_modal_js')", "@include('partials.qr_modal_js')\n        @include('partials.invite_modal_js')", $checkinContent);

file_put_contents($checkinFile, $checkinContent);
echo "Extracted QR modal to partials and included in checkin.\n";

