<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\BigSur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/u/user/{uid}/', name: 'app_user')]
    public function show(ManagerRegistry $doctrine, string $uid): Response
    {
        $user = $doctrine->getRepository(BigSur::class)->find($uid);

        if(!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$uid
            );
        }

        return new Response('Check out this great user: '.$user->getPid());
    }

    public function createUser(ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();

        $user = new BigSur();
        $user->setUid('BlahBlah1234');
        $user->setPid('justMyPostMap');
        $user->setAccess('1');

        $errors = $validator->validate($user);
        if(count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($user);

        $entityManager->flush();
    }
}
