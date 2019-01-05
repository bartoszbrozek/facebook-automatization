<?php

namespace App\Service;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Component\HttpFoundation\Session\Session;

class FacebookService
{
    protected $session;
    private $appid;
    private $appSecret;
    private $defaultGraphVersion;
    private $authLink;

    /**
     * @var Facebook
     */
    private $fb;

    /**
     * FacebookService constructor.
     * @param Session $session
     * @param string $appid
     * @param string $appSecret
     * @param string $defaultGraphVersion
     * @param string $authLink
     */
    public function __construct(Session $session, string $appid, string $appSecret, string $defaultGraphVersion, string $authLink)
    {
        $this->appid = $appid;
        $this->appSecret = $appSecret;
        $this->defaultGraphVersion = $defaultGraphVersion;
        $this->authLink = $authLink;
        $this->session = $session;

        $this->createFacebookObject();
    }

    /**
     * Creates Facebook object
     */
    private function createFacebookObject()
    {
        try {
            $this->fb = new Facebook([
                'app_id' => $this->appid,
                'app_secret' => $this->appSecret,
                'default_graph_version' => $this->defaultGraphVersion
            ]);
        } catch (FacebookSDKException $ex) {
            $this->fb = null;
        }
    }

    /**
     * @return string|null
     * @throws FacebookSDKException
     */
    public function getFbAccessToken(): ?string
    {
        if ($this->session->has("fb_access_token")) {
            return $this->session->get("fb_access_token");
        }

        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            $this->session->getFlashBag()
                ->add("error", "Graph Error: " . $e->getMessage());
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            $this->session->getFlashBag()
                ->add("error", "Validation Error: " . $e->getMessage());
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";

                $this->session->getFlashBag()
                    ->add("error", "Access Token Error: 
                    Error: {$helper->getError()}
                    Error Code: {$helper->getErrorCode()}
                    Error Reason: {$helper->getErrorReason()}
                    Error Description: {$helper->getErrorDescription()}");
            } else {
                $this->session->getFlashBag()
                    ->add("error", "Bad Request");
            }

            return null;
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $this->fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($this->appid);

        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $this->session->getFlashBag()
                    ->add("error", "Access Token Error: " . $e->getMessage());

                return null;
            }
        }

        return (string)$accessToken;
    }

    /**
     * @return Facebook|null
     */
    protected function getFb(): ?Facebook
    {
        return $this->fb;
    }

    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = ['email'];
        return $helper->getLoginUrl($this->authLink, $permissions);
    }
}
