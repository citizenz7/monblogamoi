<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request)
    {
        // Nous récupérons le nom d'hôte depuis l'URL
        $hostname = $request->getSchemeAndHttpHost();

        // On initialise un tableau pour lister les URLs
        $urls = [];

        // On ajoute les URLs "statiques"
        $urls[] = ['loc' => $this->generateUrl('home')];
        $urls[] = ['loc' => $this->generateUrl('article_index')];
        $urls[] = ['loc' => $this->generateUrl('category_index')];
        $urls[] = ['loc' => $this->generateUrl('tag_index')];
        $urls[] = ['loc' => $this->generateUrl('apropos')];
        $urls[] = ['loc' => $this->generateUrl('contact')];
        $urls[] = ['loc' => $this->generateUrl('cgu')];
        $urls[] = ['loc' => $this->generateUrl('rss_feed')];
        $urls[] = ['loc' => $this->generateUrl('search')];

        // add dynamic urls, like blog posts from your DB
        foreach ($this->getDoctrine()->getRepository(Article::class)->findAll() as $article) {
            $created_at = $article->getCreatedAt();

            $images = [
                'loc' => '/uploads/images/articles/'.$article->getImage(), // URL to image
                'title' => $article->getTitle()    // Optional, text describing the image
            ];

            $urls[] = [
                'loc' => $this->generateUrl('article_show', [
                    'slug' => $article->getSlug(),
                ]),
                //'lastmod' => $article->getUpdatedAt()->format('Y-m-d'),
                'image' => $images
            ];
        }


        // Fabrication de la réponse XML
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname]
            )
        );

        // Ajout des entêtes
        $response->headers->set('Content-Type', 'text/xml');

        // On envoie la réponse
        return $response;
    }
}