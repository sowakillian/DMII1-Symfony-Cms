<?php
namespace App\Controller;

use App\Model\Comment;
use App\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class CommentController extends AbstractController
{
    /**
    * @Route("/form")
    */
    public function new(Request $request)
    {
        // creates a task object and initializes some data for this example
        $comment = new Comment();
        $comment->setContent('Title');
        $comment->setTitle('Content');

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $comment = $form->getData();
    
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();
    
            return $this->redirectToRoute('comment_validated');
        }

        return $this->render('comment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}