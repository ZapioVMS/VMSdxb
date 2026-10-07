<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// 1. Phone Code Listing
$oldPhoneCode = <<<'HTML'
                                        <select name="phone_code">
                                            <option value="971">+971 (UAE)</option>
                                            <option value="966">+966 (KSA)</option>
                                            <option value="974">+974 (QA)</option>
                                            <option value="1">+1 (US)</option>
                                            <option value="44">+44 (UK)</option>
                                        </select>
HTML;

$newPhoneCode = <<<'HTML'
                                        <select name="phone_code">
                                            @php
                                                $ae = $countries->firstWhere('iso_code', 'AE');
                                                $others = $countries->filter(fn($c) => $c->iso_code !== 'AE');
                                            @endphp
                                            @if($ae && $ae->isd_code)
                                                <option value="{{ $ae->isd_code }}" {{ old('phone_code', '971') == $ae->isd_code ? 'selected' : '' }}>+{{ $ae->isd_code }} ({{ $ae->iso_code }})</option>
                                            @endif
                                            @foreach($others as $c)
                                                @if($c->isd_code)
                                                    <option value="{{ $c->isd_code }}" {{ old('phone_code') == $c->isd_code ? 'selected' : '' }}>+{{ $c->isd_code }} ({{ $c->iso_code }})</option>
                                                @endif
                                            @endforeach
                                        </select>
HTML;
$content = str_replace($oldPhoneCode, $newPhoneCode, $content);


// 2. Change Site Location to Branch
$content = str_replace('<label for="site_id">Site Location *</label>', '<label for="site_id">Branch *</label>', $content);


// 3. Change Host Employee to Host / employee to visit
$content = str_replace('<label for="host_employee_id">Host Employee *</label>', '<label for="host_employee_id">Host / employee to visit *</label>', $content);
$content = str_replace('<option value="">Select Host Employee</option>', '<option value="">Select Host / employee to visit</option>', $content);


// 4. Submit button text
$content = str_replace('<span>Check In Visitor</span>', '<span>Check in Visitor & Issue Badge</span>', $content);
// In case it doesn't have <span>
$content = str_replace('>Check In Visitor<', '>Check in Visitor & Issue Badge<', $content);


// 5. Checkbox text
$oldCheckbox = 'The visitor has been briefed on site health & safety rules.';
$newCheckbox = 'The visitor has been briefed on site health & safety rules and has agreed to the confidentiality (NDA) policy.';
$content = str_replace($oldCheckbox, $newCheckbox, $content);


// 6. Expected Today Table
$expectedTable = <<<'HTML'
                </form>
            </div>

            <!-- Pre-Registered Visitors Expected Today -->
            @if(isset($expectedToday) && $expectedToday->count() > 0)
            <div class="card" style="margin-top: 24px;">
                <div class="section-title">Pre-Registered Visitors Expected Today</div>
                <div style="overflow-x: auto; margin-top: 12px;">
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
                                        <!-- Edit button triggers a check-in prepopulation or actual edit -->
                                        <a href="#" class="btn btn-sm" title="Edit" style="padding: 4px 8px;">
                                            <svg class="ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <!-- Delete button via form -->
                                        <form method="POST" action="{{ route('pre-registrations.destroy', $invite->id) }}" onsubmit="return confirm('Delete this expected visitor?');" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
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
                </div>
            </div>
            @endif
HTML;

$content = preg_replace('/<\/form>\s*<\/div>\s*<\/div>\s*<\/div>/', $expectedTable . "\n        </div>\n    </div>\n</div>", $content);


file_put_contents($file, $content);
echo "View updated.\n";
