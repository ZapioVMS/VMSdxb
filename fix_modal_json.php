<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

$oldJson = <<<'HTML'
                                        <!-- Edit button opens modal -->
                                        <button type="button" class="btn btn-sm btn-ghost" title="Edit" style="padding: 4px 8px;" onclick="openEditInviteModal({{ json_encode([
                                            'id' => $invite->id,
                                            'name' => $invite->visit->visitor->first_name ?? $invite->visitor_name,
                                            'company_name' => $invite->company ?? '',
                                            'email' => $invite->email ?? '',
                                            'phone' => str_replace('+', '', $invite->phone ?? ''),
                                            'phone_code' => $invite->phone_code ?? '971',
                                            'id_type_id' => $invite->visit->visitor->id_type_id ?? '',
                                            'id_number' => $invite->visit->visitor->id_number ?? '',
                                            'nationality_id' => $invite->visit->visitor->nationality_id ?? '',
                                            'visit_date' => $invite->visit->scheduled_start_at ? \Carbon\Carbon::parse($invite->visit->scheduled_start_at)->format('Y-m-d') : '',
                                            'expected_arrival' => $invite->visit->scheduled_start_at ? \Carbon\Carbon::parse($invite->visit->scheduled_start_at)->format('H:i') : '',
                                            'duration' => $invite->duration ?? 60,
                                            'site_id' => $invite->visit->site_id ?? '',
                                            'host_employee_id' => $invite->visit->host_employee_id ?? '',
                                            'purpose_id' => $invite->visit->purpose_id ?? '',
                                            'visitor_type_id' => $invite->visit->visitor_type_id ?? '',
                                            'department_id' => $invite->visit->department_id ?? '',
                                            'meeting_room_name' => $invite->visit->meeting_room ?? ''
                                        ]) }})">
HTML;

$newJson = <<<'HTML'
                                        <!-- Edit button opens modal -->
                                        <button type="button" class="btn btn-sm btn-ghost" title="Edit" style="padding: 4px 8px;" onclick="openEditInviteModal({{ json_encode([
                                            'id' => $invite->id,
                                            'first_name' => $invite->visit->visitor->first_name ?? $invite->visitor_name ?? '',
                                            'last_name' => $invite->visit->visitor->last_name ?? '',
                                            'company_name' => $invite->company ?? '',
                                            'email' => $invite->email ?? '',
                                            'phone' => $invite->phone ?? '',
                                            'id_type_id' => $invite->visit->visitor->id_type_id ?? '',
                                            'id_number' => $invite->visit->visitor->id_number ?? '',
                                            'visit_date' => $invite->visit->scheduled_start_at ? \Carbon\Carbon::parse($invite->visit->scheduled_start_at)->format('Y-m-d') : '',
                                            'expected_arrival' => $invite->visit->scheduled_start_at ? \Carbon\Carbon::parse($invite->visit->scheduled_start_at)->format('H:i') : '10:00',
                                            'expected_departure' => $invite->visit->scheduled_end_at ? \Carbon\Carbon::parse($invite->visit->scheduled_end_at)->format('H:i') : '',
                                            'duration' => $invite->duration ?? 60,
                                            'recurring' => $invite->recurring ?? 'none',
                                            'purpose_id' => $invite->visit->purpose_id ?? '',
                                            'visitor_type_id' => $invite->visit->visitor_type_id ?? '',
                                            'site_id' => $invite->visit->site_id ?? '',
                                            'host_employee_id' => $invite->visit->host_employee_id ?? '',
                                            'department_id' => $invite->visit->department_id ?? '',
                                            'meeting_room' => $invite->meeting_room ?? '',
                                            'expected_visitor_count' => $invite->expected_visitor_count ?? 1
                                        ]) }})">
HTML;

$content = str_replace($oldJson, $newJson, $content);

file_put_contents($file, $content);
echo "Checkin blade json fix applied.\n";
