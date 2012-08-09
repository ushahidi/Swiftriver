	<div id="content" class="settings channels cf">
		<div class="center">
			<div class="col_12">
				<div class="alert-message blue">
					<p>
						<strong><?php echo __('Success!');?></strong> 
						<?php
							echo __('Your ":river_name" river has been successfully created!. 
								Click the "View my River" button below to visit your river\'s homepage.',
								array(":river_name" => $river_name));
						?> 
				</div>
				<div class="settings-toolbar">
					<p class="button-blue button-big" onclick="submitForm(this)"><a><?php echo __("View my River"); ?></a></p>
				</div>
			</div>
		</div>
	</div>