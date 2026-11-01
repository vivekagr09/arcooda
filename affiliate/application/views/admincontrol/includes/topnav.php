<?php
$db = & get_instance();
$products = $db->Product_model;
$SiteSetting =$db->Product_model->getSiteSetting();
$page_id = $products->page_id();
$serverReq = checkReq();
require APPPATH."config/breadcrumb.php";
$pageKey = $db->Product_model->page_id();
?>

<div class="dashboard-wrap admin_side_bar_color">
  <div class="dashboard-main-right main-panel">
    <div class="container-fluid">
      <!-- Breadcrumb code starts here -->
      <div class="container-fluid">
        <div class="row">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <?php 
                if(count($pageSetting) > 0) {
                  $count = $pageSetting[$pageKey]['breadcrumb'];
                } else {
                  $count = 0;
                }
                foreach($pageSetting[$pageKey]['breadcrumb'] as $key => $value) { ?> 
                  <li class="breadcrumb-item <?= $count == $key ? 'active' : '' ?>">
                    <a href="<?= $value['link'] ?>"><?= $value['title'] ?></a>
                  </li>
              <?php } ?>
            </ol>
          </nav>
        </div>
      </div>
      <!-- Breadcrumb code ends here -->