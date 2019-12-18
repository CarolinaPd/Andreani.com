<?php

namespace FrontBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Resources\Traits\EnvironmentTrait;
use Parsedown;

class FrontTwigExtension extends \Twig_Extension {

    use EnvironmentTrait;

    public function __construct(ContainerInterface $container, EntityManager $em) {
        $this->container = $container;
        $this->em = $em;
    }

    public function getFunctions() {
        return array(
            'getCodigoAnalytics' => new \Twig_Function_Method($this, 'getCodigoAnalytics'),
            'markdownToHtml' => new \Twig_Function_Method($this, 'markdownToHtml'),
        );
    }

    public function getCodigoAnalytics() {
        if ($this->isProdEnvironment()) {
            return '';
        } else {
            return '';
        }
    }

    public function markdownToHtml($text) {
        $parsedown = new Parsedown();
        echo $parsedown->text($text);
    }

}
