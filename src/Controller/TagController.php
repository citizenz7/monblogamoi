<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

class TagController extends AbstractController
{
    /**
     * @Route("/tag", name="tag_index", methods={"GET"})
     */
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/tag", name="tag_admin_index", methods={"GET"})
     */
    public function indexAdmin(TagRepository $tagRepository): Response
    {
        return $this->render('tag/index.admin.html.twig', [
            'tags' => $tagRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/tag/new", name="tag_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            $message = $translator->trans('Le tag a été créé avec succès.');
            $this->addFlash('message', $message);

            return $this->redirectToRoute('tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/tag/new/ajax/{label}", name="tag_new_ajax", methods={"POST"})
     */
    public function newTagAjax(string $label, EntityManagerInterface $em): Response 
    {
        $tag = new Tag();
        $tag->setTitle(trim(strip_tags($label)));
        $em->persist($tag);
        $em->flush();
        $id = $tag->getId();
        return new JsonResponse(['id' => $id]);
    }

    /**
     * @Route("/tag/{slug}", name="tag_show", methods={"GET"})
     */
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * @Route("/admin/tag/{slug}/edit", name="tag_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tag $tag, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $message = $translator->trans('Le tag a été modifié avec succès.');
            $this->addFlash('message', $message);

            return $this->redirectToRoute('tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/tag/{slug}", name="tag_delete", methods={"POST"})
     */
    public function delete(Request $request, Tag $tag, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        $message = $translator->trans('Le tag a été supprimé avec succès.');
        $this->addFlash('message', $message);

        return $this->redirectToRoute('tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
