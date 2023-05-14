<?php

namespace App\Verb\Home;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class UniverseHome extends AbstractController
{

    private $menu_canvas; // Menu (array)
    private $conn;

    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    public function menu(): JsonResponse
    {
        $get_cookie = new RetrieveCookie();
        $uid = $get_cookie->get_netintui_user_id()['user_id'];

        $this->menu_canvas = array(
            'menu' => array(),
        );
        $this->get_content_menu($uid);

        return $this->json([
            'message' => 'Data sent',
            'content' => $this->menu_canvas,
        ]);
    }

    protected function get_content_menu($uid)
    {
        $details = IndexFunction::user_profile_state($this->conn, $uid);

        $name     = $details['name'];
        $username = $details['username'];
        $display  = $details['display'];
        $dis_small = $details['display_small'];

        $theme_state   = IndexFunction::get_user_state($this->conn, $uid)['state'];
        $theme_checked = ($theme_state == 1) ? 'checked' : ''; // checked is LIGHT(1), other is DARK(0)
        $get_theme     = IndexFunction::light_mode_response($theme_state);
        $theme_icon    = $get_theme['icon'];
        $theme_text    = $get_theme['text'];

        $this->menu_canvas['menu'] = [
            'name'        => $name,
            'username'    => $username,
            'display'     => $display,
            'display_small' => $dis_small,

            'theme_state' => $theme_state,
            'theme_check' => $theme_checked,
            'theme_icon'  => $theme_icon,
            'theme_text'  => $theme_text,
        ];
    }
}