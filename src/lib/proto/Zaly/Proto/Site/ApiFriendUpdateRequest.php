<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: site/api_friend_update.proto

namespace Zaly\Proto\Site;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 **
 * action: api.friend.update
 *
 * Generated from protobuf message <code>site.ApiFriendUpdateRequest</code>
 */
class ApiFriendUpdateRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string userId = 1;</code>
     */
    private $userId = '';
    /**
     * Generated from protobuf field <code>repeated .site.ApiFriendUpdateValue values = 2;</code>
     */
    private $values;

    public function __construct() {
        \GPBMetadata\Site\ApiFriendUpdate::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>string userId = 1;</code>
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Generated from protobuf field <code>string userId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setUserId($var)
    {
        GPBUtil::checkString($var, True);
        $this->userId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .site.ApiFriendUpdateValue values = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Generated from protobuf field <code>repeated .site.ApiFriendUpdateValue values = 2;</code>
     * @param \Zaly\Proto\Site\ApiFriendUpdateValue[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setValues($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Zaly\Proto\Site\ApiFriendUpdateValue::class);
        $this->values = $arr;

        return $this;
    }

}

