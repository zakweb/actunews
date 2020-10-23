<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Action = 1 page = Page Accueil
     */
    public function index()
    {
        return $this->render('default/index.html.twig' );
    }

    /**
     * Action = 1 page = Page Contact
     */
    public function contact()
    {
        return $this->render('default/contact.html.twig');
    }

    /**
     * Action = 1 page = Page Categorie
     * @Route("/politique")
     */
    //public function category()
    //{
    //  return new Response('<h1>Page Categorie</h1>');

    //}
    /**
     * Action = 1 page = Page Categorie
     * @Route("/{alias}",name="default_category",methods={"GET"})
     * *{} est un parametre
     * *name = Controller_nom de la page
     * *methods = requête HTTP à inserer( GET, POST, HEAD, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH) methode autorisation pour chaque route
     */
    public function category($alias)
    {
    return $this->render('default/category.html.twig');
    //Double cote pour etre lu comme une string

    }

/**
 * Action = 1 page = Page Article
 * @Route("/{category}/{alias}_{id}.html", name="default_article", methods={"GET"})
 */
public function article()
{
    # https://localhost:8000/politique/couvre-feu-quand-la-situation-sanitaire-s-ameliorera-t-elle_14155614.html


    return $this->render('default/article.html.twig');
}
}