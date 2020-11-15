<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Entity\Media;
use App\Form\EquipementType;
use App\Service\FileUploader;
use App\Repository\EquipementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route({
 *     "en": "/administration/equipments",
 *     "fr": "/administration/materiel",
 * })
 */
class EquipementController extends AbstractController
{
    /**
     * @Route("/", name="equipement_index", methods={"GET"})
     */
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/index.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    /**
     * @Route({
     *     "en": "/add",
     *     "fr": "/ajouter",
     * }, name="equipement_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $equipement = new Equipement();
        $media = new Media();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile) {
                $media->setPath($fileUploader->upload($mediaFile));
                $media->setTitle($form->get('name')->getData());
                $equipement->setMedia($media);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($media);
            $entityManager->persist($equipement);
            $entityManager->flush();

            return $this->redirectToRoute('equipement_index');
        }

        return $this->render('equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipement_show", methods={"GET"})
     */
    public function show(Equipement $equipement): Response
    {
        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    /**
     * @Route({
     *     "en": "/{id}/edit",
     *     "fr": "/{id}/modifier",
     * }, name="equipement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Equipement $equipement): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('equipement_index');
        }

        return $this->render('equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Equipement $equipement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equipement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('equipement_index');
    }
}
