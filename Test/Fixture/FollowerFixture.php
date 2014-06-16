<?php

class FollowerFixture extends CakeTestFixture {

	public $fields = array(
		'id'			=> array('type' => 'integer', 'key' => 'primary'),
		'following_id'	=> array('type' => 'integer', 'default' => null),
		'follower_count'	=> array('type' => 'integer', 'default' => 0),
	);

	public $records = array(
		array('id' => 1, 'following_id' => null, 'follower_count' => 0),
		array('id' => 2, 'following_id' => null, 'follower_count' => 2),
		array('id' => 3, 'following_id' => null, 'follower_count' => 1),
		array('id' => 4, 'following_id' => 2, 'follower_count' => 0),
		array('id' => 5, 'following_id' => 3, 'follower_count' => 0),
		array('id' => 6, 'following_id' => null, 'follower_count' => 0),
		array('id' => 7, 'following_id' => 2, 'follower_count' => 1),
		array('id' => 8, 'following_id' => 7, 'follower_count' => 0),
	);
}
