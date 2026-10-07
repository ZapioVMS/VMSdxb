<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// 1. We need to make sure the Edit button works. Let's find the table we just injected and modify the edit button.
$oldEdit = <<<'HTML'
                                        <a href="#" class="btn btn-sm" title="Edit" style="padding: 4px 8px;">
HTML;
$newEdit = <<<'HTML'
                                        <button type="button" class="btn btn-sm btn-ghost" onclick="openEditInviteModal({{ json_encode([
                                            'id' => $invite->id,
                                            'first_name' => $invite->visit->visitor->first_name ?? '',
                                            'last_name' => $invite->visit->visitor->last_name ?? '',
                                            'company_name' => $invite->company ?? '',
                                            'email' => $invite->email ?? '',
                                            'phone' => str_replace('+', '', $invite->phone ?? ''),
                                            'id_type_id' => $invite->visit->visitor->id_type_id ?? '',
                                            'id_number' => $invite->visit->visitor->id_number ?? '',
                                            'visitDate' => $invite->visit->scheduled_start_at ? \Carbon\Carbon::parse($invite->visit->scheduled_start_at)->format('Y-m-d') : '',
                                            'expectedArrival' => $invite->visit->scheduled_start_at ? \Carbon\Carbon::parse($invite->visit->scheduled_start_at)->format('H:i') : '',
                                            'site_id' => $invite->visit->site_id ?? '',
                                            'host_employee_id' => $invite->visit->host_employee_id ?? '',
                                            'purpose_id' => $invite->visit->purpose_id ?? '',
                                            'visitor_type_id' => $invite->visit->visitor_type_id ?? '',
                                        ]) }})" title="Edit" style="padding: 4px 8px;">
HTML;
$content = str_replace($oldEdit, $newEdit, $content);
$content = str_replace("</a>\n                                        <!-- Delete button via form -->", "</button>\n                                        <!-- Delete button via form -->", $content);

// Ensure partials are included at the bottom of the body
if (strpos($content, "@include('partials.invite_modal')") === false) {
    $content = preg_replace('/<\/body>/', "    @include('partials.invite_modal')\n    @include('partials.invite_modal_js')\n</body>", $content);
}

// Ensure the form action for cancel uses pre_registrations.cancel instead of destroy
$content = str_replace("route('pre-registrations.destroy'", "route('pre_registrations.cancel'", $content);

file_put_contents($file, $content);
echo "View updated with edit/delete bindings.\n";
