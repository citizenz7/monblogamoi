<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Upload image
            $uploadedFile = $form['image']->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter("articles_images_directory");

                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $article->setImage($newFilename);
            }

            // Article creation date and time
            $article->setCreatedAt(new \DateTimeImmutable());

            // Article author (the one who's connected)
            $article->setAuthor($this->getUser());

            // Set views number to 1
            $article->setViews('1');
                        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        // Set +1 view for each visit
        $read = $article->getViews() +1;
        $article->setViews($read);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Upload image
            $uploadedFile = $form['image']->getData();

            if ($uploadedFile) {
                $image = $article->getImage();

                // Delete "old" image if exists on disk
                if($image) {
                    $nomImage = $this->getParameter("articles_images_directory") . '/' . $image;
                    if(file_exists($nomImage)) {
                        unlink($nomImage);
                    }
                }

                $destination = $this->getParameter("articles_images_directory");

                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $article->setImage($newFilename);
            }

            // Modification date and time
            $article->setUpdatedAt(new \DateTimeImmutable());

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}/delimage", name="article_delete_image", methods={"GET"})
     */
    public function deleteImage(Request $request, Article $article): Response
    {
        // Delete article's image in folder
        $image = $article->getImage();
        if($image) {
            $nomImage = $this->getParameter("articles_images_directory") . '/' . $image;
            if(file_exists($nomImage)) {
                unlink($nomImage);
            }
        }

        // Set image to "nothing" in DB
        $article->setImage('');       
        $this->getDoctrine()->getManager()->flush();

        // Redirect to edit page
        $this->addFlash('image_delete', 'L\'image de l\'article a été supprimée avec succès.');
        return $this->redirectToRoute('article_edit', ['id' => $article->getId()]);
    }

    /**
     * @Route("/{slug}", name="article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article): Response
    {
        // Delete image on disk when deleting article
        $image = $article->getImage();
        if($image) {
            $nomImage = $this->getParameter("articles_images_directory") . '/' . $image;
            if(file_exists($nomImage)) {
                unlink($nomImage);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }
}
