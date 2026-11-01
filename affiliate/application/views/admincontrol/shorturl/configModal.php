<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title m-0"><?= __('admin.url_to_be_shortened') ?></h5>
      <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="control-label"><?= __('admin.original_url') ?></label>
        <input class="form-control" value='<?= $o_url ?>' readonly>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
    </div>
  </div>
</div>