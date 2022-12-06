<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Feedback;
use App\Entity\Product;
use App\Form\FeedbackFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ProductRepository $productItems, CategoryRepository $categoryItems): Response
    {
        $product = $productItems->findAll();
        $category = $categoryItems->findAll();

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'product' => $product,
            'category' => $category
        ]);
    }

    /**
     * Products page by category
     *
     * @param Category $category
     * @param ProductRepository $productItems
     * @return Response
     */
    #[Route('/category/{slug}', name: 'category')]
    public function categoryList(Category $category, ProductRepository $productItems): Response
    {
        $product = $productItems->findBy(array('category_id' => $category->getCategoryId()));

        return $this->render('index/category_list.html.twig', [
            'title' => $category->getTitle(),
            'product' => $product
        ]);
    }

    /**
     * Detailed Product Page
     *
     * @param Product $product
     * @return Response
     */
    #[Route('/product/{slug}', name: 'product_detail')]
    public function productDetail(Product $product): Response
    {
        return $this->render('index/product_detail.html.twig', [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice()
        ]);
    }

    /**
     * Feedback page
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/contacts', name: 'contacts')]
    public function contacts(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackFormType::class, $feedback);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData();

           if ($feedback instanceof Feedback) {
               $entityManager->persist($feedback);
               $entityManager->flush();
           }
        }

        return $this->render('index/contacts.html.twig', [
            'controller_name' => 'contacts',
            'feedback_form' => $form->createView(),
        ]);
    }

}
