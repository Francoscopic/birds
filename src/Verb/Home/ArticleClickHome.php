<?php

namespace App\Verb\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;


class ArticleClickHome extends AbstractController
{
    public function Article_Click(): JsonResponse
    {
        if( isset( $_POST['views'], $_POST['note_id'], $_POST['viewer_id'] ) ) {

            $get_cookie = new RetrieveCookie();

            $note_id = $_POST['note_id'];
            $viewer_id = $get_cookie->get_netintui_user_id()['user_id'];
            $sent = ($this->save_views($note_id, $viewer_id) == true) ? 'Sent' : '[NTS]Unfinished';

            return $this->json([
                'message' => $sent,
            ]);
        }
        return $this->json([
            'message' => '[NTS]Error encountered',
        ]);
    }

    protected function save_views($note_id, $viewer_id)
    {
        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare('INSERT INTO views (access, pid, uid) VALUES(1, ?, ?)');
        $stmt->bind_param('ss', $note_id, $viewer_id);
        // kill
        return $stmt->execute();
    }
}