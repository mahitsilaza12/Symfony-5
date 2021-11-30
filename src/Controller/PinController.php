<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Doctrine\Common\Cache\Psr6\set;

class PinController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em= $em;
    }

    /**
     * @Route("/", name="app_home",  methods="GET")
     */
    public function index(PinRepository $pinrepository): Response
    {
        // $pins = $pinrepository->findAll();
        $pins = $pinrepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pin/index.html.twig', compact('pins'));
    }

    /**
     *
     * @Route("/pins/create", name="app_create", methods={"GET","POST"})
     */
    public function create(Request $request, UserRepository $useRepo): Response
    {
        if(!$this->getUser()){
            $this->addFlash('danger','besoin de login');
            return $this->redirectToRoute('app_login');
        }
        if(!$this->getUser()->isVerified()){
            $this->addFlash('danger','besoin de verification email');
            return $this->redirectToRoute('app_home');
        }
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $oskar = $useRepo->findOneBy(['email'=>'oskar@gmail.com']);
            $pin->setUser($oskar);
            $this->em ->persist($pin);
            $this->em ->flush();
            $this->addFlash('info','ajouter avec success');
            return $this->redirectToRoute('app_home');

        }
        return $this->render('pin/create.html.twig', ['variableity' => $form->createView()]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        return $this->render('pin/show.html.twig', compact('pin'));
    }
    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Pin $pin): Response
    {
        if(!$this->getUser()){
            $this->addFlash('danger','besoin de login');
            return $this->redirectToRoute('app_login');
        }
        if(!$this->getUser()->isVerified()){
            $this->addFlash('danger','besoin de verification email');
            return $this->redirectToRoute('app_home');
        }
        if($this->getUser()!= $this->getUser()){
            $this->addFlash('danger','pas daccess');
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em ->flush();
            $this->addFlash('success','modification reussite avec success');

            return $this->redirectToRoute('app_home');

        }
        return $this->render('pin/edit.html.twig', [
            'pin'=>$pin,
            'variableity'=>$form->createView()]);
    }
    /**
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete", methods={"GET", "DELETE"})
     *
     */
//Security("is_granted('ROLE_ADMIN') and is_verified() and pin.getUser = user )"
    public function delete( Pin $pin): Response
    {
        if(!$this->getUser()){
            $this->addFlash('danger','besoin de login');
            return $this->redirectToRoute('app_login');
        }
        if(!$this->getUser()->isVerified()){
            $this->addFlash('danger','besoin de verification email');
            return $this->redirectToRoute('app_home');
        }
        if($this->getUser()!= $this->getUser()){
            $this->addFlash('danger','pas daccess');
            return $this->redirectToRoute('app_home');
        }
        $this->em->remove($pin);
        $this->em->flush();
        $this->addFlash('danger','suppression avec success');

        return $this->redirectToRoute('app_home');


    }

}
