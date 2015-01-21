<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:32
 */
interface NaverSyndicationModelInterface {
	public function getId();

	public function getTitle();

	public function getAuthor( $name_or_email );

	public function getLink( $title_or_href );

	public function getUpdated();
}

interface NaverSyndicationFeedInterface extends NaverSyndicationModelInterface {
}

interface NaverSyndicationEntryInterface extends NaverSyndicationModelInterface {
	public function getPublished();

	public function getContent();

	public function isPublish();

	public function getSummery();
}