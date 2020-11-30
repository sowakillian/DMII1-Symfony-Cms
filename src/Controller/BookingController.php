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
     * @Route({
     *     "en": "/add/step1",
     *     "fr": "/ajouter/etape1",
     * }, name="booking_new_step1", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingTypeStep1::class, $booking, ['validation_groups' => ['creation']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setUser($this->get('security.token_storage')->getToken()->getUser());

            $this->entityManager->persist($booking);
            $this->entityManager->flush();


            return $this->redirectToRoute('booking_new_step2', ['id' => $booking->getId()]);
        }
        
        return $this->render('booking/new/step1.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/step2/{id}",
     *     "fr": "/ajouter/etape2/{id}",
     * }, name="booking_new_step2", methods={"GET","POST"})
     */
    public function newStep2(Request $request, Booking $booking, CategoryRepository $categoryRepository, EquipmentRepository $equipmentRepository) {

        $form = $this->createForm(BookingTypeStep2::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($form->getData()->getEquipments() as $equipment) {
                $equipment->addBooking($booking);
            }
            $this->entityManager->flush();

            // redirect
            return $this->redirectToRoute('booking_new_step3', ['id' => $booking->getId()]);
        }

        $categories = $categoryRepository->findAll();

        return $this->render('booking/new/step2.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/step3/{id}",
     *     "fr": "/ajouter/etape3/{id}",
     * }, name="booking_new_step3", methods={"GET","POST"})
     */
    public function newStep3(Request $request, Booking $booking, BookingRepository $bookingRepository) {

        // $bId = $request->query->get('bookingId');
        // $booking = $bookingRepository->find($bId);

        $categories = [];
        foreach($booking->getEquipments() as $eq) {
            $category = $eq->getCategory();
            if (!in_array($category, $categories)) {
                array_push($categories, $category);
            }
        }

        return $this->render('booking/new/step3.html.twig', [
            'categories' => $categories,
            'booking' => $booking
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/save/{id}",
     *     "fr": "/ajouter/valider",
     * }, name="booking_save", methods={"GET","POST"})
     */
    public function save(Request $request, BookingRepository $bookingRepository) {
        $bookingId = $request->query->get('id');
        $booking = $bookingRepository->find($bookingId);

        $workflow = $this->workflows->get($booking, 'booking_creating');

        try {
            $workflow->apply($booking, 'to_review');
        } catch (LogicException $exception) {
            // ...
        }

        return $this->redirectToRoute('home_index');
    }

    /**
     * @Route({
     *     "en": "/add/cancel",
     *     "fr": "/ajouter/annuler",
     * }, name="booking_cancel", methods={"GET","POST"})
     */
    public function cancel(Request $request, BookingRepository $bookingRepository) {
        $bookingId = $request->query->get('id');
        $booking = $bookingRepository->find($bookingId);

        $this->entityManager->remove($booking);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('home_index');
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
