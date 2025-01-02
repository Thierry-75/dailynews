<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * register user function
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param JWTService $jwt
     * @param MailService $mailer
     * @return Response
     */
    #[Route('/register', name: 'app_register',methods:['GET','POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        JWTService $jwt,
        MailService $mailer
        ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if($request->getMethod('POST')){
            $errors = $validator->validate($user);
            if(count($errors)>0 ){
                return $this->render('registration/register.html.twig',['registrationForm'=>$form->createView(),'errors'=>$errors]);
            }
            if($form->isSubmitted() && $form->isValid()){
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user,$plainPassword))
                     ->setRoles(['ROLE_USER']);
                     try{
                        $em->persist($user);
                        $em->flush();
                     }catch(EntityNotFoundException $e){
                        return $this->redirectToRoute('app_error',['exception'=>$e]);
                     }
                     $header = ['typ'=>'JWT','alg'=>'HS256'];
                     $payload = ['user_id'=>$user->getId()];
                     $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));
                     $mailer->sendMail('no-reply@blog.org',$user->getEmail(),'Activation de votre compte','register',['user'=>$user,'token'=>$token]);
                     return $this->redirectToRoute('app_main');
            }
        }
        return $this->render('registration/register.html.twig', ['registrationForm' => $form->createView()]);
    }

    /**
     * control user function
     *
     * @param [type] $token
     * @param JWTService $jwt
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/check/{token}',name:'check_user')]
    public function checkUser($token, JWTService $jwt,UserRepository $userRepository,EntityManagerInterface $em): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token,$this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);
            if($user && !$user->isVerified()){
                $user->setVerified(true);
                try{
                    $em->persist($user);
                    $em->flush();
                }catch(EntityNotFoundException $e){
                    return $this->redirectToRoute('app_error',['exception'=>$e]);
                }
                $this->addFlash('alert-success','votre compte a été activé !');
                return $this->redirectToRoute('app_main');  // PROVISOIRE ------------------------------------------------
            }
            $this->addFlash('alert-danger','jeton pas conforme !');
            return $this->redirectToRoute('app_login');
        }
    }

}
