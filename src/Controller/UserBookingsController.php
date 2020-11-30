<?php


namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingTypeStep1;
use App\Form\BookingTypeStep2;
use App\Repository\BookingRepository;
use App\Repository\CategoryRepository;
use App\Repository\EquipmentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Registry;

/**
 * @Route({
 *     "en": "/bookings",
 *     "fr": "/reservations",
 * })
 */
class UserBookingsController  extends AbstractController
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
     * @Route("/", name="booking_list", methods={"GET"})
     */
    public function index(Security $security, UserRepository $userRepository): Response
    {
        return $this->render('booking/list.html.twig', [
            $id = $security->getUser()->getId(),
            'bookings' => $userRepository->findOneBy(['id'=>$id])->getBookings()
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

        return $this->redirectToRoute('booking_list');
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
}