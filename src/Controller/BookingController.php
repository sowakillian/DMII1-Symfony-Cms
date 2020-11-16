<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Equipment;

use App\Form\BookingTypeStep1;
use App\Form\BookingTypeStep2;
use App\Form\BookingTypeStep3;

use App\Repository\BookingRepository;
use App\Repository\CategoryRepository;
use App\Repository\EquipmentRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route({
 *     "en": "/administration/bookings",
 *     "fr": "/administration/reservations",
 * })
 */
class BookingController extends AbstractController
{
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

            $booking->setStatus('loading');
            $booking->setUser($this->get('security.token_storage')->getToken()->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('booking_new_step2', ['bookingId' => $booking->getId()]);
        }
        
        return $this->render('booking/new/step1.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/step2",
     *     "fr": "/ajouter/etape2",
     * }, name="booking_new_step2", methods={"GET","POST"})
     */
    public function newStep2(Request $request, BookingRepository $bookingRepository, CategoryRepository $categoryRepository, EquipmentRepository $equipmentRepository) {

        $bId = $request->query->get('bookingId');

        $booking = $bookingRepository->find($bId);
        $form = $this->createForm(BookingTypeStep2::class, $booking, ['validation_groups' => ['equipments']]);

        if ($form->isSubmitted() && $form->isValid()) {

            dd('saving');

            $eqs = $request->request->get('booking_type_step2')['equipments'];
            foreach($eqs as $eq) {
                $currentEq = $equipmentRepository->find($eq);
                $booking->addEquipment($currentEq);
            };

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            // redirect
            return $this->redirectToRoute('booking_new_step3', ['bookingId' => $booking->getId()]);
        }

        $categories = $categoryRepository->findAll();

        return $this->render('booking/new/step2.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/step3",
     *     "fr": "/ajouter/etape3",
     * }, name="booking_new_step3", methods={"GET","POST"})
     */
    public function newStep3(Request $request, BookingRepository $bookingRepository) {

        $bId = $request->query->get('bookingId');
        $booking = $bookingRepository->find($bId);
        $form = $this->createForm(BookingTypeStep3::class, $booking);

        if ($form->isSubmitted() && $form->isValid()) {

            dd('update status and save');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            // redirect
            return $this->redirectToRoute('home_index');
        }

        dd($booking);

        $categories = [];
        foreach($booking->getEquipments() as $eq) {
            $category = $eq->getCategory();
            if (!in_array($category, $categories)) {
                array_push($categories, $category);
            }
        }

        return $this->render('booking/new/step3.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'booking' => $booking
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/save",
     *     "fr": "/ajouter/valider",
     * }, name="booking_save", methods={"GET","POST"})
     */
    public function save(Request $request, EntityManagerInterface $em) {
        $bookingTab = $request->query->get('bookingTab');

        $entityManager = $this->getDoctrine()->getManager();

        $booking = new Booking();

        $equipmentRepository = $em->getRepository(Equipment::class);
        foreach($bookingTab['equipmentsId'] as $equipment) {
            $currentEq = $equipmentRepository->find($equipment);
            $booking->addEquipment($currentEq);
        };

        $booking->setLoaningDate($this->convertArrayTodatetime($bookingTab['loaningDate']));
        $booking->setReturnDate($this->convertArrayTodatetime($bookingTab['returnDate']));

        $booking->setUser($this->get('security.token_storage')->getToken()->getUser());
        $booking->setStatus('waiting');

        $entityManager->persist($booking);
        $entityManager->flush();
        
        return $this->redirectToRoute('home_index');
    }

    private function convertArrayTodatetime($array) {
        $date = $array['date'];
        $year = $date['year'];
        $month = $date['month'];
        $day = $date['day'];

        $time = $array['time'];
        $hour = $time['hour'];
        $minute = $time['minute'];

        $str = $year. '-' . $month. '-' . $day. ' ' . $hour. ':' . $minute;

        return \DateTime::createFromFormat("Y-m-d H:i",$str);
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
