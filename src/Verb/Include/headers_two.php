<?php

// Tailor the right image to the right state: Dark or Light
$note_picture = ($state === 'darkmode' ? '..\icons\notes\logo-2-dark.png' : '..\icons\notes\logo-2-white.png');

// Make it easy to know the current state
function know_mode() {

    global $state;
    $for_dark = ($state === 'darkmode' ? 'bcg-e' : '');
    $for_light = ($state === 'darkmode' ? '' : 'bcg-e');
    return array($for_dark, $for_light);
}
$for_dark = know_mode()[0];
$for_light = know_mode()[1];
// Make it easy to know the current state - END


function note_menu($path='', $out_path='') {

    $for_dark = know_mode()[0];
    $for_light = know_mode()[1];
    global $display_shrink;
    echo'<div id="note-menu" class="note-menu hd">
        <a href="#" class="note-menu-close a">
            <div class="note-menu-bck glsmpsm men-cvr"></div>
        </a>
        <div class="note-menu-table abs-center abs-center-fixed ft-sect">

            <div class="note-menu-element fwl">
                <a href="'.$path.'write.php" class="a">
                    <button class="note-menu-element-button rad10">
                        <span class="note-menu-element-icon"><i class="fa fa-pen"></i></span>
                        <br><br>
                        <span class="sm-i note-menu-element-about trn3-color">Write</span>
                    </button>
                </a>
            </div>

            <div class="note-menu-element fwl">
                <a href="'.$path.'profiles.php" class="a">
                    <button class="note-menu-element-button rad50">
                        <span class="note-menu-element-icon"><img class="sm-i note-menu-element-img rad50" src="'.$display_shrink.'" /></span>
                        <br>
                        <span class="sm-i note-menu-element-about trn3-color">Profile</span>
                    </button>
                </a>
            </div>

            <div class="note-menu-element fwl">
                <a href="'.$path.'change.php" class="a">
                    <button class="note-menu-element-button rad10">
                        <span class="note-menu-element-icon"><i class="fa fa-cog"></i></span>
                        <br><br>
                        <span class="sm-i note-menu-element-about trn3-color">Change</span>
                    </button>
                </a>
            </div>

            <div class="note-menu-element fwl">
                <a href="'.$out_path.'aquamarine/us/faq.php" class="a">
                    <button class="note-menu-element-button rad10">
                        <span class="note-menu-element-icon"><i class="fa fa-question"></i></span>
                        <br><br>
                        <span class="sm-i note-menu-element-about trn3-color">FAQ</span>
                    </button>
                </a>
            </div>

            <div class="note-menu-element-slide fwl">
                <input type="hidden" id="display-request-level" level="2" />
                <p class="note-menu-element-slide-about"><i class="note-e sm-i fa fa-sun"></i></p>
                <button id="note-display-dark" class="note-menu-element-slide-buttons trad2 trn3-color">
                    <span id="note-display-dark-about" class="'.$for_dark.'">Dark</span></button><!--
                --><button id="note-display-light" class="note-menu-element-slide-buttons brad2 trn3-color">
                        <span id="note-display-light-about" class="'.$for_light.'">Light</span></button>
                <br>
            </div>

            <div class="note-menu-element-exit clear-fix">
                <a href="'.$path.'signout.php" class="a">
                    <button class="note-menu-element-exit-button ft-sect rad4 fwl" title="Sign out">
                        <i class="sm-i fa fa-arrow-left trn3-color"></i>
                        <span>sign-out</span>
                    </button>
                </a>
                <a href="#" class="note-menu-close a">
                    <button class="note-menu-element-exit-button ft-sect rad4 fwr" title="Close and continue">
                        <span>close</span>
                        <i class="sm-i fa fa-arrow-right trn3-color"></i>
                    </button>
                </a>
            </div>
        </div>
    </div>';
    unset($path, $out_path, $for_dark, $for_light, $display_shrink);
}

$note_header = '
<div class="note-header-glass" id="note-header-glass">

