<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    /**
     * Formulaire créant un article
     * @Route("/article/creer-un-article",name="post_create",methods={"GET|POST"})
     * @IsGranted("ROLE_JOURNALIST")
     */
    public function createPost(Request $request,SluggerInterface $slugger)

{
    #1.a Création d'un nouveau Post
    $post = new Post();
    #1.b Attribution d'un user
    #FIXME Temporaire
    $user=$this->getDoctrine()
        ->getRepository(User::class)
        ->find(1);
    $post->setUser($user);

    #1.c Ajout de la date de la rédaction de l'article
    $post->setCreatedAt(new \ DateTime());


    #2. Création d'un formulaire avec $post
    $form = $this->createFormBuilder($post)
        #2.a Titre de l'article
    ->add('title', TextType::class)

        #2.b Categorie de l'article(liste déroulante)
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
        ])
        #2.c Contenu de l'article
     ->add('content',textareaType::class)

        #2.d Upload Image de l'article
    ->add('featuredImage',FileType::class)

    #2.e Bouton Submit de l'article
    ->add('submit',submitType::class)

   #Recupération du formulaire généré grâce aux codes créés
    ->getForm();
    #3. Recuperation des infos du formulaire en Request
    $form->handleRequest($request);

    # 4.Verification du formulaire (si soumis et valide)
    if ($form->isSubmitted()&& $form->isValid()){
        #dump($request);
        #dd($post);
       # 4.a TODO Gestion Upload de l'image

            /** @var UploadedFile $featuredImage */
            $featuredImage = $form->get('featuredImage')->getData();


            if ($featuredImage) {
                $originalFilename = pathinfo($featuredImage->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$featuredImage->guessExtension();


                try {
                    $featuredImage->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                # Stockage de l'image dans la BDD
                $post->setFeaturedImage($newFilename);
            }
       # 4.b Génération de l'alias
        $post->setAlias($slugger->slug($post->getTitle()));
       # 4.c Sauvegarde dans la BDD
        # em = Entity Manager // Class pour sauvegarder d'autres class
        $em = $this->getDoctrine()->getManager();  # Recuperation du em
        $em ->persist($post);  # Sauvegarde du $post en BDD
        $em ->flush(); # Execution la demande

       # 4.d Notification/confirmation
        $this->addFlash('notice','Votre article est en ligne');

       # 4.e Redirection
        return $this->redirectToRoute('default_article',[
            'category'=>$post->getCategory()->getAlias(),
            'alias'=>$post->getAlias(),
            'id'=>$post->getId()
        ]);
    }
    #Transmission de formulaire à la vue
    return $this -> render('post/create.html.twig',['form'=>$form ->createView()]);

}
}