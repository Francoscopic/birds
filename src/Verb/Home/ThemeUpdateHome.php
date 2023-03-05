<?php

namespace App\Verb\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ThemeUpdateHome extends AbstractController
{
    private $request;
    private $uid;
    private $connection;

    public function __construct()
    {
        // get user_id
        $get_cookie = new RetrieveCookie();
        $this->uid = $get_cookie->get_netintui_user_id()['user_id'];

        // get request
        $this->request = Request::createFromGlobals();

        // connection
        $this->connection = new DatabaseAccess();
        $this->connection = $this->connection->connect('');
    }

    public function update(): JsonResponse
    {
        if($this->request->request->has('state')) {
            $this->update_theme($this->uid);
        }

        return $this->json([
            'message' => 'Data sent',
            'content' => '',
        ]);
    }

    protected function update_theme($uid)
    {
        $the_state = $this->request->request->get('state');
        $the_uid   = $this->uid;
        $state     = null;

        $stmt = $this->connection->prepare("UPDATE user_sapphire SET state = ? WHERE uid = ?");
        $stmt->bind_param('ss', $state, $the_uid);

        // validate
        ($the_state == 'dark') ? $state = 1 : $state = 0;
        $stmt->execute();

        unset($the_state, $the_uid, $state);
    }
}