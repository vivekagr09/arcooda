<?php
require "includes/init.php";
$page->setPath("../")->requirePermission('admin-access')->setPage(16)->setTitle("Help")->header();
?>

<div class="heading">
    <h1>Help</h1>
    <h2>Support & diagnostics</h2>
</div>

<div class="panel v2">
    <div class="menu v2">
        <ul>
            <li>
                <a href="../content/documents/documentation.html" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-text" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <line x1="9" y1="9" x2="10" y2="9" />
                        <line x1="9" y1="13" x2="15" y2="13" />
                        <line x1="9" y1="17" x2="15" y2="17" />
                    </svg>
                    <strong>Documentation</strong>
                    <p>View product documentation for usage and other general info.</p>
                </a>
            </li>
            <li>
                <a href="diagnostics.php">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bug" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 9v-1a3 3 0 0 1 6 0v1" />
                        <path d="M8 9h8a6 6 0 0 1 1 3v3a5 5 0 0 1 -10 0v-3a6 6 0 0 1 1 -3" />
                        <line x1="3" y1="13" x2="7" y2="13" />
                        <line x1="17" y1="13" x2="21" y2="13" />
                        <line x1="12" y1="20" x2="12" y2="14" />
                        <line x1="4" y1="19" x2="7.35" y2="17" />
                        <line x1="20" y1="19" x2="16.65" y2="17" />
                        <line x1="4" y1="7" x2="7.75" y2="9.4" />
                        <line x1="20" y1="7" x2="16.25" y2="9.4" />
                    </svg>
                    <strong>Diagnostics</strong>
                    <p>Run automatic tests to find issues and errors in your app.</p>
                </a>
            </li>
            <li>
                <a href="https://codecanyon.net/item/seo-studio-professional-tools-for-seo/17022701/support" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lifebuoy" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="4" />
                        <circle cx="12" cy="12" r="9" />
                        <line x1="15" y1="15" x2="18.35" y2="18.35" />
                        <line x1="9" y1="15" x2="5.65" y2="18.35" />
                        <line x1="5.65" y1="5.65" x2="9" y2="9" />
                        <line x1="18.35" y1="5.65" x2="15" y2="9" />
                    </svg>
                    <strong>Contact support</strong>
                    <p>Send an email to customer support. We're here for you!</p>
                </a>
            </li>
            <li>
                <a href="send-feedback.php">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle-2" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 2.9l-4.7 1" />
                        <line x1="12" y1="12" x2="12" y2="12.01" />
                        <line x1="8" y1="12" x2="8" y2="12.01" />
                        <line x1="16" y1="12" x2="16" y2="12.01" />
                    </svg>
                    <strong>Request a feature</strong>
                    <p>Have a brilliant new idea? Send it to us using this feedback form.</p>
                </a>
            </li>
        </ul>
    </div>
</div>


<?php
$page->footer();
?>
