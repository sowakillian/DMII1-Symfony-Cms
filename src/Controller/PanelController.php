<?php


namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class PanelController extends AbstractController
{
    /**
     * @Route("/", name="panel_index")
     */
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('panel/index.html.twig');
    }
}