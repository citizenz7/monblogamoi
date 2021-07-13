<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category_index", methods={"GET"})
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/category", name="category_admin_index", methods={"GET"})
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function indexAdmin(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM App:Category p ORDER BY p.id DESC";
        $donnees = $em->createQuery($dql);

        $categories = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );

        return $this->render('category/index.admin.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/category/new", name="category_new", methods={"GET","POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $message = $translator->trans('Catégorie créée avec succès.');
            $this->addFlash('message', $message);

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/category/{slug}", name="category_show", methods={"GET"})
     * @param Category $category
     * @return Response
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/admin/category/{slug}/edit", name="category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Category $category
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Category $category, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $message = $translator->trans('Catégorie modifiée avec succès.');
            $this->addFlash('message', $message);

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/category/{slug}", name="category_delete", methods={"POST"})
     * @param Request $request
     * @param Category $category
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, Category $category, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        $message = $translator->trans('Catégorie supprimée avec succès.');
        $this->addFlash('message', $message);

        return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
    }
}
