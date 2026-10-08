<?php

$file = 'resources/views/pre_registrations/index.blade.php';
$content = file_get_contents($file);

// Find the tablewrap
$startPos = strpos($content, '<div class="tablewrap">');
$endPos = strpos($content, '</div>', strpos($content, '</table>')) + 6;

$tableHtml = substr($content, $startPos, $endPos - $startPos);

// In the table HTML, add the Check In button
$checkInButton = <<<'HTML'
                                        @if($showCheckInButton ?? false)
                                        <button class="btn btn-sm" style="background-color: var(--accent); color: white; border: none; display: flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 4px;" title="Check In" onclick="prefillCheckinForm({{ json_encode([
                                            'first_name' => $visitor->first_name ?? '',
                                            'last_name' => $visitor->last_name ?? '',
                                            'id_type_id' => $visitor->id_type_id ?? '',
                                            'id_number' => $visitor->id_number ?? '',
                                            'nationality_id' => $visitor->nationality_id ?? '',
                                            'phone_code' => $inv->phone_code ?? '971',
                                            'phone' => str_replace('+', '', $inv->phone ?? ''),
                                            'email' => $inv->email ?? '',
                                            'company_name' => $inv->company ?? '',
                                            'site_id' => $visit->site_id ?? '',
                                            'host_employee_id' => $visit->host_employee_id ?? '',
                                            'purpose_id' => $visit->purpose_id ?? '',
                                            'duration' => $inv->duration ?? 60
                                        ]) }})">
                                            <svg class="ic" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                            Check In
                                        </button>
                                        @endif
HTML;

$tableHtml = str_replace('<div style="display: flex; gap: 4px; justify-content: flex-end;">', '<div style="display: flex; gap: 4px; justify-content: flex-end;">' . "\n" . $checkInButton, $tableHtml);

file_put_contents('resources/views/partials/pre_registrations_table.blade.php', $tableHtml);

// Replace in index.blade.php
$content = str_replace($tableHtml, "@include('partials.pre_registrations_table', ['showCheckInButton' => false])", $content);
file_put_contents($file, $content);

// Now update checkin.blade.php
$checkinFile = 'resources/views/visitors/checkin.blade.php';
$checkinContent = file_get_contents($checkinFile);

// Find the tablewrap in checkin
$startPosCheckin = strpos($checkinContent, '<div class="tablewrap">');
$endPosCheckin = strpos($checkinContent, '</div>', strpos($checkinContent, '</table>')) + 6;

$checkinTableHtml = substr($checkinContent, $startPosCheckin, $endPosCheckin - $startPosCheckin);
$checkinContent = str_replace($checkinTableHtml, "@include('partials.pre_registrations_table', ['invitations' => \$expectedToday, 'showCheckInButton' => true])", $checkinContent);

file_put_contents($checkinFile, $checkinContent);

echo "Extracted table to partial and updated both views.\n";

