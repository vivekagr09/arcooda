<div class="row">
    <div class="col">
        <div class="card m-b-30">
            <div class="card-header">
                <h4 class="card-title float-start"><?= isset($lang) ? __("admin.edit_language") : __("admin.add_language") ?></h4>
            </div>
            <div class="card-body">
                <form id="language-form" enctype="multipart/form-data" action="<?= base_url("admincontrol/update_language") ?>" method="POST">
                    <input type="hidden" name="id" value="<?= isset($lang) ? $lang['id'] : '0' ?>">
                    <div class="mb-3">
                        <label class="form-label"><?= __("admin.language_name") ?></label>
                        <?php if(isset($lang) && isset($lang['name'])) { ?>
                            <input name="name" value="<?= $lang['name'] ?>" readonly/>
                        <?php } else { ?>
                            <select name="name" required>
                                <option value="" disabled selected><?= __('admin.select') ?> <?= __("admin.language_name") ?></option>
                                <?php foreach ($languages as $key => $value) { ?>
                                    <option value="<?= $value ?>"><?= $value ?></option>
                                <?php } ?>                                         
                            </select>
                        <?php } ?>
                    </div>

                    <div class="flag-file-chooser">
                        <ul>
                            <?php
                                if(isset($lang['flag'])) $selected = $lang['flag'];
                            ?>

                            <?php foreach ($flags_code as $key => $value) { ?>
                                <li>
                                    <label>
                                        <input data-flag_code="<?= $key ?>" <?= $selected == $value ? 'checked' : '' ?> type="radio" name="flag" value="<?= $value ?>">
                                        <img src="<?= base_url($value) ?>">
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <label class="form-label"><?= __("admin.status") ?> </label>
                                <div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="1" <?= (isset($lang) && $lang['status'] == '1') ? "checked" :  '' ?>>
                                        <label class="form-check-label" for="statusSwitch"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?= __("admin.set_default") ?></label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="defaultSwitch" name="is_default" value="1" <?= (isset($lang) && $lang['is_default'] == '1') ? "checked" :  '' ?>>
                                    <label class="form-check-label" for="defaultSwitch"></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?= __("admin.is_rtl") ?></label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="rtlSwitch" name="is_rtl" value="1" <?= (isset($lang) && $lang['is_rtl'] == '1') ? "checked" :  '' ?>>
                                    <label class="form-check-label" for="rtlSwitch"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-submit"><?= __("admin.save_changes") ?></button>
                    <a href="<?= base_url("admincontrol/language") ?>" class="btn btn-secondary"><?= __("admin.cancel") ?></a>
                </form>
            </div>
        </div> 
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', (event) => {
    let languages = null;
    let countries_with_languages = null;

    fetch("<?= base_url('assets/data/countries_with_languages.json'); ?>")
    .then(response => response.json())
    .then(data => { countries_with_languages = data; });

    fetch("<?= base_url('assets/data/languages.json'); ?>")
    .then(response => response.json())
    .then(data => { languages = data; });

    let selectElem = document.querySelector('select[name="name"]');
    if (selectElem) {
        selectElem.addEventListener('change', function() {
            let langName = this.value;
            let langCode = Object.keys(languages).find(key => languages[key] === langName);

            let country = countries_with_languages.filter(function (e) {
                let languages = e.languages.split(",");
                return languages.indexOf(langCode) != -1;
            });

            if(country.length > 0) {
                document.querySelectorAll('input[name="flag"]').forEach(function(el) {
                    el.parentElement.parentElement.style.display = 'none';
                });
                for (let index = 0; index < country.length; index++) {
                    const iso_code = country[index].iso_code.toLowerCase();
                    const el = document.querySelector('input[name="flag"][data-flag_code="'+iso_code+'"]');
                    el.parentElement.parentElement.style.display = 'block';
                    if(index == 0) { el.click(); }
                }
            } else {
                document.querySelectorAll('input[name="flag"]').forEach(function(el) {
                    el.parentElement.parentElement.style.display = 'block';
                });
            }
        });
    }
});
</script>
