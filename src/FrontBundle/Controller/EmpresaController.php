<?php

namespace FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/empresas")
 */
class EmpresaController extends Controller {

    /**
     * @Route("/", name="front_empresa")
     */
    public function defaultAction(Request $request) {
        $productos = $this->container->get('cms')->getArticulos("empresas");
        $banners = $this->container->get('cms')->getBanners("empresas");

        return $this->render('FrontBundle:Empresa:index.html.twig', array(
            'productos' => $productos,
            'banners' => $banners
        ));
    }

    /**
     * @Route("/producto/{slug}", name="front_empresa_producto")
     */
    public function productoAction($slug) {
        $producto = $this->container->get('cms')->getArticulo($slug);

        if ($producto) {
            return $this->render('FrontBundle:Empresa:producto.html.twig', array(
                'producto' => $producto
            ));
        }

        return $this->redirect($this->generateUrl('front_empresa'));
    }

    /**
     * @Route("/segmento/{slug}", name="front_empresa_segmento")
     */
    public function segmentoAction($slug) {
        $segmento = $this->container->get('cms')->getLanding($slug);

        if ($segmento) {
            return $this->render('FrontBundle:Empresa:segmento.html.twig', array(
                'segmento' => $segmento
            ));
        }

        return $this->redirect($this->generateUrl('front_empresa'));
    }

    /**
     * @Route("/servicio/{slug}", name="front_empresa_servicio")
     */
    public function servicioAction($slug) {
        $servicio = $this->container->get('cms')->getLanding($slug);

        if ($servicio) {
            return $this->render('FrontBundle:Empresa:servicio.html.twig', array(
                'servicio' => $servicio
            ));
        }

        return $this->redirect($this->generateUrl('front_empresa'));
    }

}
