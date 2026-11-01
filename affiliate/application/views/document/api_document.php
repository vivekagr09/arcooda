<!-- MAIN -->
<div class="main">
   <!-- MAIN CONTENT -->
   <div class="main-content">
      <div class="container-fluid">

         <?= $this->load->view('document/doc_intro', null, true); ?>

         <?= $this->load->view('document/doc_user', ['registration_fields'=>$registration_fields], true); ?>

         <?= $this->load->view('document/doc_dashboard', null, true); ?>

         <?= $this->load->view('document/doc_aff_links', null, true); ?>

         <?= $this->load->view('document/doc_my_logs', null, true); ?>

         <?= $this->load->view('document/doc_my_network', null, true); ?>

         <?= $this->load->view('document/doc_user_reports', null, true); ?>

         <?= $this->load->view('document/doc_contact_to_admin', null, true); ?>

         <?= $this->load->view('document/doc_category', null, true); ?>

         <?= $this->load->view('document/doc_user_wallet', null, true); ?>

         <?= $this->load->view('document/doc_my_order', null, true); ?>

         <?= $this->load->view('document/doc_subscription_plan', null, true); ?>

         <?= $this->load->view('document/doc_vendor_market_place', null, true); ?>

         <?= $this->load->view('document/doc_vendor_market_tools', null, true); ?>

         <?= $this->load->view('document/doc_notification', null, true); ?>
      </div>
      <!-- container-fluid -->
   </div>
   <!-- END MAIN CONTENT -->
</div>
<!-- END MAIN -->
<script src="<?= base_url('assets/js/') ?>pretty-print-json.js"></script>
<script type="text/javascript">
   $('.response-view').each(function( index ) {
      $(this).html(prettyPrintJson.toHtml(JSON.parse($(this).text())));
   });
</script>