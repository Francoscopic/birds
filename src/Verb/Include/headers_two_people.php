<?php

// Tailor the right image to the right state: Dark or Light
$note_picture = ($state === 'darkmode' ? '..\icons\notes\logo-2-dark.png' : '..\icons\notes\logo-2-white.png');

// Make it easy to know the current state
function know_mode() {

    global $state;
    $for_dark = ($state === 'darkmode' ? 'bcg-e' : '');
    $for_light = ($state === 'darkmode' ? '' : 'bcg-e');
    return array($for_dark, $for_light);

    unset($for_dark, $for_light, $state);
}
$for_dark = know_mode()[0];
$for_light = know_mode()[1];
// Make it easy to know the current state - END


$my_note_menu = '
<div id="note-menu" class="note-menu hd">
    <a href="#" class="note-menu-close a">
        <div class="note-menu-bck glsmpsm men-cvr"></div>
    </a>
    <div class="note-menu-table abs-center abs-center-fixed ft-sect">

        <div class="note-menu-element fwl">
            <a href="write.php" class="a">
                <button class="note-menu-element-button rad10">
                    <span class="note-menu-element-icon"><i class="fa fa-pen"></i></span>
                    <br><br>
                    <span class="sm-i note-menu-element-about trn3-color">Write</span>
                </button>
            </a>
        </div>

        <div class="note-menu-element fwl">
            <a href="profiles.php" class="a">
                <button class="note-menu-element-button rad50">
                    <span class="note-menu-element-icon"><img class="sm-i note-menu-element-img rad50" src="'.$my_display_shrink.'" /></span>
                    <br>
                    <span class="sm-i note-menu-element-about trn3-color">Profile</span>
                </button>
            </a>
        </div>

        <div class="note-menu-element fwl">
            <a href="change.php" class="a">
                <button class="note-menu-element-button rad10">
                    <span class="note-menu-element-icon"><i class="fa fa-cog"></i></span>
                    <br><br>
                    <span class="sm-i note-menu-element-about trn3-color">Change</span>
                </button>
            </a>
        </div>

        <div class="note-menu-element fwl">
            <a href="../out/aquamarine/us/faq.php" class="a">
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
            <a href="signout.php" class="a">
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

$my_header_aux = '
<div class="note-header-auxiliary relative desktop mobile clear-fix" id="note-header-auxiliary">

    <div class="header-auxiliary-me ft-sect">
        <p>
            <span class="header-aux-name" id="header-aux-name">'.$my_name.'</span>
            <br />
            <span class="header-aux-username" id="header-aux-username"><a class="header-aux-username-a a" href="profiles.php">'.'@'.$my_username.'</a></span>
            <span class="header-aux-quick"><a class="header-aux-a a" href="change.php"><button class="header-aux-quick-button trn3 rad2 no-bod"><i class="sm-i fa fa-cog"></i> change</button></a></span>
        </p>
    </div>

    <div class="header-auxiliary-objects ft-sect">
        <p class="header-aux-objects-p header-new-write rad2 fwl" id="header-aux-objects-p">
            <a class="header-aux-a a" href="profiles.php">
                <span class="header-aux-right-quick">
                    <img class="header-aux-right-img sm-i rad50" src="'.$my_display_shrink.'" alt="<?php echo $name ?>" />
                </span>
            </a>
        </p>
    </div>
</div>';


$footer = '
<div class="footer-container">
    <div class="footer-elements desktop mobile">
        <ul class="footer-elements-ul ul">
            <!--<li><a href="#" class="a open-sans trn3">ABOUT US</a></li>-->
            <!--<li><a href="#" class="a open-sans trn3">TERMS</a></li>-->
            <li><a href="../aquamarine/us/faq.php" class="a open-sans trn3">FAQ</a></li>
            <!--<li><a href="#" class="a open-sans trn3">OPPORTUNITY</a></li>-->
        </ul>
        <ul class="footer-elements-ul ul">
            <li class="footer-copy open-sans"><i class="fa fa-copyright"></i> 2020. Netintui.com</li>
        </ul>
    </div>
</div>';

function empty_feed($path, $page) {
    echo '
    <div class="empty-page ft-sect center">
        <p>Nothing here.</p>
        <p>Whoops!</p>
        <a class="rad2 a" href="'.$path.'">'.$page.'</a>
    </div>';
    unset($path, $page);
}

function page_name($current, $path='') {
    echo '
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
    </div>';
    unset($current, $path);
}




?>