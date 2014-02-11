<table>
	<tr valign="top">
		<th class="metabox_label_column">
			<label for="stock">Stock No.</label>
		</th>
		<td>
			<input type="text" id="stock" name="stock" value="<?php echo @get_post_meta($post->ID, 'stock', true); ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column">
			<label for="vin-number">VIN</label>
		</th>
		<td>
			<input type="text" id="vin-number" name="vin-number" value="<?php echo @get_post_meta($post->ID, 'vin-number', true); ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column">
			<label for="mileage">Mileage</label>
		</th>
		<td>
			<input type="text" id="mileage" name="mileage" value="<?php echo @get_post_meta($post->ID, 'mileage', true); ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column">
			<label for="model">Model</label>
		</th>
		<td>
			<input type="text" id="model" name="model" value="<?php echo @get_post_meta($post->ID, 'model', true); ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column">
			<label for="year">Year</label>
		</th>
		<td>
			<input type="text" id="year" name="year" value="<?php echo @get_post_meta($post->ID, 'year', true); ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column">
			<label for="price">Price</label>
		</th>
		<td>
			<input type="text" id="price" name="price" value="<?php echo @get_post_meta($post->ID, 'price', true); ?>" />
		</td>
	</tr>
</table>