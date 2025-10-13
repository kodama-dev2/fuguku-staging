<style>
    .tabs {
        display: inline-flex;
        border-bottom: 3px solid #28a745;
        margin-bottom: 15px;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
    }
    .tab {
        padding: 10px 25px;
        cursor: pointer;
        background: #d4f5d4;
        font-weight: bold;
        transition: background 0.3s;
        border-right: 1px solid #28a745;
    }
    .tab:last-child { border-right: none; }
    .tab.active { background: #28a745; color: #fff; }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    th {
        background: #28a745;
        color: white;
        text-transform: uppercase;
        font-size: 14px;
    }
    tr:hover { background: #f1fdf1; }
    tr.update-available { background: #fff3cd; }
    tr.update-available:hover { background: #ffeaa7; }

    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 5px;
    }
    .btn-update-now { background: #ffc107; color: #000; }
    .btn-check-update { background: #17a2b8; color: #fff; }
    .btn-install { background: #28a745; color: #fff; }
    .btn-activated { background: #6c757d; color: #fff; }
    .btn:disabled { opacity: 0.5; cursor: not-allowed; }
    
    .update-status {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 3px;
        margin-left: 5px;
    }
    .update-available-badge { background: #ffc107; color: #000; }
    .up-to-date-badge { background: #28a745; color: #fff; }
    .checking-badge { background: #17a2b8; color: #fff; }
</style>

<div class="tabs">
    <div class="tab active" onclick="showTab(event,'plugins')">Plugins</div>
    <div class="tab" onclick="showTab(event,'themes')">Themes</div>
</div>

<!-- Plugins -->
<div id="plugins" class="tab-content active">
    <table>
        <thead>
            <tr>
                <th>Plugin Name</th>
                <th>Current Version</th>
                <th>Status</th>
                <th>Updates</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($all_plugins)): ?>
                <?php foreach ($all_plugins as $file => $plugin): ?>
                    <tr class="<?= $plugin['has_update'] ? 'update-available' : ''; ?>" data-plugin="<?= esc_attr($file); ?>">
                        <td>
                            <strong><?= esc_html($plugin['name']); ?></strong>
                            <br><small><?= esc_html($plugin['slug']); ?></small>
                        </td>
                        <td class="srm-currentversion"><?= esc_html($plugin['version']); ?></td>
                        <td>
                            <?php if ($plugin['is_active']): ?>
                                <button class="btn btn-activated">Active</button>
                            <?php else: ?>
                                <button class="btn btn-install" onclick="activatePlugin('<?= esc_attr($file); ?>')">Activate</button>
                            <?php endif; ?>
                        </td>
                        <td class="update-column">
                            <?php if ($plugin['has_update']): ?>
                                <span class="update-status update-available-badge">Update to <?= esc_html($plugin['new_version']); ?></span>
                            <?php else: ?>
                                <span class="update-status up-to-date-badge">Up to date</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions-column">
                            <button 
                                class="btn btn-check-update srm-checkupdate-item" 
                                    data-type="plugin" 
                                    data-file="<?= esc_attr($file); ?>" 
                                    data-name="<?= esc_attr($plugin['name']); ?>" 
                                    data-package="<?= esc_attr($plugin['package']); ?>">
                                Check Update
                            </button>
                            <?php if ($plugin['has_update'] && !empty($plugin['package'])): ?>
                                <button 
                                    class="btn btn-update-now srm-update-item" 
                                    data-type="plugin" 
                                    data-file="<?= esc_attr($file); ?>" 
                                    data-name="<?= esc_attr($plugin['name']); ?>" 
                                    data-package="<?= esc_attr($plugin['package']); ?>">
                                    Update Now
                                </button>
                            <?php elseif ($plugin['has_update']): ?>
                                <button 
                                    class="btn btn-update-now srm-update-item" 
                                    data-type="plugin" 
                                    data-file="<?= esc_attr($file); ?>" 
                                    data-name="<?= esc_attr($plugin['name']); ?>" 
                                    data-package="">
                                    Manual Update
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; font-style:italic; color:#666;">
                        No plugins found
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Themes -->
<div id="themes" class="tab-content">
    <table>
        <thead>
            <tr>
                <th>Theme Name</th>
                <th>Current Version</th>
                <th>Status</th>
                <th>Updates</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($all_themes)): ?>
                <?php foreach ($all_themes as $slug => $theme): ?>
                    <tr class="<?= $theme['has_update'] ? 'update-available' : ''; ?>" data-theme="<?= esc_attr($slug); ?>">
                        <td>
                            <strong><?= esc_html($theme['name']); ?></strong>
                            <br><small><?= esc_html($slug); ?></small>
                        </td>
                        <td class="srm-currentversion"><?= esc_html($theme['version']); ?></td>
                        <td>
                            <?php if ($theme['is_active']): ?>
                                <button class="btn btn-activated">Active</button>
                            <?php else: ?>
                                <button class="btn btn-install" onclick="activateTheme('<?= esc_attr($slug); ?>')">Activate</button>
                            <?php endif; ?>
                        </td>
                        <td class="update-column">
                            <?php if ($theme['has_update']): ?>
                                <span class="update-status update-available-badge">Update to <?= esc_html($theme['new_version']); ?></span>
                            <?php else: ?>
                                <span class="update-status up-to-date-badge">Up to date</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions-column">
                            <button 
                                class="btn btn-check-update srm-checkupdate-item" 
                                 data-type="theme" 
                                    data-file="<?= esc_attr($slug); ?>" 
                                    data-name="<?= esc_attr($theme['name']); ?>" 
                                    data-package="<?= esc_attr($theme['package']); ?>">
                                Check Update
                            </button>
                            <?php if ($theme['has_update'] && !empty($theme['package'])): ?>
                                <button 
                                    class="btn btn-update-now srm-update-item" 
                                    data-type="theme" 
                                    data-file="<?= esc_attr($slug); ?>" 
                                    data-name="<?= esc_attr($theme['name']); ?>" 
                                    data-package="<?= esc_attr($theme['package']); ?>">
                                    Update Now
                                </button>
                            <?php elseif ($theme['has_update']): ?>
                                <button 
                                    class="btn btn-update-now srm-update-item" 
                                    data-type="theme" 
                                    data-file="<?= esc_attr($slug); ?>" 
                                    data-name="<?= esc_attr($theme['name']); ?>" 
                                    data-package="">
                                    Manual Update
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; font-style:italic; color:#666;">
                        No themes found
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function showTab(e, tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    e.target.classList.add('active');
}

</script>

