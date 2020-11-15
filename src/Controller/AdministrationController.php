<?php


namespace App\Controller;

use App\Repository\BookingRepository;
use App\Repository\EquipementRepository;
use App\Repository\UserRepository;
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
    public function index(UserRepository $userRepository, BookingRepository $bookingRepository, EquipementRepository $equipementRepository): Response
    {
        return $this->render('administration/index.html.twig', [
            'users' => $userRepository->findAll(),
            'bookings' => $bookingRepository->findAll(),
            'equipments' => $equipementRepository->findAll(),
        ]);
    }
}