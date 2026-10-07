<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

$oldSection = <<<'HTML'
            <div class="pagehead" style="margin-top: 40px; margin-bottom: 16px;">
                <div>
                    <h1>Pre-Registered Visitors Expected Today</h1>
                </div>
            </div>

            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 40px;">
                @if($expectedToday->count() > 0)
                    @include('partials.pre_registrations_table', ['invitations' => $expectedToday, 'showCheckInButton' => true])
                @else
                    <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 15px;">
                        No pre-registered visitors are expected today.
                    </div>
                @endif
            </div>
HTML;

$newSection = <<<'HTML'
            <div class="card" style="margin-top: 30px; margin-bottom: 40px; padding: 24px;">
                <div style="margin-bottom: 20px;">
                    <h2 style="font-size: 16px; font-weight: 700; margin: 0 0 4px 0; color: var(--text-primary);">Pre-Registered Visitors Expected Today</h2>
                    <div style="font-size: 13px; color: var(--text-muted);">{{ $expectedToday->count() }} pre-registered visitor{{ $expectedToday->count() !== 1 ? 's' : '' }} expected today.</div>
                </div>

                @if($expectedToday->count() > 0)
                    <div class="checkin-table-container">
                        <style>
                            .checkin-table-container .tablewrap { border: none; border-radius: 0; box-shadow: none; }
                            .checkin-table-container .dtable thead th { background: transparent; border-bottom: 2px solid var(--surface-2); padding: 12px 14px; }
                            .checkin-table-container .dtable tbody td { border-bottom: 1px solid var(--surface-2); }
                            .checkin-table-container .dtable tbody tr:last-child td { border-bottom: none; }
                        </style>
                        @include('partials.pre_registrations_table', ['invitations' => $expectedToday, 'showCheckInButton' => true])
                    </div>
                @else
                    <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px; border: 1px dashed var(--border); border-radius: var(--radius-sm);">
                        No pre-registered visitors are expected today.
                    </div>
                @endif
            </div>
HTML;

$content = str_replace($oldSection, $newSection, $content);
file_put_contents($file, $content);
echo "Updated Checkin layout.\n";

