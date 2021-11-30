<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account', methods:"GET")]
    public function show(): Response
    {
        if(!$this->getUser()){
            $this->addFlash('danger','besoin de login');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('account/show.html.twig');
    }

    /**
     *@Route("/account/edit", name= "app_account_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if(!$this->getUser()){
            $this->addFlash('danger','besoin de login');
            return $this->redirectToRoute('app_login');
        }
        $form= $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success','Update profil success');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     *@Route("/account/change", name= "app_account_chanegemdp", methods={"GET","POST"})
     */
    public function change(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if(!$this->getUser()){
            $this->addFlash('danger','besoin de login');
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $form= $this->createForm( ChangePasswordFormType::class,null,
            ['current_password_is_required'=>true]
        );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
//          dd($form->getData());
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form['plainPassword']->getData())
            );
            $em->flush();
            $this->addFlash('success','password updated success');
            return $this->redirectToRoute('app_account');

        }
        return $this->render('account/changemdp.html.twig',[
            'form'=>$form->createView()
        ]);

    }
}
