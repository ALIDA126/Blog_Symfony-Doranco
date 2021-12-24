<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;


/*
    Les fixtures sont un jeux de données
    Elles servent a remplir la bdd juste apres la creation de la bdd 
    pour pourvoir manipuler des données dans mon code => des entités
 */
class CategoryFixtures extends Fixture
{

    
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {

           $categories = [
            'Politique', 
            'Société', 
            'Economie', 
            'Santé', 
            'Environement', 
            'Sport', 
            'Culture'
        ];

        foreach ($categories as $category) {
           

        $cat = new Category();
        
        $cat->setName($category);
        $cat->setAlias($this->slugger->slug($category));

        $manager->persist($cat);
    }

        $manager->flush();
    }
}
