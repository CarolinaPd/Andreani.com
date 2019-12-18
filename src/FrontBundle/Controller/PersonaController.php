<?php

namespace FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/")
 */
class PersonaController extends Controller {

    /**
     * @Route("/", name="front_persona")
     */
    public function defaultAction(Request $request) {
        $articulos = $this->container->get('cms')->getArticulos("personas");
        $banners = $this->container->get('cms')->getBanners("personas");

        return $this->render('FrontBundle:Persona:index.html.twig', array(
            'articulos' => $articulos,
            'banners' => $banners
        ));
    }

    /**
     * @Route("/articulo/{slug}", name="front_persona_articulo")
     */
    public function articuloAction($slug) {
        $articulo = $this->container->get('cms')->getArticulo($slug);

        if ($articulo) {
            return $this->render('FrontBundle:Persona:articulo.html.twig', array(
                'articulo' => $articulo
            ));
        }

        return $this->redirect($this->generateUrl('front_persona'));
    }
}
