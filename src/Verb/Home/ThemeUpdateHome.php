<?php

namespace App\Verb\Home;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ThemeUpdateHome extends AbstractController
{
    private $request;
    private $uid;
    private $conn;

    public function __construct(Connection $connection)
    {
        // get user_id
        $get_cookie = new RetrieveCookie();
        $this->uid = $get_cookie->get_netintui_user_id()['user_id'];

        // get request
        $this->request = Request::createFromGlobals();

        // connection
        $this->conn = $connection;
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
        $state     = ($the_state == 'dark') ? 1 : 0;

        // validate
        $state = ($the_state == 'dark') ? 1 : 0;
        // update
        $this->conn->update('user_sapphire', ['state'=>$state], ['uid'=>$uid]);

        unset($the_state, $state, $uid);
    }
}