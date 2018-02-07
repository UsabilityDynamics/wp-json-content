<?php

namespace UsabilityDynamics\WPJC;

/**
 * Class User
 * @package UsabilityDynamics\FSW
 */
class User {

  /**
   * @var false|\WP_User
   */
  private $_user;

  /**
   * @var
   */
  public $id;

  /**
   * @var string
   */
  public $first_name;

  /**
   * @var string
   */
  public $last_name;

  /**
   * @var string
   */
  public $username;

  /**
   * @var string
   */
  public $email;

  /**
   * @var false|string
   */
  public $avatar;

  /**
   * User constructor.
   * @param $user_id
   */
  public function __construct( $user_id ) {

    $this->_user = get_user_by( 'id', $user_id );

    $this->id = $user_id;

    $this->first_name = $this->_user->first_name;

    $this->last_name = $this->_user->last_name;

    $this->username = $this->_user->user_login;

    $this->email = $this->_user->user_email;

    $this->avatar = get_avatar_url( $this->id );

  }
}