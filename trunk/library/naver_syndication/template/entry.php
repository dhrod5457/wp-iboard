<?php /* @var $this NaverSyndicationEntry */ ?>
<?php if ( $this->isPublish() ): ?>
	<entry>
		<id><?php echo $this->getId() ?></id>
		<title><![CDATA[<?php echo $this->getTitle(); ?>]]></title>
		<author>
			<name><?php echo $this->getAuthor('name'); ?></name>
			<email><?php echo $this->getAuthor('email'); ?></email>
		</author>
		<updated><?php echo $this->getUpdated() ?></updated>
		<published><?php echo $this->getPublished() ?></published>
		<link rel="via" href="<?php echo $this->getLink('href'); ?>" title="<?php echo $this->getLink('title'); ?>"/>
		<content type="html"><![CDATA[<?php echo $this->getContent(); ?>]]></content>
		<summary type="text"><![CDATA[<?php echo $this->getSummery(); ?>]]></summary>
	</entry>
<?php else: ?>
	<deleted-entry ref="<?php echo $this->id; ?>" when="<?php echo $this->getUpdated(); ?>"/>
<?php endif; ?>