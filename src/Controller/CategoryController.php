<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
   /**
    * @Route("/admin/creer-categorie", name="create_category", methods={"GET|POST"})
    * @param Request $request
    * @param SluggerInterface $slugger
    * @param EntityManagerInterface $entityManager
    */

    public function createCategory(Request $request, SluggerInterface $slugger, EntityManagerInterface $entitymanager)

{
     $category = new Category();

     $form = $this->createform(CategoryType::class, $category)->handleRequest($request);
     return $this->render('dashboard/form_category.html.twig', [
        'form' => $form->createView()
     ]);
} 

}







