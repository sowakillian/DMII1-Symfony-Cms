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
    public function new()
    {
        // creates a task object and initializes some data for this example
        $comment = new Comment();
        $comment->setContent('Content of comment');
        $comment->setTitle('BlogTitle');

        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('comment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}