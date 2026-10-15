<div class="tablewrap">
                <table class="dtable">
                    <thead>
                        <tr>
                            <th>Visitor</th>
                            <th>Expected Date</th>
                            <th>Time</th>
                            <th>Host Employee</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th style="text-align: end;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invitations as $inv)
                            @php
                                $visit = $inv->visit;
                                $visitor = $visit->visitor ?? null;
                                $host = $visit->hostEmployee ?? null;
                                $scheduledStart = $visit->scheduled_start_at ?? null;
                                $scheduledEnd = $visit->scheduled_end_at ?? null;

                                $isToday = $scheduledStart ? $scheduledStart->isToday() : false;
                                // Status shown = what actually happened to the visit (a checked-out visit shows "Checked out")
                                $effStatus = $inv->status === 'cancelled'
                                    ? 'cancelled'
                                    : (in_array($visit->status ?? null, ['checked_in', 'checked_out'], true) ? $visit->status : $inv->status);
                                $dateDisplay = $isToday ? 'Today' : ($scheduledStart ? $scheduledStart->format('D, M d, Y') : '—');
                                $timeDisplay = $scheduledStart ? $scheduledStart->format('H:i') . ($scheduledEnd ? ' – ' . $scheduledEnd->format('H:i') : '') : '—';
                            @endphp
                            <tr>
                                <td>
                                    <div class="cell-name">{{ $visitor ? $visitor->full_name : '—' }}</div>
                                    <div class="cell-sub">{{ $visitor->company_name ?? '—' }}</div>
                                </td>
                                <td>
                                    @if ($isToday)
                                        <strong style="color: var(--accent);">Today</strong>
                                    @else
                                        {{ $dateDisplay }}
                                    @endif
                                </td>
                                <td style="font-variant-numeric: tabular-nums; white-space: nowrap;">{{ $timeDisplay }}</td>
                                <td>
                                    <div class="cell-name">{{ $host ? $host->full_name : '—' }}</div>
                                    <div class="cell-sub">{{ $visit->department->name ?? ($host->department->name ?? '') }}</div>
                                </td>
                                <td>
                                    <span class="chip chip-accent">{{ $visit->purpose->name ?? 'Meeting' }}</span>
                                </td>
                                <td>
                                    @if ($effStatus === 'approved')
                                        <span class="chip chip-good">Confirmed</span>
                                    @elseif ($effStatus === 'checked_in')
                                        <span class="chip chip-good">Checked in</span>
                                    @elseif ($effStatus === 'checked_out')
                                        <span class="chip chip-muted" style="background: rgba(42, 92, 170, 0.10); color: #2a5caa;">Checked out</span>
                                    @elseif ($effStatus === 'pending')
                                        <span class="chip chip-warning">Pending</span>
                                    @elseif ($effStatus === 'cancelled')
                                        <span class="chip chip-muted">Cancelled</span>
                                    @else
                                        <span class="chip chip-muted">{{ ucfirst(str_replace('_', ' ', $effStatus)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                        @if (($showCheckInButton ?? false) && $isToday && !in_array($effStatus, ['cancelled', 'checked_in', 'checked_out'], true))
                                        <form action="{{ route('pre_registrations.checkin', $inv->id) }}" method="POST" style="display:inline;" onsubmit="this.querySelector('button').disabled = true;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" title="Check In" style="display: inline-flex; align-items: center; gap: 4px;">
                                                <svg class="ic" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                                Check In
                                            </button>
                                        </form>
                                        @endif
                                        <button class="btn btn-sm btn-ghost" onclick="openQrModal({{ json_encode([
                                            'code' => $inv->access_code,
                                            'name' => $visitor ? $visitor->full_name : '',
                                            'company' => $visitor->company_name ?? '',
                                            'host' => $host ? $host->full_name : '',
                                            'site' => $visit->site->name ?? '',
                                            'date' => $dateDisplay,
                                            'time' => $timeDisplay,
                                        ]) }})" title="View Access QR">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                        </button>
                                        <button class="btn btn-sm btn-ghost" onclick="openEditInviteModal({{ json_encode([
                                            'id' => $inv->id,
                                            'status' => $effStatus,
                                            'first_name' => $visitor->first_name ?? '',
                                            'last_name' => $visitor->last_name ?? '',
                                            'company_name' => $visitor->company_name ?? '',
                                            'email' => $visitor->email ?? '',
                                            'phone' => $visitor->phone ?? '',
                                            'id_type_id' => $visitor->id_type_id ?? '',
                                            'id_number' => $visitor->id_number ?? '',
                                            'visit_date' => $scheduledStart ? $scheduledStart->format('Y-m-d') : '',
                                            'expected_arrival' => $scheduledStart ? $scheduledStart->format('H:i') : '10:00',
                                            'expected_departure' => $scheduledEnd ? $scheduledEnd->format('H:i') : '',
                                            'duration' => $inv->duration ?? 60,
                                            'recurring' => $inv->recurring ?? 'none',
                                            'purpose_id' => $visit->purpose_id ?? '',
                                            'visitor_type_id' => $visit->visitor_type_id ?? '',
                                            'site_id' => $visit->site_id ?? '',
                                            'host_employee_id' => $visit->host_employee_id ?? '',
                                            'department_id' => $visit->department_id ?? '',
                                            'meeting_room' => $inv->meeting_room ?? '',
                                            'expected_visitor_count' => $inv->expected_visitor_count ?? 1,
                                            'send_confirmation_email' => $inv->send_confirmation_email,
                                            'notify_host_on_arrival' => $inv->notify_host_on_arrival,
                                            'send_qr_self_checkin' => $inv->send_qr_self_checkin,
                                            'requires_nda' => $inv->requires_nda,
                                            'wifi_guest_access' => $inv->wifi_guest_access,
                                            'parking_required' => $inv->parking_required,
                                            'escort_required' => $inv->escort_required,
                                            'special_instructions' => $inv->special_instructions ?? '',
                                            'items_to_bring' => $inv->items_to_bring ?? '',
                                        ]) }})" title="Edit Pre-registration">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 20l1-4.5L15.5 5 19 8.5 8.5 19 4 20z"/></svg>
                                        </button>

                                        @if (!in_array($effStatus, ['cancelled', 'checked_in', 'checked_out'], true))
                                            <form action="{{ route('pre_registrations.resend', $inv->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ghost" title="Resend Notification">
                                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 10a6 6 0 0 1 12 0c0 4 1.5 5.5 2 6H4c.5-.5 2-2 2-6z"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('pre_registrations.cancel', $inv->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this pre-registration?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ghost" style="color: var(--critical);" title="Cancel Pre-registration">
                                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 36px;">
                                    No pre-registrations found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>