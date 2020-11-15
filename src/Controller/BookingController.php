<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Equipement;

use App\Form\BookingType;

use App\Repository\BookingRepository;

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
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        if ($request->isMethod('POST')) {

            // set equipements to booking
            $equipements = $request->request->get('booking')['equipements'];

            // redirect
            return $this->redirectToRoute('booking_new_step2', array('equipements' => $equipements));
        }
        
        // get categories
        $categoryRepository = $em->getRepository(Category::class);
        $categories = $categoryRepository->findAll();


        return $this->render('booking/newStape1.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/step2",
     *     "fr": "/ajouter/etape2",
     * }, name="booking_new_step2", methods={"GET","POST"})
     */
    public function newStep2(Request $request) {

        $equipements = $request->query->get('equipements');

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        if ($request->isMethod('POST')) {

            $bookingTab = [
                'loaningDate' => $request->request->get('booking')['loaningDate'],
                'returnDate' => $request->request->get('booking')['returnDate'],
                'equipementsId' => $equipements
            ];

            // dd($bookingTab);

            // redirect
            return $this->redirectToRoute('booking_new_step3', array('bookingTab' => $bookingTab));
        }

        return $this->render('booking/newStape2.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route({
     *     "en": "/add/step3",
     *     "fr": "/ajouter/etape3",
     * }, name="booking_new_step3", methods={"GET","POST"})
     */
    public function newStep3(Request $request, EntityManagerInterface $em) {

        $bookingTab = $request->query->get('bookingTab');

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $equipementRepository = $em->getRepository(Equipement::class);
        $bookingTab['equipements'] = [];
        $categories = [];
        foreach($bookingTab['equipementsId'] as $equipement) {
            $currentEq = $equipementRepository->find($equipement);
            array_push($bookingTab['equipements'], $currentEq);
            if(!in_array($currentEq->getCategory(), $categories, true)){
                array_push($categories, $currentEq->getCategory());
            }
        };

        if ($request->isMethod('POST')) {

            // redirect
            return $this->redirectToRoute('booking_save', array('bookingTab' => $bookingTab));
        };

        return $this->render('booking/newStape3.html.twig', [
            'form' => $form->createView(),
            'booking' => $bookingTab,
            'categories' => $categories
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

        $equipementRepository = $em->getRepository(Equipement::class);
        foreach($bookingTab['equipementsId'] as $equipement) {
            $currentEq = $equipementRepository->find($equipement);
            $booking->addEquipement($currentEq);
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