</div>
<div class="note-header" id="note-header">

    <div class="nht-inside desktop mobile center ft-sect clear-fix">
        
        <div class="ni-one fwl">
            <a href="../../index.php" class="a">
                <button class="" title="Home">
                    <span id="ni-one-arrow" class="ni-one-arrow rad50"><i class="fa fa-arrow-left"></i></span>
                </button>
            </a>
        </div>

        <div class="note-header-search">
            <a href="../../index.php" class="a">
                <div class="note-header-search-div">
                    <img src="'.$note_picture.'" alt="Notes" class="note-header-img" />
                </div>
            </a>
        </div>

        <div class="ni-two fwl">
            <a href="#" id="note-menu-open" class="a">
                <button class="" id="note-header-dork" title="Menu">
                    <i class="sm-i fa fa-bars"></i>
                </button>
            </a>
        </div>

    </div>

</div>';

function quick_notif() {
    echo'<div id="note-quick-notification" class="note-quick-notification hd rad4">

        <div id="note-quick-header" class="profile-headers clear-fix">
            <div class="profile-headers-div fwl">
                <a class="a close-quick-notif" href="#" title="close box">
                    <button id="profile-button-left-for-quick-notif" class="profile-headers-button ft-sect no-bod"><i class="sm-i fa fa-chevron-left"></i> close</button>
                </a>
            </div>
            <div class="profile-headers-div fwr">
                <a class="a" href="#" title="FAQ">
                    <button id="profile-button-right-for-quick-notif" class="profile-headers-button no-bod"><i class="sm-i note-e fa fa-question"></i></button>
                </a>
            </div>
        </div>

        <div id="quick-notif-msg" class="quick-notif-msg ft-sect hd">
            <p id="quick-msg-p" class="sm-i quick-msg-p"></p>
        </div>

        <div id="quick-notif-request" class="quick-notif-request ft-sect hd">
            <p id="quick-req-p" class="sm-i quick-req-p"></p>
            <div class="">
                <a href="#" class="close-quick-notif"><button class="quick-req-button rad20">cancel</button></a>
                <a href="#" class=""><button id="quick-req-button-confirm" class="quick-req-button quick-req-button-confirm rad20">confirm</button></a>
            </div>
        </div>

    </div>';
}

function footer($path='') {
    echo'<div class="footer-container ft-menu">
        <div class="footer-elements desktop mobile">
            <ul class="footer-elements-ul ul">
                <li><a href="'.$path.'aquamarine/us/faq.php" class="a trn3">FAQ</a></li>
            </ul>
            <ul class="footer-elements-ul ul">
                <li class="footer-copy sm-i"><i class="fa fa-copyright"></i> 2020. Netintui.com</li>
            </ul>
        </div>
    </div>';
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

function page_nav($current) {
    $hm = $cm = '';
    $hm_link = $cm_link = $wr_link = '';
    if($current === 'home') {
        $hm = 'pg-current';
        $cm_link = 'pages/community.php';
        $wr_link = 'pages/';
    }
    if($current === 'cmt') {
        $cm = 'pg-current';
        $hm_link = '../';
    }
    echo '
    <div class="pages-nav contain">
        <a class="a" href="'.$hm_link.'" title="Visit Home">
            <button class="pg-nav-button align-right '.$hm.' trn3-color ft-menu fwl">Home</button>
        </a>
        <a class="a" href="'.$wr_link.'write.php" title="Share something creative">
            <button class="pg-nav-button pg-nav-plus trn3-bck-color rad50 ft-sect fwl"><i class="note-e sm-i fa fa-pen"></i></button>
        </a>
        <a class="a" href="'.$cm_link.'" title="Visit Community">
            <button class="pg-nav-button align-left '.$cm.' trn3-color ft-menu fwl">People</button>
        </a>
    </div>';

    unset($hm, $cm, $hm_link, $cm_link, $wr_link);
}

function page_name($current, $path='') {
    echo '<br>
    <div id="pages-names" class="pages-names desktop mobile center contain">
        <h1 id="pages-names-h1" class="fwl pages-names-h1 ft-title">'.ucwords($current).'</h1>
        <div class="fwr">
            <a class="a" href="#" title="Notification">
                <button class="pages-names-button but-p rad20"><i class="fa fa-bell"></i></button>
            </a>
            <a class="a" href="'.$path.'write.php" title="Write something great">
                <button class="pages-names-button but-e rad20"><i class="fa fa-plus"></i></button>
            </a>
        </div>
    </div>
    <br>';
    unset($current, $path);
}




?>