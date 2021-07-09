<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article_index", methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM App:Article p ORDER BY p.created_at DESC";
        $donnees = $em->createQuery($dql);

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            8 // Nombre de résultats par page
        );

        return $this->render('article/index.html.twig', [
            //'articles' => $articleRepository->findAll(),
            'articles' => $articles,
            'current_menu' => 'Blog',
        ]);
    }

    /**
     * @Route("/admin/article", name="article_admin_index", methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function indexAdmin(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM App:Article p ORDER BY p.created_at DESC";
        $donnees = $em->createQuery($dql);

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );

        return $this->render('article/index.admin.html.twig', [
            //'articles' => $articleRepository->findAll(),
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/article/new", name="article_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
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

            $entityManager = $this->getDoctrine()->getManager();

            // Article creation date and time
            $article->setCreatedAt(new \DateTimeImmutable());

            // Article author (the one who's connected)
            $article->setAuthor($this->getUser());

            // Set views number to 1
            $article->setViews('1');

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'L\'article a été créé avec succès.');
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_show", methods={"GET","POST"})
     * @param Article $article
     * @return Response
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        // Set +1 view for each visit
        $read = $article->getViews() +1;
        $article->setViews($read);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        // Comments
        $comment = new Comment;
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        // Form
        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setArticle($article);

            // Fetch parentid content
            $parentid = $commentForm->get("parentid")->getData();

            $em = $this->getDoctrine()->getManager();

            // Fetch corresponding comment
            if ($parentid != null) {
                $parent = $em->getRepository(Comment::class)->find(($parentid));
            }

            // Define parent
            $comment->setParent($parent ?? null);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre commentaire.');
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView()
        ]);
    }

    /**
     * @Route("/admin/article/{slug}/edit", name="article_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Article $article
     * @return Response
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

            $this->addFlash('edited', 'Le message a été modifié avec succès.');
            return $this->redirectToRoute('article_admin_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{slug}/delimage", name="article_delete_image", methods={"GET"})
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
     * @Route("/admin/article/{slug}", name="article_delete", methods={"POST"})
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function delete(Request $request, Article $article): Response
    {
        // Delete image
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

        $this->addFlash('deleted', 'L\'article a été supprimé avec succès.');
        return $this->redirectToRoute('article_index');
    }
}