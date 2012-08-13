	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><?php echo __('Create a river') ?></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li <?php echo ($step == 'name') ? 'class="active"' : '';?>>
				<a href="<?php echo URL::site().$account_path.'/river/create'; ?>"><?php echo __('1. Name your river'); ?></a>
			</li>
			<li <?php echo ($step == 'channels') ? 'class="active"' : '';?>>
				<a href="<?php echo $open_url; ?>"><?php echo __('2. Add channels'); ?></a>
			</li>
			<li <?php echo ($step == 'filters') ? 'class="active"' : '';?>>
				<a href="<?php echo $filters_url; ?>"><?php echo __('3. Add Filters'); ?></a>
			</li>
			<li <?php echo ($step == 'finish') ? 'class="active"' : '';?>>
				<a href="<?php echo $view_url; ?>"><?php echo __('4. View your river'); ?></a>
			</li>
		</ul>
	</nav>

	<?php echo Form::open(); ?>
	<?php echo $step_content; ?>
	<?php echo Form::close(); ?>

	<div id="modal-container">
		<div class="modal-window"></div>
		<div class="modal-fade"></div>
	</div>