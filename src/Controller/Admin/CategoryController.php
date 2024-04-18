<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/admin/category", name:'admin.category.')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController{

    #[Route(name: 'index')]
    public function index(CategoryRepository $repository)
    {
        $categories = $repository->findAll();
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form->isValid()){
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setUpdtaedAt(new \DateTimeImmutable());
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été créée.');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/create.html.twig', [
            "form" => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', requirements:['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em)
    {
        $form = $this -> createForm(CategoryType::class, $category);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form->isValid()){
            $category->setUpdtaedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée.');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/edit.html.twig', [
            "category" => $category,
            "form" => $form
        ]);
    }

    #[Route('/{id}', name: 'remove', requirements:['id' => Requirement::DIGITS],methods: ['DELETE'])]
    public function remove(Category $category, EntityManagerInterface $em)
    {
        try{
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été supprimée.');
            return $this->redirectToRoute('admin.category.index');
        }catch(Exception $e)
        {
            $this->addFlash('danger', 'La catégorie ne peut pas être supprimée car elle est associé à au moins une recette.');
            return $this->redirectToRoute('admin.category.index');
        }

    }
}