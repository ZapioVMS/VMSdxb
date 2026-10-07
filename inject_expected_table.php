<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

$expectedTable = <<<'HTML'

            <!-- Pre-Registered Visitors Expected Today -->
            <div class="card" style="margin-top: 24px;">
                <div class="section-title">Pre-Registered Visitors Expected Today</div>
                <div style="overflow-x: auto; margin-top: 12px;">
                    @if(isset($expectedToday) && $expectedToday->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border); color: var(--text-secondary);">
                                <th style="padding: 12px 16px; font-weight: 600;">Visitor</th>
                                <th style="padding: 12px 16px; font-weight: 600;">Expected Time</th>
                                <th style="padding: 12px 16px; font-weight: 600;">Host</th>
                                <th style="padding: 12px 16px; font-weight: 600;">Purpose</th>
                                <th style="padding: 12px 16px; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expectedToday as $invite)
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 12px 16px;">
                                    <div style="font-weight: 500; color: var(--text-primary);">{{ $invite->visit->visitor->full_name ?? $invite->visitor_name ?? '-' }}</div>
                                    <div style="font-size: 12px; color: var(--text-secondary);">{{ $invite->company ?? '-' }}</div>
                                </td>
                                <td style="padding: 12px 16px; font-family: monospace;">
                                    {{ \Carbon\Carbon::parse($invite->visit->scheduled_start_at)->format('H:i') }}
                                </td>
                                <td style="padding: 12px 16px;">{{ $invite->visit->hostEmployee->full_name ?? '-' }}</td>
                                <td style="padding: 12px 16px;">
                                    <span style="background: var(--surface-3); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        {{ $invite->visit->purpose->name ?? 'Meeting' }}
                                    </span>
                                </td>
                                <td style="padding: 12px 16px;">
                                    <div style="display: flex; gap: 8px;">
                                        <!-- Edit button redirects to pre-registrations page -->
                                        <a href="{{ route('pre_registrations.index') }}" class="btn btn-sm btn-ghost" title="Edit" style="padding: 4px 8px;">
                                            <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <!-- Delete button via form -->
                                        <form method="POST" action="{{ route('pre_registrations.cancel', $invite->id) }}" onsubmit="return confirm('Delete this expected visitor?');" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="padding: 4px 8px; color: var(--critical); border-color: rgba(208, 59, 59, 0.25);">
                                                <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div style="padding: 24px; text-align: center; color: var(--text-secondary); font-size: 14px; background: var(--surface-2); border-radius: 8px;">
                        No pre-registered visitors are expected today.
                    </div>
                    @endif
                </div>
            </div>
HTML;

$content = str_replace('</form>', "</form>\n" . $expectedTable, $content);

file_put_contents($file, $content);
echo "Injected expected table properly.\n";
