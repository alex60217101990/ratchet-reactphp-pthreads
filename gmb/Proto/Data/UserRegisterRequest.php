<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/test.proto

namespace Proto\Data;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.data.UserRegisterRequest</code>
 */
class UserRegisterRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.proto.data.User user = 1;</code>
     */
    private $user = null;

    public function __construct() {
        \GPBMetadata\Proto\Test::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>.proto.data.User user = 1;</code>
     * @return \Proto\Data\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Generated from protobuf field <code>.proto.data.User user = 1;</code>
     * @param \Proto\Data\User $var
     * @return $this
     */
    public function setUser($var)
    {
        GPBUtil::checkMessage($var, \Proto\Data\User::class);
        $this->user = $var;

        return $this;
    }

}

