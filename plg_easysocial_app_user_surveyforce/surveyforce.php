<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );


Foundry::import( 'admin:/includes/apps/apps' );

class SocialUserAppSurveyForce extends SocialAppItem
{

	public function __construct()
	{
		parent::__construct();
	}


	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true ){
	}

	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true ){
	}


	public function onAfterLikeSave( &$likes ){
	}

	public function onAfterCommentSave( &$comment ){
	}

	public function onNotificationLoad( $item ){
	}
	
}
