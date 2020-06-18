<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

// Controller pour les fonctions admin

class AdminController extends AbstractController
{

    /**
     * @var AnnonceRepository
     */

    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(AnnonceRepository $repository, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @Route("/admin", name="admin_list")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */

    // fonction pour la page d'accueil admin, ou j'affiche toutes les annnonces en les transmettant
    public function index()
    {
        // on recupere toutes les annonces de la bdd
        $annonces = $this->repository->findAll();

        // on retourne la page souhaitée avec toutes les annonces
        return $this->render('admin/list.html.twig', ['annonces'=>$annonces]);
    }

    /**
     * @Route("/admin/{id}/edit", name="admin_edit")
     * @param Annonce $annonce
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    // fonction qui permet d'aller chercher les informations d'une annonce existante dans la base de données,
    // et de remplir un formulaire d'édition avec les données déjà en ligne pour pouvoir les modifier
    public function edit(Annonce $annonce, Request $request)
    {
        // on créé un formulaire qui correspond à l'entité Annonce, avec les données de l'annonce que l'on souhaite modifié pré-remplis dans les champs
        $form = $this->createForm(AnnonceType::class, $annonce);
        // on recupere les données du POST, traite et valide
        $form->handleRequest($request );

        // ensuite on vérifié si le formulaire à été soumis et s'il est valide
        if($form->isSubmitted() && $form->isValid())
        {
            // on envoi les données et les modifications dans la base de données, on met à jour la BDD
            $this->em->flush();
            // on créé un message traduisible pour annoncer que la modification a été faite
            $message = $this->translator->trans('Item edited succesfully');
            // on créé une alerte qui permet de voir clairement que la modification a été faite
            $this->addFlash('success', $message);

            return $this->redirectToRoute('admin_list');
        }
        return $this->render('admin/edit.html.twig', ["annonce"=>$annonce, "form" => $form->createView()]);
    }

    /**
     * @Route("annonce/create", name="app_annonce_create", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */

    // fonction qui permet de génerer un formulaire qui correspond aux données nécessaires à la création d'une annonce
    // et si les données sont bonnes et le formuaire remplis, d'envoyer les données dans la BDD
    public function create(Request $request): Response
    {
        // on créé une nouvelle annonce en se basant sur l'entité Annonce
        $annonce = new Annonce();
        // on créé un formulaire qui correspond aux données nécessaires à la création d'une Annonce
        $form = $this->createForm(AnnonceType::class, $annonce);
        // on recupere les données du POST, traite et valide
        $form->handleRequest($request);

        // ensuite on vérifié si le formulaire à été soumis et s'il est valide
        if($form->isSubmitted() && $form->isValid())
        {
            // on persiste les données de l'annonce
            $this->em->persist($annonce);
            $this->em->flush();

            // on créé un message traduisible pour annoncer que la modification a été faite
            $message = $this->translator->trans('Item created succesfully');
            // on créé une alerte qui permet de voir clairement que la modification a été faite
            $this->addFlash('success', $message);

            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/create.html.twig', ["formCreate" => $form->createView()]);
    }

    /**
     * @Route("/admin/{id}/delete", name="admin_delete")
     * @param Annonce $annonce
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    // function qui permet de supprimer une annonce de la BDD
    public function delete(Annonce $annonce, Request $request)
    {
        // on supprime l'annonce dans la bdd
        $this->em->remove($annonce);
        $this->em->flush();
        // on créé un message traduisible pour annoncer que la modification a été faite
        $message = $this->translator->trans('Item deleted succesfully');
        // on créé une alerte qui permet de voir clairement que la modification a été faite
        $this->addFlash('success', $message);

        return $this->redirectToRoute('admin_list');
    }
}

