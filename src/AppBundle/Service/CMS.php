<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Resources\Traits\EnvironmentTrait;

class CMS {

    use EnvironmentTrait;

    protected $container;
    protected $em;

    public function __construct(ContainerInterface $container, EntityManager $em) {
        $this->container = $container;
        $this->em = $em;
    }

    public function getBanners($carpeta) {
        $banners = $this->container->get('curl')->get($this->getData("cms", "url") . "banners/" . $carpeta . "&sort_by=position:asc" . $this->getRandParam(), null, null, null);
        $banners = $banners->stories;

        return $banners;
    }

    public function getArticulos($categoria) {
        $articulos = $this->container->get('curl')->get($this->getData("cms", "url") . "articulos/&filter_query[pages][in_array]=" . $categoria . "&sort_by=first_published_at:desc" . $this->getRandParam(), null, null, null);
        $articulos = $articulos->stories;

        return $articulos;
    }
    
    public function getArticulo($slug) {
        $articulos = $this->container->get('curl')->get($this->getData("cms", "url") . "articulos/" . $slug . $this->getRandParam(), null, null, null);
        $articulos = $articulos->stories;
        
        return $articulos ? $articulos[0] : null;
    }
    
    public function getLanding($slug) {
        $landings = $this->container->get('curl')->get($this->getData("cms", "url") . "landings/" . $slug . $this->getRandParam(), null, null, null);
        $landings = $landings->stories;

        return $landings ? $landings[0] : null;
    }
    
    protected function getRandParam() {
        return $this->getData("cms", "cache") ? "" : "&rand=" .rand();
    }

}
