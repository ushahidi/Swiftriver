	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<?php if (isset($errors)): ?>
				<?php foreach ($errors as $message): ?>
				<div class="alert-message red">
					<p><strong>Uh oh.</strong> <?php echo $message; ?></p>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>

				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1><?php echo __('What\'s your river about'); ?> <em><?php echo __('and who can see it?'); ?></em></h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="river_name">
								<p class="field"><?php echo __('Name'); ?></p>
								<input type="text" value="" name="river_name" />
							</label>
						</div>
						<div class="parameter">
							<label for="river_url">
								<p class="field"><?php echo __('Who can see it?'); ?></p>
								<select class="field" name="river_public">
									<option value="1"><?php echo __('Public (Anyone)'); ?></option>
									<option value="0"><?php echo __('Private (Collaborators only)'); ?></option>
								</select>						
							</label>
						</div>						
					</section>
				</article>

				<div class="settings-toolbar">
					<p class="button-blue button-big" onclick="submitForm(this)"><a>Next</a></p>
				</div>
			</div>
		</div>
	</div>