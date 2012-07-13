<?php
function soft_truncate($string, $length = 100)
{
	$string = str_replace("\n", ' ', $string);
	if (strlen($string) > $length)
	{
		$string = substr(wordwrap($string, $length), 0, strpos($string, "\n"));
	}
	return $string;
}
?><script type="text/javascript">
	$(function() {
		$('.messages tr').click(function(){
			window.location.href = '<?php echo $link_inbox; ?>/'+$(this).attr('id');
		});
	});
</script>
<div id="content" class="messages inbox cf" align="center">
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
