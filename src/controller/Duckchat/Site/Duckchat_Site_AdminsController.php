<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 2018/11/28
 * Time: 10:37 AM
 */

class Duckchat_Site_AdminsController extends Duckchat_MiniProgramController
{

    private $classNameForRequest = '\Zaly\Proto\Plugin\DuckChatSiteAdminsRequest';
    private $classNameForResponse = '\Zaly\Proto\Plugin\DuckChatSiteAdminsResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {

        $response = new Zaly\Proto\Plugin\DuckChatSiteAdminsResponse();

        try {
            $siteManagers = $this->ctx->Site_Config->getSiteManagers();

            $userProfiles = $this->ctx->SiteUserTable->getUserByUserIds($siteManagers);

            $publicArray = [];
            if (!empty($userProfiles)) {
                foreach ($userProfiles as $profileInfo) {
                    $publicProfile = $this->getPublicUserProfile($profileInfo);
                    $publicArray[] = $publicProfile;
                }
            }

            $response->setPublicProfiles($publicArray);
            $this->returnSuccessRPC($response);
        } catch (Exception $e) {
            $this->logger->error($this->action, $e);
            $this->returnErrorRPC($response, $e);
        }

        return;
    }

}