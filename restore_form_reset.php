<?php
$file = 'resources/views/settings/index.blade.php';
$content = file_get_contents($file);

$target = <<<'HTML'
    // Modals open/close
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('open');
    }
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('open');
    }
HTML;

$replacement = <<<'HTML'
    // Modals open/close
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('open');
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.modal-root form').forEach(f => {
            f.dataset.defaultAction = f.action;
        });
    });

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('open');
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                if (form.dataset.defaultAction) {
                    form.action = form.dataset.defaultAction;
                }
                const methodField = form.querySelector('input[name="_method"]');
                if (methodField) {
                    methodField.value = 'POST';
                }
                const title = modal.querySelector('.modal-head h3');
                if (title && title.innerText.startsWith('Edit ')) {
                    title.innerText = title.innerText.replace('Edit ', 'Add ');
                }
                
                // Clear any dynamic elements like tags-input if they exist
                const tagsContainer = modal.querySelector('.tags-input-container');
                if (tagsContainer && typeof window.currentFloorTags !== 'undefined') {
                    window.currentFloorTags = [];
                    if (typeof renderFloorTags === 'function') {
                        renderFloorTags();
                    }
                }
            }
        }
    }
HTML;

$newContent = str_replace($target, $replacement, $content);
file_put_contents($file, $newContent);
echo "Restored form resetting logic.\n";
