<?php
$file = "resources/views/settings/index.blade.php";
$c = file_get_contents($file);

$oldForm = <<<EOF
<form method="POST" action="{{ route('settings.package_sizes.destroy', \$s['id']) }}" onsubmit="return confirm('Remove size?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </form>
EOF;

$newForm = <<<EOF
<div style="display: flex; gap: 4px;">
                                        <button type="button" class="btn btn-sm btn-ghost" onclick="openEditPackageSizeModal('{{ \$s['id'] }}', '{{ addslashes(\$s['name']) }}')" title="Edit">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20l1-4.5L15.5 5 19 8.5 8.5 19 4 20z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('settings.package_sizes.destroy', \$s['id']) }}" onsubmit="return confirm('Remove package size?');" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost" title="Delete"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                        </form>
                                    </div>
EOF;
$c = str_replace($oldForm, $newForm, $c);

// Replace description block with packages logged
$oldDesc = <<<EOF
@if (!empty(\$s['description']))
                                                <div class="cell-sub" style="margin-top: 2px;">{{ \$s['description'] }}</div>
                                            @endif
EOF;
$newDesc = <<<EOF
<div class="cell-sub" style="margin-top: 2px;">{{ \$s['packages'] ?? 0 }} packages logged</div>
EOF;
$c = str_replace($oldDesc, $newDesc, $c);

file_put_contents($file, $c);
echo "done";
?>
