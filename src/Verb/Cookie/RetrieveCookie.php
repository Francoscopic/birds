<?php

namespace App\Verb\Cookie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class RetrieveCookie extends AbstractController
{
    protected $user_id;
    public function get_netintui_user_id(): array
    {
        $uid = $this->cookie_user_id();
        return array(
            'message' => 'Data sent',
            'user_id' => $uid,
        );
    }

    protected function cookie_user_id(): string {

        $request = Request::createFromGlobals();

        $cookie_user = $request?->cookies?->get('cookie_user');
        $cookie_vst  = $request?->cookies?->get('vst');

        return ($cookie_user == null) ? $cookie_vst : $cookie_user;
    }
}