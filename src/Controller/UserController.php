<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="user_index", methods={"GET"})
     * @param \App\Repository\UserRepository $userRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM App:User p ORDER BY p.id DESC";
        $donnees = $em->createQuery($dql);

        $users = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/new", name="user_new", methods={"GET","POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Upload user avatar (image)
            $uploadedFile = $form['avatar']->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter("users_images_directory");

                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $user->setAvatar($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = $translator->trans('Nouvel utilisateur créé avec succès.');
            $this->addFlash('message', $message);

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_show", methods={"GET"})
     * @param \App\Entity\User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\User $user
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, User $user, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Upload image
            $uploadedFile = $form['avatar']->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter("users_images_directory");

                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $user->setAvatar($newFilename);
            }

            $this->getDoctrine()->getManager()->flush();

            $message = $translator->trans('Utilisateur modifié avec succès.');
            $this->addFlash('message', $message);

            return $this->redirectToRoute('user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/delavatar", name="user_delete_avatar", methods={"GET"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\User $user
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAvatar(Request $request, User $user, TranslatorInterface $translator): Response
    {
        // Delete user's avatar in folder
        $avatar = $user->getAvatar();
        if($avatar) {
            $nomAvatar = $this->getParameter("users_images_directory") . '/' . $avatar;
            if(file_exists($nomAvatar)) {
                unlink($nomAvatar);
            }
        }

        // Set avatar to "nothing" in DB
        $user->setAvatar('');
        $this->getDoctrine()->getManager()->flush();

        // Redirect to user edit page with translated message
        $message = $translator->trans('L\'avatar du profil a été supprimé avec succès.');
        $this->addFlash('message', $message);

        return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
    }

    /**
     * @Route("/user/{id}", name="user_delete", methods={"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\User $user
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, User $user, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $message = $translator->trans('Le compte a été supprimé avec succès.');
        $this->addFlash('message', $message);

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
