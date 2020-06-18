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

    public function index()
    {
        $annonces = $this->repository->findAll();

        return $this->render('admin/list.html.twig', ['annonces'=>$annonces]);
    }

    /**
     * @Route("/admin/{id}/edit", name="admin_edit")
     * @param Annonce $annonce
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function edit(Annonce $annonce, Request $request)
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request );

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();

            $message = $this->translator->trans('Item edited succesfully');

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

    public function create(Request $request): Response
    {

        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($annonce);
            $this->em->flush();

            $message = $this->translator->trans('Item created succesfully');

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

    public function delete(Annonce $annonce, Request $request)
    {

        $this->em->remove($annonce);
        $this->em->flush();

        $message = $this->translator->trans('Item deleted succesfully');

        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_list');

    }
}

