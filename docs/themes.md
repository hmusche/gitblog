# Themes

*This document is early work in progress*

A theme is simply a PHP script including [gitblog.php](../gitblog.php).

Example of an extremely simple theme:

	<?php
	$gb_handle_request = true;
	require 'gitblog/gitblog.php';
	?>
	<html>
		<body>
		<h1><?php echo gb_title() ?></h1>
		<?php
		if (gb::$is_post) {
			?>
			<h1><?php echo $post->title ?></h1>
			<?php echo $post->body() ?>
			<?php
		}
		# handle other cases ...
		?>
		</body>
	</html>

The theme index.php is then placed (hardlinked or symlinked) into your document root.

Have a closer look at the default theme in [themes/default](../themes/default) as it uses most of the 
functionality of Gitblog and contains comments.
