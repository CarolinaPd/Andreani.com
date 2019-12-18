<?php

namespace AppBundle\Resources\Traits;

use Symfony\Component\Yaml\Yaml;

trait EnvironmentTrait {

    public function getData($module, $identifier) {
        $yamlString = file_get_contents($this->getDataFilename());
        $yamlResult = Yaml::parse($yamlString);
        $data = $yamlResult[$module][$identifier];
        return $data;
    }

    protected function getDataFilename() {
        if ($this->isDevEnvironment()) return dirname(__FILE__) . '/config/data_dev.yml';
        if ($this->isProdEnvironment()) return dirname(__FILE__) . '/config/data_prod.yml';
    }

    protected function isDevEnvironment(){
        $container = isset($this->container) ? $this->container : $this->getContainer();
        $host = $container->get('router')->getContext()->getHost();
        $kernelEnvironment = $container->get('kernel')->getEnvironment();
        if (strpos($host, '.local') !== false || strpos($host, '.test') !== false || $kernelEnvironment == 'dev') return true;
        return false;
    }

    protected function isProdEnvironment(){
        $container = isset($this->container) ? $this->container : $this->getContainer();
        $kernelEnvironment = $container->get('kernel')->getEnvironment();
        if (!$this->isDevEnvironment() && $kernelEnvironment == 'prod') return true;
        return false;
    }

}
