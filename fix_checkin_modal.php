<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// Remove the includes
$content = str_replace("@include('partials.invite_modal')", "", $content);
$content = str_replace("@include('partials.invite_modal_js')", "", $content);

// Change the Edit button to link to the pre-registrations page, or remove the onclick
// We will replace the onclick with a simple href back to pre-registrations index, since the modal is not wanted here.
$pattern = '/onclick="openEditInviteModal\([^)]+\)"/s';
$content = preg_replace($pattern, 'href="{{ route(\'pre_registrations.index\') }}"', $content);

// Also fix the delete button if it's there. The delete form is fine since it submits a DELETE request.

file_put_contents($file, $content);
echo "Checkin blade fixed.\n";
