<?php

require "../includes/init.php";
$page->setTitle("Account")->setPage(3)->setPath("../")->header()->requireLogin();

?>

<section class="title">
    <div class="container">
        <h1><?php pt("Account"); ?></h1>
    </div>
</section>

<section class="account">
    <div class="container">
        <div class="advertising vertical">
            <div class="content">
                <div class="row">
                    <div class="col-md-4">
                        <a href="websites.php">
                            <div class="option">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-world" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9" />
                                    <line x1="3.6" y1="9" x2="20.4" y2="9" />
                                    <line x1="3.6" y1="15" x2="20.4" y2="15" />
                                    <path d="M11.5 3a17 17 0 0 0 0 18" />
                                    <path d="M12.5 3a17 17 0 0 1 0 18" />
                                </svg>

                                <h3><?php pt("Manage sites"); ?></h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="settings.php">
                            <div class="option">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>

                                <h3><?php pt("Edit account"); ?></h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="signout.php">
                            <div class="option">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                    <path d="M7 12h14l-3 -3m0 6l3 -3" />
                                </svg>

                                <h3><?php pt("Sign out"); ?></h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="slots">
                <?php
                    if (($snippet = Ads::commit('728x90')) !== false) {
                        echo $snippet;
                    }
                    else if (($snippet = Ads::commit('468x60')) !== false) {
                        echo $snippet;
                    }
                ?>
            </div>
        </div>
    </div>
</section>

<?php $studio->getPluginManager()->call("account_bottom"); ?>

<?php
$page->footer();
?>
