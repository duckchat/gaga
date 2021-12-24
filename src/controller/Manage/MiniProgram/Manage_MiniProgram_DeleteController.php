<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 28/08/2018
 * Time: 6:40 PM
 */

class Manage_MiniProgram_DeleteController extends Manage_CommonController
{
    private $innerPluginIds = [100, 101, 102, 104, 105];

    public function doRequest()
    {
        $result = [
            'errCode' => "error.alert",
            'errInfo' => "",
        ];

        $pluginId = $_POST['pluginId'];

        try {

            if (in_array($pluginId, $this->innerPluginIds)) {
                throw new Exception("forbidden operation");
            }

            if ($this->deleteMiniProgram($pluginId)) {
                $result['errCode'] = "success";
            }
        } catch (Throwable $e) {
            $this->logger->error("manage.miniprogram.delete", $e);
            $result["errInfo"] = $e->getMessage();
        }

        echo json_encode($result);
        return;
    }


    private function deleteMiniProgram($pluginId)
    {
        return $this->ctx->SitePluginTable->deletePlugin($pluginId);
    }
}