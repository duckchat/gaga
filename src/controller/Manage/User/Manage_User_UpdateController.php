<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_User_UpdateController extends Manage_CommonController
{

    public function doRequest()
    {
        $userId = $_POST['userId'];
        $updateKey = $_POST['key'];
        $updateValue = $_POST['value'];

        $response = [
            'errCode' => "error"
        ];

        try {

            switch ($updateKey) {
                case "userId":
                    throw new Exception("userId update permission error");
                case "nickname":
                    if (empty($updateValue)) {
                        throw new Exception("nickname is null");
                    }

                    $pinyin = new \Overtrue\Pinyin\Pinyin();
                    $nicknameInLatin = $pinyin->permalink($updateValue, "");

                    if ($this->updateUserNickname($userId, $updateValue, $nicknameInLatin)) {
                        $response['errCode'] = "success";
                    }
                    break;
                case "loginName":
                    if (empty($updateValue)) {
                        throw new Exception("loginName is null");
                    }

                    $loginNameLowercase = strtolower($updateValue);

                    if ($this->updateUserLoginName($userId, $updateValue, $loginNameLowercase)) {
                        $response['errCode'] = "success";
                    }
                    break;
                case "avatar":
                    if (empty($updateValue)) {
                        throw new Exception("user avatar is null");
                    }

                    if ($this->updateUserProfile($userId, $updateKey, $updateValue)) {
                        $response['errCode'] = "success";
                    }

                    break;
                case "availableType":
                    // #TODO
                    break;
                case "addDefaultFriend":
                    if ($this->updateSiteDefaultFriends($userId, $updateValue)) {
                        $response['errCode'] = "success";
                    }
                    break;
                case "addSiteManager":
                    if ($this->updateSiteManagers($userId, $updateValue)) {
                        $response['errCode'] = "success";
                    }
                    break;
                case "changePassword"://update user password
                    $updateValue = trim($updateValue);
                    if (empty($updateValue)) {
                        throw new Exception($this->language == 1 ? "请输入修改的新密码" : "please input new password");
                    }

                    $response['errCode'] = $this->updateUserPassword($userId, $updateValue) ? "success" : "error";
                    break;
                default:
                    throw new Exception("known update field:" . $updateKey);
            }
        } catch (Exception $e) {
            $response['errInfo'] = $e->getMessage();
            $this->ctx->Wpf_Logger->error("manage.user.update", $e);
        }

        echo json_encode($response);
        return;
    }

    private function updateUserNickname($userId, $nickname, $nicknameInLatin)
    {
        $where = [
            'userId' => $userId
        ];

        $data = [
            'nickname' => $nickname,
            'nicknameInLatin' => $nicknameInLatin,
        ];

        return $this->ctx->SiteUserTable->updateUserData($where, $data);
    }

    private function updateUserLoginName($userId, $loginName, $loginNameLowercase)
    {
        $where = [
            'userId' => $userId
        ];

        $data = [
            'loginName' => $loginName,
            'loginNameLowercase' => $loginNameLowercase,
        ];

        return $this->ctx->SiteUserTable->updateUserData($where, $data);
    }

    private function updateUserProfile($userId, $updateName, $updateValue)
    {
        $where = [
            'userId' => $userId
        ];

        $data = [
            $updateName => $updateValue
        ];

        return $this->ctx->SiteUserTable->updateUserData($where, $data);
    }

    private function updateSiteManagers($userId, $updateValue)
    {
        if ($updateValue == 1) {
            return $this->ctx->Site_Config->addSiteManager($userId);
        } else {
            return $this->ctx->Site_Config->removeSiteManager($userId);
        }
    }

    private function updateSiteDefaultFriends($userId, $updateValue)
    {
        if ($updateValue == 1) {
            return $this->ctx->Site_Config->addDefaultFriend($userId);
        } else {
            return $this->ctx->Site_Config->removeDefaultFriend($userId);
        }
    }

    private function updateUserPassword($userId, $newPassword)
    {
        $userProfile = $this->ctx->SiteUserTable->getUserByUserId($userId);

        $where = [
            "loginName" => $userProfile['loginName']
        ];

        $data = [
            "password" => password_hash($newPassword, PASSWORD_BCRYPT)
        ];
        return $this->ctx->PassportPasswordTable->updateUserData($where, $data);
    }

}