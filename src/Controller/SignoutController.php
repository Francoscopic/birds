<?php

namespace App\Controller;

// use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Database\DatabaseAccess;
use App\Function\IndexFunction;

class SignoutController extends AbstractController
{
    private $session_cell;

    public function __construct()
    {
        $this->session_cell = new Session();
    }

    #[Route('/{user_name}/signout/', name: 'note_signout')]
    public function signout(): Response
    {
        /*
        Basically, you delete all the cookies and 
        Create a fresh session key, save it to the database so the user doesn't login again.
        The security is not that air-tight but it's interesting.

        CONSEQUENCE: Users can't login on two browsers at the same time.
        When they login with another browser, they have to re-login on the "penultimate" browser, whenever they go back to it. Does it make sense?
        */

        // kill session
        $this->session_cell->clear();

        // kill cookie
        if(
            IndexFunction::set_cookie_variables('cookie_user', '', '-7 months') &&
            IndexFunction::set_cookie_variables('cookie_sesh', '', '-7 months')
        )
        {
            return $this->redirectToRoute('note_signin');
        }

        // otherwise
        return $this->redirectToRoute('note_home');
    }

    protected function change_user_session_keys($new_key, $uid): bool
    {
        // Update the database with the fresh session-key
        # Database Access
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare("UPDATE user_diamond SET seshkey = ? WHERE uid = ?");
        $stmt->bind_param("ss", $new_key, $uid);

        if( $stmt->execute() ) {
            
            return true;
        }
        return false;
    }
}
