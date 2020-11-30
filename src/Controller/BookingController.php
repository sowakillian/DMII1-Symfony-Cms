<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Equipment;

use App\Form\BookingType;
use App\Form\BookingTypeStep1;
use App\Form\BookingTypeStep2;
use App\Form\BookingTypeStep3;

use App\Repository\BookingRepository;
use App\Repository\CategoryRepository;
use App\Repository\EquipmentRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @Route({
 *     "en": "/administration/bookings",
 *     "fr": "/administration/reservations",
 * })
 */
class BookingController extends AbstractController
{
    private $entityManager;
    private $workflows;

    /**
     * @var Registry
     */
    public function __construct(EntityManagerInterface $entityManager, Registry $workflows)
    {
        $this->entityManager = $entityManager;
        $this->workflows = $workflows;
    }


    /**
     * @Route("/", name="booking_index", methods={"GET"})
     */
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_show", methods={"GET"})
     */
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    /**
     * @Route({
     *     "en": "/{id}/edit",
     *     "fr": "/{id}/modifier",
     * }, name="booking_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_index');
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Booking $booking): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('booking_index');
    }
}
