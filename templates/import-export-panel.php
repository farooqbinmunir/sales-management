<?php
include (FBM_PLUGIN_DIR . '/templates/header.php');

$entities = fbm_get_exportable_entities();
$status = isset($_GET['import_status']) ? sanitize_text_field($_GET['import_status']) : '';
$message = isset($_GET['message']) ? sanitize_text_field(urldecode($_GET['message'])) : '';
$imported = isset($_GET['imported']) ? intval($_GET['imported']) : 0;
?>

<div class="fbm_panel_box">
	<?php if ($status === 'success'): ?>
		<div class="notice notice-success">
			<p>Imported <?php echo esc_html($imported); ?> rows successfully.</p>
			<?php if ($message): ?>
				<p><?php echo esc_html($message); ?></p>
			<?php endif; ?>
		</div>
	<?php elseif ($status === 'error'): ?>
		<div class="notice notice-error">
			<p><?php echo esc_html($message ?: 'Import failed. Please check the file and try again.'); ?></p>
		</div>
	<?php endif; ?>

	<div class="fbm_section">
		<h3>Export Data</h3>
		<p>Select the record type you want to download. Exported files are CSV format and include the table fields as headers.</p>
		<form method="get" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="fbm_export_data" />
			<?php wp_nonce_field('fbm_export_data', 'fbm_export_nonce'); ?>
			<label for="export_entity">Export</label>
			<select id="export_entity" name="entity" class="regular-text" style="max-width: 360px; margin-right: 10px;">
				<?php foreach ($entities as $key => $entity): ?>
					<option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($entity['label']); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="button button-primary">Download CSV</button>
		</form>
	</div>

	<div class="fbm_section" style="margin-top: 30px;">
		<h3>Import Data</h3>
		<p>Choose a previously exported CSV file and the entity to import. The first row must remain the exported header row.</p>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
			<input type="hidden" name="action" value="fbm_import_data" />
			<?php wp_nonce_field('fbm_import_data', 'fbm_import_nonce'); ?>
			<label for="import_entity">Import</label>
			<select id="import_entity" name="entity" class="regular-text" style="max-width: 360px; margin-right: 10px;">
				<?php foreach ($entities as $key => $entity): ?>
					<option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($entity['label']); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="file" name="import_file" accept=".csv" required style="margin-right: 10px;" />
			<button type="submit" class="button button-primary">Upload CSV</button>
		</form>
		<p class="description" style="margin-top: 1rem;">Note: Do not modify the header labels if you want the file to import correctly. Primary key columns are ignored on import.</p>
	</div>
</div>

<?php include (FBM_PLUGIN_DIR . '/templates/footer.php');
