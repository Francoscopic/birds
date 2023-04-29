<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\BigSur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/u/user/{uid}/', name: 'app_user')]
    // public function show1(ManagerRegistry $doctrine, string $uid): Response
    public function show1(Connection $connection, string $uid): Response
    {
        // $stmt = $connection->fetchAssociative('SELECT uid, pid FROM big_sur WHERE access = 1 ORDER BY id DESC LIMIT 5', [], []);
        // $stmt = $connection->insert('big_sur', ['uid'=>'theUseridentity2', 'pid'=>'thepostid2', 'access'=>1]);
        // $values = '';
        // foreach($connection->iterateAssociativeIndexed('SELECT uid, pid FROM big_sur WHERE access = 1 ORDER BY id DESC LIMIT 15', [], []) as $id => $data)
        // {
        //     $values .= $data['pid']. ' - ';
        // }
        // return new Response('Result of query: '.$values);


        $stmt = $connection->fetchAssociative(
            'SELECT title, parags, cover, state, date FROM big_sur_list WHERE pid = :pid', ['pid'=>'a9wuAXy51654521835tmS0SEm9h'], []
        );
        if($stmt == true) {
            $title            = $stmt['title'];
            $paragraphs       = $stmt['parags'];
            $cover            = $stmt['cover'];
            $article_or_image = $stmt['state'];
            $date             = $stmt['date'];
        }
        return new Response('Result of query: '.$title);

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
