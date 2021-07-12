<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        $articleUne = $this->getDoctrine()->getRepository(Article::class)->findBy([],['created_at' => 'desc'], 1, 0);

        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy([],['created_at' => 'desc'], 4, 1);

        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([],['title' => 'desc']);
        
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findBy([],['title' => 'desc']);
        
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'articleUne' => $articleUne,
            'categories' => $categories,
            'tags' => $tags,
            'current_menu' => 'Accueil',
        ]);
    }

    /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos()
    {
        return $this->render('home/apropos.html.twig', [
            'current_menu' => 'A propos',
        ]);
    }

    /**
     * @Route("cgu", name="cgu")
     */
    public function cgu()
    {
        return $this->render('home/cgu.html.twig', [
            'current_menu' => 'Cgu',
        ]);
    }
}
