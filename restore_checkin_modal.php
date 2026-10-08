<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// 1. Revert Edit button, add Check In button
$oldButtons = <<<'HTML'
                                    <div style="display: flex; gap: 8px;">
                                        <!-- Edit button populates the checkin form -->
                                        <button type="button" class="btn btn-sm btn-ghost" title="Fill Check-In Form" style="padding: 4px 8px;" onclick="prefillCheckinForm({{ json_encode([
                                            'first_name' => $invite->visit->visitor->first_name ?? '',
                                            'last_name' => $invite->visit->visitor->last_name ?? '',
                                            'id_type_id' => $invite->visit->visitor->id_type_id ?? '',
                                            'id_number' => $invite->visit->visitor->id_number ?? '',
                                            'nationality_id' => $invite->visit->visitor->nationality_id ?? '',
                                            'phone_code' => $invite->phone_code ?? '971',
                                            'phone' => str_replace('+', '', $invite->phone ?? ''),
                                            'email' => $invite->email ?? '',
                                            'company_name' => $invite->company ?? '',
                                            'site_id' => $invite->visit->site_id ?? '',
                                            'host_employee_id' => $invite->visit->host_employee_id ?? '',
                                            'purpose_id' => $invite->visit->purpose_id ?? '',
                                            'duration' => $invite->duration ?? 60
                                        ]) }})">
                                            <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <!-- Delete button via form -->
                                        <form method="POST" action="{{ route('pre_registrations.cancel', $invite->id) }}" onsubmit="return confirm('Delete this expected visitor?');" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="padding: 4px 8px; color: var(--critical); border-color: rgba(208, 59, 59, 0.25);">
                                                <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
HTML;

$newButtons = <<<'HTML'
                                    <div style="display: flex; gap: 8px;">
                                        <!-- Check In button populates the checkin form -->
                                        <button type="button" class="btn btn-sm" style="background-color: var(--accent); color: white; border: none; display: flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 4px;" title="Check In" onclick="prefillCheckinForm({{ json_encode([
                                            'first_name' => $invite->visit->visitor->first_name ?? '',
                                            'last_name' => $invite->visit->visitor->last_name ?? '',
                                            'id_type_id' => $invite->visit->visitor->id_type_id ?? '',
                                            'id_number' => $invite->visit->visitor->id_number ?? '',
                                            'nationality_id' => $invite->visit->visitor->nationality_id ?? '',
                                            'phone_code' => $invite->phone_code ?? '971',
                                            'phone' => str_replace('+', '', $invite->phone ?? ''),
                                            'email' => $invite->email ?? '',
                                            'company_name' => $invite->company ?? '',
                                            'site_id' => $invite->visit->site_id ?? '',
                                            'host_employee_id' => $invite->visit->host_employee_id ?? '',
                                            'purpose_id' => $invite->visit->purpose_id ?? '',
                                            'duration' => $invite->duration ?? 60
                                        ]) }})">
                                            <svg class="ic" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                            Check In
                                        </button>
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
                                            <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <!-- Delete button via form -->
                                        <form method="POST" action="{{ route('pre_registrations.cancel', $invite->id) }}" onsubmit="return confirm('Delete this expected visitor?');" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="padding: 4px 8px; color: var(--critical); border-color: rgba(208, 59, 59, 0.25);">
                                                <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
HTML;

$content = str_replace($oldButtons, $newButtons, $content);

// 2. Add the modal CSS to the <style> section
$modalCSS = <<<'CSS'
        /* Modal styling with visible y-axis scrollbar */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; background: rgba(6, 12, 11, 0.5); padding: 20px 16px; }
        .modal-root.open { display: flex; }
        .modal-backdrop { position: absolute; inset: 0; }
        .modal-panel { position: relative; z-index: 1; max-width: 680px; width: 100%; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); max-height: 88vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-panel form { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; height: 100%; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .modal-head h3 { font-size: 16px; font-weight: 700; margin: 0; }
        .modal-body { padding: 20px 24px; overflow-y: scroll; flex: 1; min-height: 0; max-height: calc(88vh - 130px); scrollbar-width: thin; scrollbar-color: #b0b8b6 transparent; }
        .modal-body::-webkit-scrollbar { width: 8px; }
        .modal-body::-webkit-scrollbar-track { background: transparent; }
        .modal-body::-webkit-scrollbar-thumb { background: #b0b8b6; border-radius: 99px; }
        .modal-body::-webkit-scrollbar-thumb:hover { background: #7c8985; }
        .modal-foot { display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 14px 20px; border-top: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .section-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); margin: 16px 0 10px; border-top: 1px solid var(--border); padding-top: 14px; }
        .section-label:first-child { margin-top: 0; border-top: none; padding-top: 0; }
CSS;

$content = preg_replace('/<\/style>/', $modalCSS . "\n    </style>", $content);

// 3. Add the modal HTML and JS at the end of the file
$modalHTML = <<<'HTML'
    @include('partials.invite_modal')
    <script>
        @include('partials.invite_modal_js')
    </script>
</body>
HTML;

$content = str_replace('</body>', $modalHTML, $content);

file_put_contents($file, $content);
echo "Checkin blade updated with Check In button and modal.\n";
