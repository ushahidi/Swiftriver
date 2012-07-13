<script type="text/javascript">
	$(function() {
		$('.button-send').click(function() {
			var data = {
				a: 1,
				r: $('#message-recipient').val(),
				s: $('#message-subject').val(),
				b: $('#message-body').val()
			}
			$.post('create', data, function(response) {
				$('p.message-status').css('color', 'green');
				$('p.message-status').text(response);
				window.location.href = '<?php echo $link_outbox; ?>';
			}, 'json').error(function(response) {
				$('p.message-status').css('color', 'red');
				$('p.message-status').text($.parseJSON(response.responseText));
			});
			return false;
		});
	});
</script>
<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1>Send a message</h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="back">
				<a href="<?php echo $link_inbox; ?>">
					<span class="icon"></span>
					Back to Inbox
				</a>
			</h2>
		</div>
	</div>
</hgroup>
<div id="content" class="message-create center cf" align="center">
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1>New message</h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label for="bucket_name">
					<p class="field">Recipient</p>
					<input type="text" id="message-recipient" name="recipient" value="<?php echo HTML::chars(Arr::get($_GET, 'r', '')); ?>" />
				</label>
			</div>
			<div class="parameter">
				<label for="bucket_name">
					<p class="field">Subject</p>
					<input type="text" id="message-subject" name="subject" value="<?php echo HTML::chars(Arr::get($_GET, 's', '')); ?>" />
				</label>
			</div>
			<div class="parameter">
				<label for="bucket_name">
					<p class="field">Message</p>
					<textarea id="message-body" name="body" rows="6"></textarea>
				</label>
			</div>
		</section>
	</article>
	<div class="save-toolbar visible">
		<p class="button-blue"><a href="#" class="button-send">Send</a></p>
		<p class="button-blank"><a href="<?php echo $link_inbox; ?>">Cancel</a></p>
		<p class="message-status"></p>
	</div>
</div>