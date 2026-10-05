<?php

$file = 'resources/views/settings/index.blade.php';
$content = file_get_contents($file);

$oldForm = <<<'EOF'
                        <div class="card">
                            <div class="section-title">Primary Brand Color</div>
                            <div class="cell-sub">Theme color applied across guest passes, kiosk terminals, buttons, and notifications.</div>
                            <div class="color-swatches">
                                @foreach (['#2E9E5B','#3B82F6','#8B5CF6','#EF4444','#F59E0B','#14B8A6'] as $clr)
                                    <button type="button" class="color-swatch {{ strtolower($branding['primary_color'] ?? '#2E9E5B') === strtolower($clr) ? 'active' : '' }}" style="background: {{ $clr }};" onclick="setBrandColor('{{ $clr }}')"></button>
                                @endforeach
                                <input type="color" id="brandColorPicker" value="{{ $branding['primary_color'] ?? '#2E9E5B' }}" oninput="setBrandColor(this.value)" style="width: 38px; height: 34px; padding: 0; border: none; border-radius: 6px; cursor: pointer;">
                                <input type="text" name="primary_color" id="brandColorHex" value="{{ $branding['primary_color'] ?? '#2E9E5B' }}" style="width: 100px; font-family: monospace; padding: 7px 10px; border-radius: 6px; border: 1px solid var(--border);">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 18px;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Branding</span>
                            </button>
                        </div>
EOF;

$newForm = <<<'EOF'
                        <div class="card">
                            <div class="section-title">Primary Brand Color</div>
                            <div class="color-swatches" style="margin-top: 12px;">
                                @foreach (['#2E9E5B','#3B82F6','#8B5CF6','#EF4444','#F59E0B','#14B8A6'] as $clr)
                                    <button type="button" class="color-swatch {{ strtolower($branding['primary_color'] ?? '#2E9E5B') === strtolower($clr) ? 'active' : '' }}" style="background: {{ $clr }};" onclick="setBrandColor('{{ $clr }}')"></button>
                                @endforeach
                                <input type="color" id="brandColorPicker" value="{{ $branding['primary_color'] ?? '#2E9E5B' }}" oninput="setBrandColor(this.value)" style="width: 38px; height: 34px; padding: 0; border: none; border-radius: 6px; cursor: pointer;">
                                <input type="text" name="primary_color" id="brandColorHex" value="{{ $branding['primary_color'] ?? '#2E9E5B' }}" style="width: 100px; font-family: monospace; padding: 7px 10px; border-radius: 6px; border: 1px solid var(--border);">
                            </div>
                        </div>

                        <div class="card">
                            <div class="form-grid">
                                <div class="field">
                                    <label>Welcome Message</label>
                                    <input type="text" name="welcome_message" value="{{ old('welcome_message', $branding['welcome_message'] ?? 'Welcome to Zapio') }}">
                                </div>
                                <div class="field">
                                    <label>Subtitle</label>
                                    <input type="text" name="welcome_subtitle" value="{{ old('welcome_subtitle', $branding['welcome_subtitle'] ?? 'Please check in below to notify your host') }}">
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="section-title">Background Image (Kiosk)</div>
                            <div class="upload-box" style="margin-top: 12px; min-height: 160px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px dashed var(--border); border-radius: 6px; padding: 24px; position: relative; background: #fafafa;">
                                @if(!empty($branding['background_url']))
                                    <img id="bgPreview" src="{{ $branding['background_url'] }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.4; border-radius: 6px; z-index: 0;">
                                @else
                                    <img id="bgPreview" src="" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.4; border-radius: 6px; z-index: 0;">
                                @endif
                                <div style="z-index: 1; text-align: center;">
                                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" style="width: 32px; height: 32px; color: var(--text-muted); margin-bottom: 8px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <div>
                                        <input type="file" name="background_image" id="bgFileInput" accept="image/*" style="display: none;" onchange="previewUploadBg(this)">
                                        <button type="button" class="btn btn-sm" style="background: #fff; border: 1px solid var(--border);" onclick="document.getElementById('bgFileInput').click();">
                                            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                            <span>Upload Image</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="cell-sub" style="margin-top: 8px;">Displayed behind the welcome screen on kiosk devices.</div>
                        </div>

                        <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div class="section-title" style="margin-bottom: 2px;">Favicon</div>
                                <div class="cell-sub">Shown in the browser tab of the admin panel.</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div id="faviconPreviewBox" style="width: 36px; height: 36px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; border-radius: 6px; overflow: hidden;">
                                    @if(!empty($branding['favicon_url']))
                                        <img id="faviconPreview" src="{{ $branding['favicon_url'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <span id="faviconText">Z</span>
                                        <img id="faviconPreview" src="" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                    @endif
                                </div>
                                <input type="file" name="favicon" id="faviconInput" accept="image/*" style="display: none;" onchange="previewUploadFavicon(this)">
                                <button type="button" class="btn btn-sm" onclick="document.getElementById('faviconInput').click();">
                                    <span>Change</span>
                                </button>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 18px;">
                            <button type="reset" class="btn" style="background: #fff; border: 1px solid var(--border);">Reset</button>
                            <button type="submit" class="btn btn-primary" style="background: #0f6c5c;">
                                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <span>Save Branding</span>
                            </button>
                        </div>
EOF;

if (strpos($content, $oldForm) !== false) {
    $content = str_replace($oldForm, $newForm, $content);
} else {
    // Let's do regex replace if exact text mismatch
    $pattern = '/<div class="card">\s*<div class="section-title">Primary Brand Color<\/div>.*?<\/button>\s*<\/div>/s';
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $newForm, $content);
    }
}

// Add the JS preview functions at the end of the file
$jsFunctions = <<<'EOF'
        function previewUploadBg(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('bgPreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function previewUploadFavicon(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('faviconPreview');
                    const text = document.getElementById('faviconText');
                    if (text) text.style.display = 'none';
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
EOF;

if (strpos($content, 'previewUploadBg') === false) {
    $content = str_replace('function previewUploadLogo(input)', $jsFunctions . "\n        function previewUploadLogo(input)", $content);
}

file_put_contents($file, $content);
echo "Settings view updated.\n";
