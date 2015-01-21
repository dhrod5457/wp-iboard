<?php /* @var $this IBoardLatestPage */ ?>

<ul>
	<?php foreach ( $this->list as $item ) : ?>
		<li><a href="<?php echo $item->getReadLink( $this->url ); ?>"><?php echo $item->subject ?></a></li>
	<?php endforeach; ?>
</ul>