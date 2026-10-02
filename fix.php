<?php
$c = file_get_contents('resources/views/pre_registrations/index.blade.php');
$s = strpos($c, '<!-- Add / Edit Pre-Registration Modal -->');
$e = strpos($c, '<!-- QR & Access Code Modal -->', $s);
$modal = substr($c, $s, $e - $s);
if(!is_dir('resources/views/partials')) mkdir('resources/views/partials', 0777, true);
file_put_contents('resources/views/partials/invite_modal.blade.php', $modal);

// Also extract JS functions related to the invite modal
$s_js = strpos($c, 'function openInviteModal');
$e_js = strpos($c, 'function openQrModal', $s_js);
$js = substr($c, $s_js, $e_js - $s_js);
file_put_contents('resources/views/partials/invite_modal_js.blade.php', $js);
echo "Done.";
