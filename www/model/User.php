<?php

namespace photobox\model;

class User extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'utilisateurs';
	protected $primary_key = 'id';
	protected $timestamps = true;
}