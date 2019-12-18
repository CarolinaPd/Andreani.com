<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Curl {

    protected $container;
    protected $em;

    public function __construct(ContainerInterface $container, EntityManager $em) {
        $this->container = $container;
        $this->em = $em;
        $this->curl = null;
    }

    public function initCurl() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
        return $this->curl;
    }

    public function closeCurl() {
        curl_close($this->curl);
    }

    public function getCurl() {
        //return $this->curl ? $this->curl : $this->initCurl();
        return $this->initCurl();
    }

    public function get($url, $bearer, $token) {
        $url = str_replace(' ', '%20', $url);
        $oauthConect = $this->getCurl();

        curl_setopt($oauthConect, CURLOPT_URL, $url);
        curl_setopt($oauthConect, CURLOPT_HTTPHEADER, $this->getHttpHeaders($bearer, $token));

        $oauthContent = curl_exec($oauthConect);
        $oauthHttpcodigo = curl_getinfo($oauthConect, CURLINFO_HTTP_CODE);

        return $this->codeAnalysis($oauthHttpcodigo, $oauthContent);
    }

    public function post($url, $data_json, $bearer, $token) {
        $oauthConect = $this->getCurl();

        curl_setopt($oauthConect, CURLOPT_URL, $url);
        curl_setopt($oauthConect, CURLOPT_POST, 1);
        curl_setopt($oauthConect, CURLOPT_POSTFIELDS, $data_json ?: null);
        curl_setopt($oauthConect, CURLOPT_HTTPHEADER, $this->getHttpHeaders($bearer, $token));

        $oauthContent = curl_exec($oauthConect);
        $oauthHttpcodigo = curl_getinfo($oauthConect, CURLINFO_HTTP_CODE);

        return $this->codeAnalysis($oauthHttpcodigo, $oauthContent);
    }

    public function put($url, $data_json, $bearer, $token) {
        $oauthConect = $this->getCurl();

        curl_setopt($oauthConect, CURLOPT_URL, $url);
        curl_setopt($oauthConect, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($oauthConect, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($oauthConect, CURLOPT_HTTPHEADER, $this->getHttpHeaders($bearer, $token));

        $oauthContent = curl_exec($oauthConect);
        $oauthHttpcodigo = curl_getinfo($oauthConect, CURLINFO_HTTP_CODE);

        return $this->codeAnalysis($oauthHttpcodigo, $oauthContent);
    }

    public function patch($url, $data_json, $bearer, $token) {
        $oauthConect = $this->getCurl();

        curl_setopt($oauthConect, CURLOPT_URL, $url);
        curl_setopt($oauthConect, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($oauthConect, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($oauthConect, CURLOPT_HTTPHEADER, $this->getHttpHeaders($bearer, $token));

        $oauthContent = curl_exec($oauthConect);
        $oauthHttpcodigo = curl_getinfo($oauthConect, CURLINFO_HTTP_CODE);

        return $this->codeAnalysis($oauthHttpcodigo, $oauthContent);
    }

    public function delete($url, $bearer, $token) {
        $oauthConect = $this->getCurl();

        curl_setopt($oauthConect, CURLOPT_URL, $url);
        curl_setopt($oauthConect, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($oauthConect, CURLOPT_HTTPHEADER, $this->getHttpHeaders($bearer, $token));

        $oauthContent = curl_exec($oauthConect);
        $oauthHttpcodigo = curl_getinfo($oauthConect, CURLINFO_HTTP_CODE);

        return $this->codeAnalysis($oauthHttpcodigo, $oauthContent);
    }

    public function getBasicToken($url, $username, $password) {
        $oauthConect = $this->getCurl();

        curl_setopt($oauthConect, CURLOPT_URL, $url);
        curl_setopt($oauthConect, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($oauthConect, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($oauthConect, CURLOPT_HEADER, true);

        $oauthContent = curl_exec($oauthConect);
        $oauthHttpcodigo = curl_getinfo($oauthConect, CURLINFO_HTTP_CODE);

        $this->codeAnalysis($oauthHttpcodigo, $oauthContent);

        return $this->getContentHeaderByName($oauthContent, "X-Authorization-token");
    }

    public function getContentHeaderByName($content, $headerName) {
        list($headers, $content) = explode("\r\n\r\n", $content, 2);

        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, $headerName) !== false) {
                $headerArray = explode(" ", $header);
                return $headerArray[1];
            }
        }
    }

    protected function getHttpHeaders($bearer, $token, $fileType = null) {
        $arrayHttpHeaders = array();

        $arrayHttpHeaders[] = "Content-Type: application/json";

        if ($fileType) {
            $arrayHttpHeaders[] = 'Accept: ' . $fileType;
        } else {
            $arrayHttpHeaders[] = 'Accept: application/json';
        }

        if ($bearer) {
            $arrayHttpHeaders[] = 'Authorization: Bearer ' . $bearer;
        }

        if ($token) {
            $arrayHttpHeaders[] = 'X-Authorization-token: ' . $token;
        }

        return $arrayHttpHeaders;
    }

    protected function codeAnalysis($oauthHttpcodigo, $oauthContent) {
        if (is_null($oauthHttpcodigo)) {
            return json_decode($oauthContent);
        }

        switch ($oauthHttpcodigo) {
        case 200:
            return json_decode($oauthContent);
        case 201:
            return json_decode($oauthContent);
        case 202:
            return json_decode($oauthContent);
        case 203:
            return json_decode($oauthContent);
        case 204:
            return json_decode($oauthContent);
        case 400:
            throw new BadRequestHttpException($this->getErrorMessage($oauthContent));
        case 404:
            throw new NotFoundHttpException($this->getErrorMessage($oauthContent));
        case 405:
            throw new NotFoundHttpException($this->getErrorMessage($oauthContent));
        case 409:
            throw new ConflictHttpException($this->getErrorMessage($oauthContent));
        case 422:
            throw new UnprocessableEntityHttpException($this->getErrorMessage($oauthContent));
        case 500:
            throw new \Exception($this->getErrorMessage($oauthContent));
        case 503:
            throw new ServiceUnavailableHttpException();
        default:
            throw new ServiceUnavailableHttpException();
        }
    }

    private function getErrorMessage($oauthContent) {
        return $this->isJson($oauthContent) && property_exists(json_decode($oauthContent), 'message') ? json_decode($oauthContent)->message : $oauthContent;
    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}
