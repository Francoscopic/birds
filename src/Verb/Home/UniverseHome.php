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
use App\Function\IndexFunction;


class UniverseHome extends AbstractController
{

    public function menu(): JsonResponse
    {
        $get_cookie = new RetrieveCookie();
        $uid = $get_cookie->get_netintui_user_id()['user_id'];

        $content = $this->get_content($uid)['content'];

        return $this->json([
            'message' => 'Data sent',
            'content' => $content,
        ]);
    }

    protected function get_content($uid): array {

        $details = IndexFunction::profile_user_figures($uid);

        $name    = $details['name'];
        $username = $details['username'];
        $display = IndexFunction::image_file_paths('profile')['content'] . $details['display'];
        
        $link_home    = $this->generateUrl('note_home');
        $link_profile = $this->generateUrl('note_profile', array('user_name'=>$username));
        $link_saved   = $this->generateUrl('note_saved', array('user_name'=>$username));
        $link_history = $this->generateUrl('note_history', array('user_name'=>$username));
        $link_change  = $this->generateUrl('note_change', array('user_name'=>$username));
        $link_support = $this->generateUrl('note_support');
        $link_signout = $this->generateUrl('note_signout', array('user_name'=>$username));

        $content = <<<MENU
        <nav id="menu-august-nav" class="menu-august-nav hdd">
            <div class="menu-august-cover ft-sect">
                <div class="menu-august" give-trans-bck>
                    <div class="menu-august-profile">
                        <a href="$link_profile">
                            <div prof-img>
                                <img src="$display" />
                            </div>
                            <div prof-text>
                                <h1>$name</h1>
                                <p>@$username</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="menu-august" give-botm-space>
                    <div class="menu-august-profile">
                        <div class="menu-august-pages">
                            <ul give-und>
                                <a href="$link_home">
                                    <li>Home</li>
                                </a>
                                <a href="$link_profile">
                                    <li>Profile</li>
                                </a>
                                <a href="$link_saved">
                                    <li>Saved</li>
                                </a>
                                <a href="$link_history">
                                    <li>History</li>
                                </a>
                                <a href="$link_change">
                                    <li>Settings</li>
                                </a>
                            </ul>
                            <ul give-un>
                                <a href="$link_support">
                                    <li>Help & FAQ</li>
                                </a>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="menu-august">
                    <div class="menu-august-profile">
                        <div class="menu-august-profile-mixt">
                            <label class="note-color-mode">
                                <input type="checkbox" class="hd" name="color_mode" path="{path}" mode="{theme_state}" {theme_checked} />
                                <div>
                                    <h1><i class="{theme_icon}"></i></h1>
                                    <p>{theme_text}</p>
                                </div>
                            </label>
                            <a href="$link_signout">
                                <div>
                                    <h1><i class="fa-solid fa-right-from-bracket"></i></h1>
                                    <p>Log out</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        MENU;
        
        return array(
            'message' => 'Data sent',
            'content' => $content,
        );
    }
}