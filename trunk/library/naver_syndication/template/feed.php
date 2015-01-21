<?php /* @var $this NaverSyndicationFeed */ ?>
<feed xmlns="http://webmastertool.naver.com">
	<id><?php echo $this->getId(); ?></id>
	<title><?php echo $this->getTitle(); ?></title>
	<author>
		<name><?php echo $this->getAuthor( 'name' ); ?></name>
		<email><?php echo $this->getAuthor( 'email' ); ?></email>
	</author>
	<updated><?php echo $this->getUpdated() ?></updated>
	<link rel="site" href="<?php echo $this->getLink( 'href' ); ?>" title="<?php echo $this->getLink( 'title' ); ?>"/>
	<?php
	foreach ( $this->entries as $entry ) :
		echo $entry->asXml();
	endforeach;
	?>
</feed>