<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 28/08/2018
 * Time: 6:40 PM
 */

class Manage_MiniProgram_AddController extends Manage_CommonController
{

    protected function doRequest()
    {
        $params = [];
        $params['lang'] = $this->language;

        $type = $_GET['type'];

        if ("save" == $type) {
            $name = $_POST['name'];
            $logo = "logo";
            $order = $_POST['order'];
            $landingPageUrl = $_POST['landingPageUrl'];
            $withProxy = $_POST['withProxy'];
            $usageType = $_POST['usageType'];
            $loadingType = $_POST['loadingType'];
            $permissionType = $_POST['permissionType'];
            $openSecretKey = $_POST['secretKey'];
            $management = $_POST['management'];

            $data = [
                'name' => $name,
                'logo' => $logo,
                'sort' => $order,
                'landingPageUrl' => $landingPageUrl,
                'landingPageWithProxy' => $withProxy,
                'usageType' => $usageType,
                'permissionType' => $permissionType,
                'addTime' => $this->ctx->ZalyHelper->getMsectime(),
                'authKey' => "",
                'management' => $management,
            ];

            if ($loadingType) {
                $data['loadingType'] = $loadingType;
            } else {
                $data['loadingType'] = 0;
            }

            if ($openSecretKey) {
                $data['authKey'] = $this->ctx->ZalyHelper->generateStrKey(32);
            }

            $this->ctx->Wpf_Logger->info("add mini program", json_encode($data));

            $saveSuccess = $this->saveMiniProgram($data);

            $result = [
                'errCode' => "error",
            ];

            if ($saveSuccess) {
                $result['errCode'] = "success";
            } else {
                $result['errInfo'] = "save mini program to db error";
            }

            echo json_encode($result);
            return;
        } else {
            //type = page
            $this->ctx->Wpf_Logger->info("add mini program", json_encode($params));
            echo $this->display("manage_miniProgram_add", $params);
        }
        return;
    }

    private function saveMiniProgram($miniProgramProfile)
    {
        $maxPlugin = $this->ctx->SitePluginTable->getMaxPluginId();
        $nextPluginId = $maxPlugin + 1;

        $miniProgramProfile['pluginId'] = $nextPluginId;

        $this->ctx->Wpf_Logger->info("save mini program", "data=" . json_encode($miniProgramProfile));

        return $this->ctx->SitePluginTable->insertMiniProgram($miniProgramProfile);
    }
}