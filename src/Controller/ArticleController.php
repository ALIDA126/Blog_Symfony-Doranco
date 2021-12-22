<?php

namespace App\Controller;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\EditArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;


class ArticleController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager){
           $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/article", name="create_article")
     * @param Request $request
     * @return Response
     */
    public function createArticle(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        
        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            $article->setUser($this->getUser());
            

            # Association de l'article au user : setOwner()
            //

            # Association de l'article à la category : setOwner()
            //

            $article->setCreatedAt(new \DateTime());

            # Coder ici la logique pour uploader la photo

             // On recupere le fichier du formulaire grace a getData(). Cela nous retoune un objet de type uploaded file
              //vous trouverez tous les MimeType existant sur internet(moxilla developper)
            
            $file = $form->get('picture')->getData();
            
            //generer une contrainte d'upload, on declare un array avec deux valeurs de type string qui sont les
            //MimeType autorisés (MimeType c'est le type du fichier)
            if($file) {

                //$allowedMimeType = ['image/jpeg', 'image/png'];

                //La function native in_array() permet de comparer deux valeurs (2 arguments attendu)
               // if(in_array($file->getMimeType(), $allowedMimeType)) { // $in_array Vien verifier si un valeur existe dans le table


              //nous allons construire le nouveau nom du fichier:

              //on stock dans une variable $originalFilename le nom du fichier
               //on utilise encore une fonction native pathinfo()
              $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
             
             
              //Recuperation de l'extension pour pouvoir reconstruire le nom quelques lignes apres
              //on utilise la concartenation pour ajouter un point '.'
               $extension = '.' . $file->guessExtension();

               //Assainissement du nom au slugger fournier par symfony pour la construction du nouveau nom
              // $safeFilename = $slugger->slug($article->getTitle());
               $safeFilename = $slugger->slug($originalFilename);

               #construction du  nouveau nom

               //unigid est une function native qui permet de generer un id unique 

              $newFilename = $safeFilename . '_' . uniqid() . $extension;


               /**
                *on utilise un try catch()lorsqu'on appelle une methode qui lance une erreur
                */
              try {

                //ON appele la methode move() de UploadedFile pour pouvoir deplacer le fichier
                //dans son dossier de destination
                // Le dossier de destination a ete parametré dans services yaml

                // ATTENTION : La methode move() lance une erreur de type FileException
                //On attrappe cette erreur dans le catch(FileException $exception)
                  
                
                $file->move($this->getParameter('upload_dir'), $newFilename);


                // On set la nouvelle valeur (nom du fichier) le propriete picture de notre objet Article

                  $article->setPicture($newFilename);

              } catch (FileException $exception) {
                  //code a executer si une erreur est attrapée')

              }
       //     }

      //      else { // SI CE N'EST PAS LE BON fichier uploader
      //          $this->addFlash('warning', 'Les types de fichier autorisés sont : .jpeg / .png');
      //          return $this->redirectToRoute('create_article');
      //      }
                
            }





            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('success','Article ajouter!');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/form_article.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/modifier/article/{id}", name="edit_article")
     * @param Article $article
     * @param Request $request
     * @return Response
     */
    public function editArticle(Article $article, Request $request): Response
    {
        # Supprimer le edit form et utiliser ArticleType (configurer les options) : pas besoin de dupliquer un form
        $form = $this->createForm(EditArticleType::class, $article)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            # Créer une nouvelle propriété dans l'entité : setUpdatedAt()

            $this->entityManager->persist($article);
            $this->entityManager->flush();

        }

        return $this->render('article/edit_article.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/voir/article/{id}", name="show_article")
     * @param Article $singleArticle
     * @return Response
     */
    public function showArticle(Article $singleArticle): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->find($singleArticle->getId());

        return $this->render('article/show_article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/admin/supprimer/article/{id}", name="delete_article")
     * @param Article $article
     * @return Response
     */
    public function deleteArticle(Article $article): Response
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        $this->addFlash('success','Article supprimé !');

        return $this->redirectToRoute('dashboard');
    }
}
