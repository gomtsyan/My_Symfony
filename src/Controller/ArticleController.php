<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\FileUploader;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     * @Security("user")
     * @param ArticleRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     */
    public function index(ArticleRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $repository->getWithQueryBuilder($this->getUser()->getId());
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     *
     * @Route("/new", name="article_new", methods={"GET","POST"})
     * @Security("user")
     * @param Request $request
     * @param FileUploader $fileUploader
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleData = $form->getData();
            $articleData->setUser($this->getUser());
            $articleImageFile = $form->get('imageFile')->getData();
            if ($articleImageFile) {
                $imageUploadResult = $fileUploader->upload($articleImageFile);
                if (is_array($imageUploadResult)) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }

                $articleData->setImage($imageUploadResult);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($articleData);
            $entityManager->flush();
            $this->addFlash('success', 'Article was created!');
            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @Security("user")
     * @param Article $article
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @Security("user")
     * @param Request $request
     * @param Article $article
     * @param FileUploader $fileUploader
     */
    public function edit(Request $request, Article $article, FileUploader $fileUploader): Response
    {
        if (!$article) {
            throw $this->createNotFoundException(
                'Invalid Data'
            );
        }

        $oldImage = $article->getImage();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleData = $form->getData();
            $articleImageFile = $form->get('imageFile')->getData();

            if ($articleImageFile) {
                $imageUploadResult = $fileUploader->upload($articleImageFile);
                if (is_array($imageUploadResult)) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }
                if ($oldImage) {
                    $fileUploader->delete($oldImage);
                }

                $articleData->setImage($imageUploadResult);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($articleData);
            $entityManager->flush();

            $this->addFlash('success', 'Article was Edited!');
            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"POST"})
     * @Security("user")
     * @param Request $request
     * @param Article $article
     * @param FileUploader $fileUploader
     */
    public function delete(Request $request, Article $article, FileUploader $fileUploader): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $imageToDelete = $article->getImage();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            if ($imageToDelete) {
                $fileUploader->delete($imageToDelete);
            }
        }

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }
}
