<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param ArticleRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     */
    public function index(ArticleRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $repository->getQueryBuilder();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('index/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("post/{id}", name="post_show", methods={"GET"})
     * @param Article $article
     */
    public function show(Article $article): Response
    {
        return $this->render('index/show.html.twig', [
            'article' => $article,
        ]);
    }
}
