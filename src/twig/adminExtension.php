<?php

namespace App\twig;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;


class adminExtension extends AbstractExtension
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @var CommentRepository
     */
    private $commentRepository;


    /**
     * @var Environment
     */
    private $twig;


    public function __construct(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        TagRepository $tagRepository,
        CommentRepository $commentRepository,
        Environment $twig
    )
    {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
        $this->tagRepository = $tagRepository;
        $this->commentRepository = $commentRepository;
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin', [$this, 'getAdmin'], ['is_safe' => ['html']])
        ];
    }

    public function getAdmin(): string
    {
        $articles = $this->articleRepository->popularArticles();
        $articlesAll = $this->articleRepository->findAll();
        $lastArticles = $this->articleRepository->lastArticles();
        $categories = $this->categoryRepository->sidebarCategories();
        $categoriesAll = $this->categoryRepository->sidebarCategoriesAll();
        $users = $this->userRepository->findAll();
        $views = $this->articleRepository->totalViews();
        $tags = $this->tagRepository->findAll();
        $commentsAll = $this->commentRepository->findAll();
        $lastComments = $this->commentRepository->lastComments();


        try {
            return $this->twig->render('admin/stats.html.twig',
                compact('articles', 'articlesAll', 'lastArticles', 'categories', 'categoriesAll', 'users', 'views', 'tags', 'commentsAll', 'lastComments'));
        } 
        catch (LoaderError | RuntimeError | SyntaxError $e) {
        }
    }
}