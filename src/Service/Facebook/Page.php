<?php

namespace App\Service\Facebook;

use App\Service\FacebookService;
use Facebook\Exceptions\FacebookSDKException;

class Page extends FacebookService
{
    private $pageid;

    public function setPageId(int $pageid)
    {
        $this->pageid = $pageid;

        return $this;
    }

    public function getPageAccessToken(): ?string
    {
        if ($this->session->has("fb_page_access_token")) {
            return $this->session->get("fb_page_access_token");
        }

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->getFb()
                ->get("/{$this->pageid}?fields=access_token", $this->getFbAccessToken());

            $pageAccessToken = $response
                ->getGraphNode()
                ->getField("access_token");

            $this->session->set("fb_page_access_token", $pageAccessToken);

            return $pageAccessToken;

        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $this->session->getFlashBag()
                ->add("error", "Graph Error: " . $e->getMessage() . " ID: " . $e->getCode() . " RAW: " . $e->getRawResponse());
        } catch (FacebookSDKException $e) {
            $this->session->getFlashBag()
                ->add("error", "SDK Error: " . $e->getMessage());
        }

        return null;
    }

    public function createTest(): ?\Facebook\GraphNodes\GraphNode
    {
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->getFb()
                ->post("/me/accounts", [
                    'name' => 'Bartek Test Page 2',
                    'category' => '756092301147942',
                    'about' => 'Bartek test 777',
                    'picture' => 'https://www.petmd.com/sites/default/files/what-does-it-mean-when-cat-wags-tail.jpg',
                    'cover_photo' => '{"url":"https://www.petmd.com/sites/default/files/what-does-it-mean-when-cat-wags-tail.jpg"}'
                ], $this->getPageAccessToken());

            return $response->getGraphNode();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $this->session->getFlashBag()
                ->add("error", "Graph Error: " . $e->getMessage() . " ID: " . $e->getCode() . " RAW: " . $e->getRawResponse());
        } catch (FacebookSDKException $e) {
            $this->session->getFlashBag()
                ->add("error", "SDK Error: " . $e->getMessage());
        }

        return null;
    }

    public function createPost(string $message): ?\Facebook\GraphNodes\GraphNode
    {
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->getFb()
                ->post("/{$this->pageid}/feed", [
                    'message' => $message
                ], $this->getPageAccessToken());

            return $response->getGraphNode();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $this->session->getFlashBag()
                ->add("error", "Graph Error: " . $e->getMessage() . " ID: " . $e->getCode() . " RAW: " . $e->getRawResponse());
        } catch (FacebookSDKException $e) {
            $this->session->getFlashBag()
                ->add("error", "SDK Error: " . $e->getMessage());
        }

        return null;
    }
}
