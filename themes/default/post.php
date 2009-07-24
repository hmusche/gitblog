<div class="wrapper">
	<!-- =========================== sidebar =========================== -->
	<? include gb::$theme_dir . '/sidebar.php' ?>
	<!-- =========================== post =========================== -->
	<div class="posts single">
		<div class="post">
			<?= $post->commentsLink() ?>
			<h1><?= $post->title ?></h1>
			<p class="meta">
				<?= $post->published->age() ?>
				by <?= h($post->author->name) . $post->tagLinks(', tagged ') . $post->categoryLinks(', filed under ')  ?>
			</p>
			<div class="body">
				<?= $post->body ?>
			</div>
		</div>
		<div class="breaker"></div>
	</div>
	<!-- =========================== comments =========================== -->
	<div id="comments">
		<hr/>
		<div class="wrapper">
		<? if ($post->comments): ?>
			<h2><?= $post->numberOfComments() ?></h2>
			<ul>
			<?
				$prevlevel = 0;
				foreach($post->comments as $level => $comment):
					if ($level > $prevlevel)
						echo '<ul class="l'.$level.'">';
					elseif ($level < $prevlevel)
						echo str_repeat('</ul>', $prevlevel-$level);
					$prevlevel = $level;
				?>
				<li class="comment<?= $comment->type === GBComment::TYPE_PINGBACK ? ' pingback' : '' ?>" id="comment-<?= $comment->id ?>">
					<div class="avatar">
						<img 
							src="http://www.gravatar.com/avatar.php?gravatar_id=<?= md5($comment->email) ?>&amp;size=48&amp;default=<?= urlencode(gb::$theme_url . 'default-avatar.png') ?>" />
					</div>
					<div class="message-wrapper">
						<? if ($post->commentsOpen): ?>
						<div class="actions">
							<? if (gb::$authenticated): ?>
								<a class="rm" href="<?= h($comment->removeURL()) ?>"
									title="Remove this comment and hide any replies to it"><span>&otimes;</span></a>
							<? endif ?>
							<a class="reply" href="javascript:reply('<?= $comment->id ?>');" 
								title="Reply to this comment"><span>&#x21A9;</span></a>
							<div class="breaker"></div>
						</div>
						<? endif; ?>
						<div class="author">
							<?= $comment->nameLink() ?>
							<a href="#comment-<?= $comment->id ?>" class="age"><?= $comment->date->age() ?></a>
						</div>
						<div class="breaker"></div>
						<div class="message">
							<?= $comment->body ?>
						</div>
					</div>
					<div class="breaker"></div>
				</li>
				<li class="breaker"></li>
			<? endforeach; echo str_repeat('</ul>', $prevlevel); ?>
			</ul>
			<!-- =========================== unapproved info =========================== -->
			<div class="breaker"></div>
			<? if ($post->comments->countUnapproved()): ?>
				<p><small>
					<?= $post->numberOfUnapprovedComments() ?> awaiting approval
					<? if ($post->comments->countShadowed()): ?>
						— <?= $post->numberOfShadowedComments(
							'approved comment is', 'approved comments are', 'no', 'one') ?>
						therefore not visible.
					<? endif; ?>
				</small></p>
			<? endif; ?>
		<? endif; # comments ?>
		<!-- =========================== post comment form =========================== -->
		<? if ($post->commentsOpen): ?>
			<div id="reply"></div>
			<h2 id="reply-title">Comment</h2>
			<form id="comment-form" action="<?= h(gb::$site_url) ?>gitblog/helpers/post-comment.php" method="POST">
				<?= gb_comment_fields() ?>
				<p>
					<textarea id="comment-reply-message" name="reply-message" rows="3"></textarea>
				</p>
				<p>
					<?= gb_comment_author_field('email', 'Email') 
						. gb_comment_author_field('name', 'Name')
					 	. gb_comment_author_field('url', 'Website (optional)') ?>
				</p>
				<p class="buttons">
					<input type="submit" value="Add comment" />
				</p>
				<div class="breaker"></div>
			</form>
			<script type="text/javascript">
				//<![CDATA[
				function trim(s) {
					return s.replace(/(^[ \t\s\n\r]+|[ \t\s\n\r]+$)/g, '');
				}
				document.getElementById('comment-form').onsubmit = function(e) {
					function check_filled(id, default_value) {
						var elem = document.getElementById(id);
						if (!elem)
							return false;
						elem.value = trim(elem.value);
						if (elem.value == default_value || elem.value == '') {
							elem.select();
							return false;
						}
						return true;
					}
					if (!check_filled('comment-reply-message', ''))
						return false;
					if (!check_filled('comment-author-name', 'Name'))
						return false;
					if (!check_filled('comment-author-email', 'Email'))
						return false;
					return true;
				}

				// reply-to
				var reply_to_comment = null;

				function reply(comment_id) {
					reply_to_comment = document.getElementById('comment-'+comment_id);
					document.getElementById('comment-reply-to').value = comment_id;
				}

				var reply_to = document.getElementById('comment-reply-to');
				var reply_to_lastval = "";
				var form_parent = null;
				var cancel_button = null;

				reply_to.onchange = function(e) {
					reply_to.value = trim(reply_to.value);
					var title = document.getElementById('reply-title');
					var form = document.getElementById('comment-form');

					// remove any cancel button
					if (cancel_button != null) {
						if (cancel_button.parentNode)
							cancel_button.parentNode.removeChild(cancel_button);
						cancel_button = null;
					}

					if (reply_to.value != "") {
						if (reply_to_comment == null) {
							reply_to.value = "";
							return;
						}

						if (form_parent == null)
							form_parent = form.parentNode;

						cancel_button = document.createElement('input');
						cancel_button.setAttribute('type', 'button');
						cancel_button.setAttribute('value', 'Cancel');
						cancel_button.onclick = function(e) { document.getElementById('comment-reply-to').value = ""; };

						// find submit button and append the form to its parent
						var inputs = form.getElementsByTagName("input");
						for (var i=0; i<inputs.length; i++) {
							var elem = inputs.item(i);
							if (elem.getAttribute('type') == 'submit') {
								elem.parentNode.appendChild(cancel_button);
								break;
							}
						}

						form.className = "inline-reply";
						reply_to_comment.appendChild(form);
						title.style.display = 'none';

						//document.location.hash = "comment-"+reply_to.value;
					}
					else {
						form.className = "";
						if (form_parent != null)
							form_parent.appendChild(form);
						title.style.display = '';
						document.location.hash = "reply";
					}	
					setTimeout(function(){
						document.getElementById('comment-reply-message').select()
					},100);
				}
				setInterval(function(e){
					if (reply_to_lastval != reply_to.value)
						reply_to.onchange(e);
					reply_to_lastval = reply_to.value;
				},200);

				// select message on reply
				setTimeout(function(){if (document.location.hash == '#reply')
					document.getElementById('comment-reply-message').select();
				},100);
			//]]>
			</script>
		<? else: ?>
			<p>Comments are closed.</p>
		<? endif; ?>
		</div>
	</div>
	<div class="breaker"></div>
</div>
<? flush() ?>