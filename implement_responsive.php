<?php
// 1. Modify topbar to add Hamburger Menu
$topbarFile = 'resources/views/partials/topbar.blade.php';
$topbarContent = file_get_contents($topbarFile);
$hamburgerHtml = <<<'HTML'
    <!-- Left Section: Site Filter Dropdown + Global Search Bar -->
    <div style="display:flex; align-items:center; gap:12px; flex:1; max-width:650px;">
        <!-- Hamburger Menu Button (Mobile Only) -->
        <button type="button" class="mobile-only-btn" onclick="toggleSidebar()" style="border:none; background:none; padding:8px; cursor:pointer; color:var(--text-primary); margin-left:-10px;">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
HTML;
if (strpos($topbarContent, 'mobile-only-btn') === false) {
    $topbarContent = str_replace('    <!-- Left Section: Site Filter Dropdown + Global Search Bar -->
    <div style="display:flex; align-items:center; gap:12px; flex:1; max-width:650px;">', $hamburgerHtml, $topbarContent);
    file_put_contents($topbarFile, $topbarContent);
}

// 2. Modify sidebar to add Close Button and overlay
$sidebarFile = 'resources/views/partials/sidebar.blade.php';
$sidebarContent = file_get_contents($sidebarFile);
$closeBtnHtml = <<<'HTML'
    <!-- Brand Logo Section -->
    <div class="brand" style="padding: 16px 18px; border-block-end: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('dashboard') }}" style="display: block; text-decoration: none;">
            <img class="brand-logo" src="{{ asset('images/logo.png') }}" alt="Zapio VMS" style="height: 38px; width: auto; max-width: 100%; display: block;">
        </a>
        <button type="button" class="mobile-only-btn" onclick="toggleSidebar()" style="border:none; background:none; padding:6px; cursor:pointer; color:var(--text-primary);">
            <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
HTML;
if (strpos($sidebarContent, 'mobile-only-btn') === false) {
    // Replace the exact brand section
    $brandRegex = '/<!-- Brand Logo Section -->\s*<div class="brand"[^>]*>\s*<a[^>]*>\s*<img[^>]*>\s*<\/a>\s*<\/div>/s';
    $sidebarContent = preg_replace($brandRegex, $closeBtnHtml, $sidebarContent);
    
    // Add overlay at the top of the sidebar file
    $overlayHtml = '<div class="sidebar-overlay" onclick="toggleSidebar()"></div>' . "\n";
    $sidebarContent = $overlayHtml . $sidebarContent;
    file_put_contents($sidebarFile, $sidebarContent);
}

// 3. Inject Responsive CSS into all standalone blade files
$responsiveCss = <<<'CSS'
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 998; opacity: 0; transition: opacity 0.2s ease; }
        .mobile-only-btn { display: none !important; }
        
        @@media (max-width: 900px) {
            #app { display: flex !important; flex-direction: column !important; min-height: 100vh; overflow-x: hidden; }
            #sidebar { 
                position: fixed !important; left: 0; top: 0; bottom: 0; z-index: 999; 
                width: 260px; transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
                box-shadow: 4px 0 24px rgba(0,0,0,0.1); 
            }
            body.sidebar-open #sidebar { transform: translateX(0); }
            body.sidebar-open .sidebar-overlay { display: block; opacity: 1; }
            
            #topbar { width: 100%; grid-column: auto !important; grid-row: auto !important; padding-inline: 12px !important; }
            #main { padding: 16px 14px 60px !important; grid-column: auto !important; grid-row: auto !important; width: 100%; flex: 1; }
            
            .mobile-only-btn { display: inline-flex !important; }
            
            /* Responsive Utilities */
            .grid-4, .grid-3, .form-grid { grid-template-columns: 1fr !important; }
            .grid-2 { grid-template-columns: 1fr !important; }
            .pagehead { flex-direction: column; align-items: flex-start !important; gap: 12px; height: auto !important; padding-bottom: 12px; }
            .pagehead > div:last-child { width: 100%; display: flex; flex-direction: column; gap: 8px; }
            .pagehead button { width: 100%; justify-content: center; }
            .filterbar { flex-direction: column; align-items: stretch !important; gap: 10px; }
            .searchbox { max-width: 100% !important; }
            
            table { display: block; overflow-x: auto; white-space: nowrap; }
            .modal-panel { width: 92% !important; max-height: 90vh !important; margin: auto; }
            .dtable th, .dtable td { padding: 10px 12px !important; }
        }
    </style>
CSS;

$jsScript = <<<'HTML'
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>
</body>
HTML;

$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach ($files as $file) {
    $filePath = $file[0];
    $content = file_get_contents($filePath);
    
    // Check if it has a <style> block and #app definition (standalone files)
    if (strpos($content, '<style>') !== false && strpos($content, '#app {') !== false) {
        $updated = false;
        
        // Inject CSS
        if (strpos($content, '@@media (max-width: 900px)') === false && strpos($content, '@media (max-width: 900px)') === false) {
            $content = str_replace('</style>', $responsiveCss, $content);
            $updated = true;
        }
        
        // Inject JS before </body>
        if (strpos($content, 'function toggleSidebar()') === false) {
            $content = str_replace('</body>', $jsScript, $content);
            $updated = true;
        }
        
        if ($updated) {
            file_put_contents($filePath, $content);
            $count++;
        }
    }
}

echo "Successfully updated $count view files.\n";
