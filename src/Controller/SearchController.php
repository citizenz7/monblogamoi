<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index()
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController'
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchBar(): Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('handleSearch'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Recherche',
                    'size' => '12'
                ]
            ])
            ->getForm();

        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/handleSearch", name="handleSearch")
     * @param Request $request
     * @param \App\Repository\ArticleRepository $repo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleSearch(Request $request, ArticleRepository $repo): Response
    {
        $query = $request->request->get('form')['query'];

        if($query) {
            $articles = $repo->findArticlesByName($query);
        }

        return $this->render('search/index.html.twig', [
            'articles' => $articles
        ]);
    }
}
