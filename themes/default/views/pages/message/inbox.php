<?php
function soft_truncate($string, $length = 200)
{
	$string = str_replace("\n", ' ', $string);
	if (strlen($string) > $length)
	{
		$string = substr(wordwrap($string, $length), 0, strpos($string, "\n"));
	}
	return $string;
}
?><script type="text/javascript">
	window.history.replaceState({}, 'Inbox', "<?php echo $link_inbox; ?>");
	$(function() {
		$('.messages tr').click(function(){
			window.location.href = '<?php echo $link_inbox; ?>/'+$(this).attr('id');
		});
	});
</script>
<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1>Inbox</h1>
			<div class="rundown-people">
				<h2><?php echo $new ?></h2><span>new message<?php echo ($new == 1) ? '' : 's'; ?> </span>
			</div>
		</div>
		<div class="page-actions col_3">
			<h2 class="discussion">
				<a href="<?php echo $link_outbox; ?>">
					<span class="icon"></span>
					Outbox
				</a>
			</h2>
			<h2 class="add">
				<a href="<?php echo $link_create; ?>">
					<span class="icon"></span>
					Write new message
				</a>
			</h2>
		</div>
	</div>
</hgroup>
<div id="content" class="messages inbox center cf" align="center">
	<table>
		<tbody><?php foreach ($messages as $m): ?>
			<tr id="<?php echo $m->id; ?>" class="<?php echo $m->read ? 'read' : 'unread'; ?>">
				<td width="15%" align="left"><?php echo $m->sender->name; ?></td>
				<td width="*" align="left">
					<span class="subject"><?php echo $m->subject; ?></span>
					<span class="details"> - <?php echo soft_truncate($m->message); ?></span>
				</td>
				<td width="110px" align="right"><?php echo $m->relative_time(); ?></td>
			</tr><?php endforeach; ?>
		</tbody>
	</table>
</div>