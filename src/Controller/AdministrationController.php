<?php


namespace App\Controller;


use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administration")
 */
class AdministrationController extends AbstractController
{
    /**
     * @Route("/", name="administration_index")
     */
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('administration/index.html.twig');
    }
}