<?php

namespace AppBundle\Resources\Libs;

use Symfony\Component\HttpFoundation\Response;

class FileDownloadResponse extends Response {

    public function __construct($filename, $content = NULL) {
        parent::__construct();
        $this->setHeaders($filename);
        $this->sendHeaders();
        if (!$content){
            $content = file_get_contents($filename);
        }
        $this->setContent($content);
    }

    private function setHeaders($filename){
        $this->headers->set('Cache-Control', 'private');
        $this->headers->set('Content-type', "mime_content_type($filename)");
        $this->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
    }

}
