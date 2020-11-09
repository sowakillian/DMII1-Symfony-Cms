<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route({
 *     "en": "/",
 *     "fr": "/",
 * })
 */
class HomeController  extends AbstractController
{
    /**
     * @Route("/", name="home_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('panel/index.html.twig');
    }
}