<?php
namespace App\Controller;

use App\Model\Comment;
use App\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
    * @Route("/form")
    */
    public function new(Request $request)
    {
        // creates a task object and initializes some data for this example
        $comment = new Comment();
        $comment->setContent('Content of comment');
        $comment->setTitle('BlogTitle');

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();
    
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

    /**
    * @Route("/comment-validated", 
    *        name="comment_validated")
    */
    public function validated() 
    {
        return $this->render('comment/validated.html.twig');
    }
}