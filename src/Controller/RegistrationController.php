<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\CheckUser;
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
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        JWTService $jwt,
        MailService $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($request->getMethod('POST')) {
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->render('registration/register.html.twig', ['registrationForm' => $form->createView(), 'errors' => $errors]);
            }
            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword))
                    ->setRoles(['ROLE_USER']);
                try {
                    $em->persist($user);
                    $em->flush();
                } catch (EntityNotFoundException $e) {
                    return $this->redirectToRoute('app_error', ['exception' => $e]);
                }
                // generation jeton
                $header = ['typ' => 'JWT', 'alg' => 'HS256'];
                $payload = ['user_id' => $user->getId()];
                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
                //envoi mail
                $mailer->sendMail('no-reply@e-commerce.com', $user->getEmail(), 'Activation de votre compte', 'register', ['user' => $user, 'token' => $token]);
                $this->addFlash('alert-success', 'confirmer votre adresse courriel');
                return $this->redirectToRoute('app_main');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView()
            ]);
        }
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
    #[Route('/check/{token}', name: 'check_user',methods :['GET'])]
    public function checkUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        if($jwt->isValid($token)  && !$jwt->isExpired($token)  && $jwt->check($token,$this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);
            if($user  && !$user->IsVerified()){
                $user->setVerified(true);
                try{
                    $em->persist($user);
                    $em->flush();
                }catch(EntityNotFoundException $e){
                    return $this->redirectToRoute('app_error', ['exception'=> $e]);
                }
                $this->addFlash('alert-success','Votre compte a été activé !');
                return $this->redirectToRoute('app_main'); 
            }
            $this->addFlash('alert-danger','Token invalide !');
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * renvoi activation function
     *
     * @param JWTService $jwt
     * @param MailService $mail
     * @param CheckUser $check_email
     * @return Response
     */
    #[Route('/resendverif',name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, MailService $mail,CheckUser $checkEmail): Response
    {
        if($this->denyAccessUnlessGranted('ROLE_USER')){
            $this->addFlash('alert-danger','Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }
        if(!$checkEmail->confirmEmail($this->getUser()))
        {
            $this->addFlash('alert-warning','Ce compte est déjà activé !');
            return $this->redirectToRoute('app_main'); // vers profil
        }
        // generate jeton
        $header = [ 'typ' => 'JWT', 'alg' => 'HS256'];
        $payload = ['user_id' => $this->getUser()->getId()];
        $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));
        // envoi mail
        $mail->sendMail('no-reply@e-commerce.com',$this->getUser()->getEmail(),'Activation de votre compte','register',['user'=>$this->getUser(),'token'=>$token]);
        $this->addFlash('alert-success','Email de vérification envoyé !');
        return $this->redirectToRoute('app_main');
    }

    #[Route('register/update/{id}', name: 'app_register_update', methods: ['GET', 'POST'])]
    public function updateUser(): Response
    {
        if ($this->denyAccessUnlessGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_main');
        }
        return $this->render('registration/update.html.twig');
    }

    #[Route('register/delete/{id}', name: 'app_register_delete', methods: ['GET', 'POST'])]
    public function deleteUser(): Response
    {
        if ($this->denyAccessUnlessGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_main');
        }
        return $this->render('registration/delete.html.twig');
    }
}
