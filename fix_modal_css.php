<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

$modalCSS = <<<'CSS'
        /* Modal styling with visible y-axis scrollbar */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; background: rgba(6, 12, 11, 0.5); padding: 20px 16px; }
        .modal-root.open { display: flex; }
        .modal-backdrop { position: absolute; inset: 0; }
        .modal-panel { position: relative; z-index: 1; max-width: 680px; width: 100%; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); max-height: 88vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-panel form { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; height: 100%; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .modal-head h3 { font-size: 16px; font-weight: 700; margin: 0; }
        .modal-body { padding: 20px 24px; overflow-y: scroll; flex: 1; min-height: 0; max-height: calc(88vh - 130px); scrollbar-width: thin; scrollbar-color: #b0b8b6 transparent; }
        .modal-body::-webkit-scrollbar { width: 8px; }
        .modal-body::-webkit-scrollbar-track { background: transparent; }
        .modal-body::-webkit-scrollbar-thumb { background: #b0b8b6; border-radius: 99px; }
        .modal-body::-webkit-scrollbar-thumb:hover { background: #7c8985; }
        .modal-foot { display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 14px 20px; border-top: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .section-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); margin: 16px 0 10px; border-top: 1px solid var(--border); padding-top: 14px; }
        .section-label:first-child { margin-top: 0; border-top: none; padding-top: 0; }
CSS;

$newModalCSS = <<<'CSS'
        /* Modal styling with visible y-axis scrollbar */
        .modal-root { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; background: rgba(6, 12, 11, 0.5); padding: 20px 16px; }
        .modal-root.open { display: flex; }
        .modal-backdrop { position: absolute; inset: 0; }
        .modal-panel { position: relative; z-index: 1; max-width: 680px; width: 100%; background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); max-height: 88vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-panel form { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; height: 100%; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .modal-head h3 { font-size: 16px; font-weight: 700; margin: 0; }
        .modal-body { padding: 20px 24px; overflow-y: scroll; flex: 1; min-height: 0; max-height: calc(88vh - 130px); scrollbar-width: thin; scrollbar-color: #b0b8b6 transparent; }
        .modal-body::-webkit-scrollbar { width: 8px; }
        .modal-body::-webkit-scrollbar-track { background: transparent; }
        .modal-body::-webkit-scrollbar-thumb { background: #b0b8b6; border-radius: 99px; }
        .modal-body::-webkit-scrollbar-thumb:hover { background: #7c8985; }
        .modal-foot { display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 14px 20px; border-top: 1px solid var(--border); flex-shrink: 0; background: var(--surface); }
        .section-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); margin: 16px 0 10px; border-top: 1px solid var(--border); padding-top: 14px; }
        .section-label:first-child { margin-top: 0; border-top: none; padding-top: 0; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field.span2 { grid-column: 1 / -1; }
        .field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); }

        /* Switch row styling */
        .switchrow { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); font-size: 13px; font-weight: 500; }
        .switchrow:last-child { border-bottom: none; }
        .switchrow .switch { position: relative; display: inline-block; width: 36px; height: 20px; flex: none; }
        .switchrow .switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .switchrow .switch .slider { position: absolute; cursor: pointer; inset: 0; background-color: var(--surface-3); transition: .2s; border-radius: 20px; }
        .switchrow .switch .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .2s; border-radius: 50%; }
        .switchrow .switch input:checked + .slider { background-color: var(--accent); }
        .switchrow .switch input:checked + .slider:before { transform: translateX(16px); }
CSS;

$content = str_replace($modalCSS, $newModalCSS, $content);

file_put_contents($file, $content);
echo "Checkin blade modal CSS updated with switches and form grids.\n";
