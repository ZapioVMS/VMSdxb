<?php
$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

$target = <<<'HTML'
                                  <div class="field">
                                      <label for="first_name">First Name *</label>
                                      <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required autofocus oninput="checkWatchlistName(this.value)">
                                  </div>
                                  <div class="field">
                                      <label for="last_name">Last Name</label>
                                      <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}">
                                  </div>
HTML;

$replacement = <<<'HTML'
                                  <div class="field" style="grid-column: span 2;">
                                      <label for="full_name">Fullname *</label>
                                      <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus oninput="checkWatchlistName(this.value)">
                                  </div>
HTML;

$content = str_replace($target, $replacement, $content);

// Also update the prefillCheckinForm JS at the bottom!
$jsTarget = <<<'JS'
            if(data.first_name) document.getElementById('first_name').value = data.first_name;
            if(data.last_name) document.getElementById('last_name').value = data.last_name;
JS;
$jsReplacement = <<<'JS'
            if(data.first_name) {
                const lastName = data.last_name ? ' ' + data.last_name : '';
                document.getElementById('full_name').value = data.first_name + lastName;
            }
JS;

$content = str_replace($jsTarget, $jsReplacement, $content);

file_put_contents($file, $content);
echo "Updated Check-in form UI.\n";
