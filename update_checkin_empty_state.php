<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

$oldCode = <<<'HTML'
            <!-- Pre-Registered Visitors Expected Today -->
            @if(isset($expectedToday) && $expectedToday->count() > 0)
            <div class="card" style="margin-top: 24px;">
                <div class="section-title">Pre-Registered Visitors Expected Today</div>
                <div style="overflow-x: auto; margin-top: 12px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
HTML;

$newCode = <<<'HTML'
            <!-- Pre-Registered Visitors Expected Today -->
            <div class="card" style="margin-top: 24px;">
                <div class="section-title">Pre-Registered Visitors Expected Today</div>
                <div style="overflow-x: auto; margin-top: 12px;">
                    @if(isset($expectedToday) && $expectedToday->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
HTML;

$content = str_replace($oldCode, $newCode, $content);

$oldEnd = <<<'HTML'
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
HTML;

$newEnd = <<<'HTML'
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

$content = str_replace($oldEnd, $newEnd, $content);

file_put_contents($file, $content);
echo "Checkin blade empty state updated.\n";
