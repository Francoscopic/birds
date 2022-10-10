<?php


$menu = "";

// Make it easy to know the current state


function quick_notif() {
    
}

function footer($page) {
    function foot_specs($pg) {
        $path = '';
        switch ($pg) {
            case 'home':
                $path = 'pages/out/';
                break;
            case 'pages':
                $path = '../out/';
                break;
            default: break;
        }  
        return array($path);
    }
    $path_spec = foot_specs($page);
    $path = $path_spec[0];

    echo'<div class="footer-container desktop mobile glsmpsm">
        <div class="footer-elements">
            <ul class="footer-elements-ul ul">
                <li><a href="'.$path.'aquamarine/us/faq.php" class="a ft-menu trn3">FAQ</a></li>
            </ul>
            <ul class="footer-elements-ul ul">
                <li class="footer-copy ft-sect"><i class="fa fa-copyright"></i> 2021. Netintui.com</li>
            </ul>
        </div>
    </div>';
    echo'<div class="footer-aux"></div>';
    unset($path, $path_spec, $path);
}

function empty_feed($path, $page) {
    echo '
    <div class="empty-page ft-sect center">
        <p>Nothing here.</p>
        <p>Whoops!</p>
        <a class="rad2 a" href="'.$path.'">'.$page.'</a>
    </div>';
    unset($path, $page);
}

function note_header($page, $page_name, $display) {
    function header_spec($pg) {
        $path_home = $path_img = $path_pages = '';
        switch ($pg) {
            case 'home':
                $path_pages = 'pages/in/';
                break;
            case 'pages':
                $path_pages = '';
                $path_img = $path_home = '../../';
                break;
            default: break;
        }
        return array($path_home, $path_img, $path_pages);
    }
    function have_disp($disp) {
        $disp_name = explode('.', $disp)[0];
        if($disp_name == 'display') {
            return 'people/users/'.$disp;
        }
        return 'people/community/profiles/shk_'.$disp;
    }
    $path_spec = header_spec($page);
    $path_home = $path_spec[0];
    $path_img  = $path_spec[1];
    $path_pages = $path_spec[2];
    
    # Define parameters:
    $display = have_disp($display);
    echo '<div class="n-header desktop mobile glsmpsm" id="n-header">
        <div class="n-header-one">
            <a href="'.$path_home.'index.php" class="a">
                <button class="n-header-write"><h1 class="n-header-one-h ft-menu">Notes</h1>
                <span><p class="n-header-one-p">'.ucwords($page_name).'</p></span></button>
            </a>
        </div>
        <div class="n-header-two">
            <a href="#" id="note-menu-open" class="a">
                <button class="n-header-write trn3-color"><i class="fa fa-bars"></i></button>
            </a>
            <a href="'.$path_pages.'profiles.php" class="a" title="Profile">
                <button class=""><img class="rad50" src="'.$path_img.$display.'" /></button>
            </a>
        </div>
    </div><div class="n-header-height"></div>';
    unset($display, $page, $page_name, $path_pages, $path_home, $path_img, $fix);
}

function note_header_reception($page, $page_name, $pid) {

    function header_specs($pg) {
        $path_home = $path_img = $path_pages = '';
        switch ($pg) {
            case 'home':
                $path_pages = 'pages/in/';
                break;
            case 'pages':
                $path_pages = '';
                $path_img = $path_home = '../../';
                break;
            default: break;
        }
        return array($path_home, $path_img, $path_pages);
    }
    $path_spec = header_specs($page);
    $path_home = $path_spec[0];
    $path_img  = $path_spec[1];
    $path_pages = $path_spec[2];

    // Get parameters
    // from send_data_to_entrance($page)
    
    # Define parameters:
    echo '
    <div class="n-header desktop mobile" id="n-header">
        <div class="n-header-one">
            <a href="'.$path_home.'index.php" class="a">
                <button class="n-header-write"><h1 class="n-header-one-h ft-menu">Notes</h1>
                <span><p class="n-header-one-p">'.ucwords($page_name).'</p></span></button>
            </a>
        </div>
        <div class="n-header-two reception-header">
            <a href="'.send_data_to_entrance('signup').'" class="a">
                <button class="reception-header-signup rad20 trn3-color">Sign up</button>
            </a>
            <a href="'.send_data_to_entrance('signin').'" class="a">
                <button class="reception-header-signin rad20 trn3-color">Sign in</button>
            </a>
        </div>
    </div><div class="n-header-height"></div>';
    unset($page, $page_name, $path_pages, $path_home, $path_img, $pid);
}
function send_data_to_entrance($page) {
    global $pid;
    $this_page_name = trim(basename($_SERVER['PHP_SELF'], '.php'));
    return '../out/aquamarine/'.$page.'.php?pg='.$this_page_name.'&pid='.$pid;
}


?>