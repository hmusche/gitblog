<?
$updated_time = $postspage->posts ? $postspage->posts[0]->modified->time : time();

header('Content-Type: application/atom+xml; charset=utf-8');
header('Last-Modified: '.date('r', $updated_time));
echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
?>
<feed xmlns="http://www.w3.org/2005/Atom" 
      xmlns:thr="http://purl.org/syndication/thread/1.0"
      xmlns:gb="http://gitblog.se/ns/atom/1.0"
      xml:lang="en"
      xml:base="<?php echo h(gb::$site_url) ?>">
	<id><?php echo h(gb::url()) ?></id>
	<title><?php echo h(gb::$site_title) ?></title>
	<link rel="alternate" type="text/html" href="<?php echo h(gb::$site_url) ?>" />
	<updated><?php echo date('c', $updated_time) ?></updated>
	<generator uri="http://gitblog.se/" version="<?php echo gb::$version ?>">Gitblog</generator>
<?php foreach ($postspage->posts as $post): ?>
	<entry>
		<title type="html"><?php echo h($post->title) ?></title>
		<author>
			<name><?php echo h($post->author->name) ?></name>
			<uri><?php echo h(gb::$site_url) ?></uri>
		</author>
		<link rel="alternate" type="text/html" href="<?php echo h($post->url()) ?>" />
		<id><?php echo h($post->url()) ?></id>
		<published><?php echo $post->published ?></published>
		<updated><?php echo $post->modified ?></updated>
		<?php echo $post->tagLinks('', '', '<category scheme="'.gb::url_to('tags').'" term="%n" />',
			"\n\t\t", "\n\t\t").($post->tags ? "\n" : '') ?>
		<?php echo $post->categoryLinks('', '', '<category scheme="'.gb::url_to('categories').'" term="%n" />',
			"\n\t\t", "\n\t\t").($post->categories ? "\n" : '') ?>
		<comments><?php echo $post->comments ?></comments>
		<gb:version><?php echo $post->id ?></gb:version>
		<?php if ($post->excerpt): ?>
		<summary type="html"><![CDATA[<?php echo $post->excerpt ?>]]></summary>
		<?php endif ?>
		<content type="html" xml:base="<?php echo h($post->url()) ?>"><![CDATA[<?php echo $post->body() ?><?php if ($post->excerpt): ?>
			<p><a href="<?php echo h($post->url()) ?>#<?php echo $post->domID() ?>-more">Read more...</a></p>
		<?php endif; ?>]]></content>
		<link rel="replies" type="text/html" href="<?php echo h($post->url()) ?>#comments" thr:count="<?php echo $post->comments ?>" />
		<thr:total><?php echo $post->comments ?></thr:total>
	</entry>
<?php endforeach ?>
</feed>
