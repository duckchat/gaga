<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_Group_UpdateController extends Manage_CommonController
{

    public function doRequest()
    {
        $groupId = $_POST['groupId'];
        $updateKey = $_POST['key'];
        $updateValue = $_POST['value'];

        $response = [
            'errCode' => "error"
        ];

        try {

            switch ($updateKey) {
                case "groupId":
                    throw new Exception("update groupId error");
                case "name":
                case "groupName":
                    if (empty($updateValue)) {
                        throw new Exception("group-name is empty");
                    }

                    $groupName = $updateValue;
                    $pinyin = new \Overtrue\Pinyin\Pinyin();
                    $groupNameInLatin = $pinyin->permalink($groupName, "");;

                    if ($this->updateGroupName($groupId, $groupName, $groupNameInLatin)) {
                        $response['errCode'] = "success";
                    }
                    break;
                case "maxMembers":
                    if (empty($updateValue)) {
                        throw new Exception("maxMembers is null");
                    }

                    $siteGroupMaxMembers = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_MAX_GROUP_MEMBERS);

                    if (empty($siteGroupMaxMembers)) {
                        $siteGroupMaxMembers = 100;
                    }

                    $updateValue = min($updateValue, $siteGroupMaxMembers);

                    if ($this->updateGroupProfile($groupId, $updateKey, $updateValue)) {
                        $response['errCode'] = "success";
                    }
                    break;
                case "avatar":
                    // TODO
                    break;
                case "enableShareGroup":
                    // TODO
//                    if ($this->updateGroupProfile($groupId, $updateKey, $updateValue)) {
//                        $response['errCode'] = "success";
//                    }

                    break;
                case "addDefaultGroup":

                    $flag = true;
                    if ($updateValue == 1) {
                        $flag = $this->ctx->Site_Config->addDefaultGroup($groupId);
                    } else {
                        $flag = $this->ctx->Site_Config->removeDefaultGroup($groupId);
                    }

                    if ($flag) {
                        $response['errCode'] = "success";
                    }

                    break;
                default:
                    break;
            }

        } catch (Exception $e) {
            $response['errInfo'] = $e->getMessage();
            $this->ctx->Wpf_Logger->error("manage.user.update", $e);
        }

        echo json_encode($response);
        return;
    }

    private function updateGroupName($groupId, $newName, $newNameInLatin)
    {
        $where = [
            'groupId' => $groupId,
        ];

        $data = [
            'name' => $newName,
            'nameInLatin' => $newNameInLatin,
        ];

        return $this->ctx->SiteGroupTable->updateGroupInfo($where, $data);
    }

    private function updateGroupProfile($groupId, $updateKey, $updateValue)
    {
        $where = [
            'groupId' => $groupId,
        ];

        $data = [
            $updateKey => $updateValue,
        ];

        return $this->ctx->SiteGroupTable->updateGroupInfo($where, $data);
    }

}