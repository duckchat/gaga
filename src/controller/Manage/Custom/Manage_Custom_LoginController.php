<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 17/10/2018
 * Time: 6:55 PM
 */

class Manage_Custom_LoginController extends Manage_ServletController
{

    protected function doGet()
    {
        $params['lang'] = $this->language;

        //2.loginConfig
        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();

        $loginWelcomeTextConfig = $loginConfig[LoginConfig::LOGIN_PAGE_WELCOME_TEXT];
        $loginWelcomeText = $loginWelcomeTextConfig["configValue"];

        $loginBackgroundColorConfig = $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_COLOR];
        $loginBackgroundColor = $loginBackgroundColorConfig["configValue"];

        $loginBackgroundImageConfig = $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE];
        $loginBackgroundImage = $loginBackgroundImageConfig["configValue"];

        $loginBackgroundImageDisplayConfig = $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE_DISPLAY];
        $loginBackgroundImageDisplay = $loginBackgroundImageDisplayConfig["configValue"];

        $params['loginWelcomeText'] = $loginWelcomeText;
        $params['loginBackgroundColor'] = $loginBackgroundColor;
        $params['loginBackgroundImage'] = $loginBackgroundImage;

        if (isset($loginBackgroundImageDisplay)) {
            $params['loginBackgroundImageDisplay'] = $loginBackgroundImageDisplay;
        } else {
            $params['loginBackgroundImageDisplay'] = 0;
        }

        echo $this->display("manage_custom_login", $params);

        return;
    }

    protected function doPost()
    {
        //response
        $result = [
            'errCode' => "error",

        ];

        try {
            $key = $_POST["key"];
            $value = $_POST["value"];

            if ($key == LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE) {
                $fileId = $value;
                $imageDir = WPF_LIB_DIR . "../public/site/image/";
                $this->ctx->File_Manager->moveImage($fileId, $imageDir);
            }

            $res = $this->ctx->Site_Custom->updateLoginConfig($key, $value, "", $this->userId);
            if ($res) {
                $result["errCode"] = "success";
            }
        } catch (Exception $e) {
            $result["errInfo"] = $e->getMessage();
            $this->logger->error($this->action, $e);

        }

        echo json_encode($result);
        return;
    }
}