<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.locales%>}/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index", methods={"GET"})
     *
     * @param Request            $request            HTTP request
     * @param CategoryRepository $categoryRepository Category repository
     * @param PaginatorInterface $paginator          Paginator
     *
     * @return Response HTTP response
     */
    public function index(Request $request, CategoryRepository $categoryRepository, PaginatorInterface $paginator): Response
    {
        /** @var Category $categories */
        $categories = $categoryRepository->findAll();

        $pagination = $paginator->paginate(
            $categories,
            $request->query->getInt('page', 1),
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('category/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/create", name="category_create", methods={"GET","POST"})
     *
     * @param Request            $request            HTTP request
     * @param CategoryRepository $categoryRepository Category repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category);
            $this->addFlash('success', 'global.message.category_created.success');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods={"GET"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","PUT"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Request            $request            HTTP request
     * @param Category           $category           Category entity
     * @param CategoryRepository $categoryRepository Category repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category);
            $this->addFlash('success', 'global.message.category_updated.success');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="category_delete", methods={"GET", "DELETE"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Request            $request            HTTP request
     * @param Category           $category           Category entity
     * @param CategoryRepository $categoryRepository Category repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->delete($category);
            $this->addFlash('success', 'global.message.category_deleted.success');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/delete.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }
}
