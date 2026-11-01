<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    ?>

    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if(isset($meta_title)){ ?> <meta property="og:title" content="<?php echo $meta_title ?>"><?php } ?>
    <?php if(isset($meta_description)){ ?> <meta property="og:description" content="<?php echo $meta_description ?>"><?php } ?>
    <?php if(isset($meta_image)){ ?> <meta property="og:image" content="<?php echo $meta_image ?>"><?php } ?>
    <meta property="og:url" content="<?php echo $actual_link ?>">
    <meta name="twitter:card" content="summary_large_image">
    
    <?php if($store_setting['favicon']){ ?>
        <link rel="icon" href="<?php echo base_url('assets/images/site/'.$store_setting['favicon']) ?>" type="image/*" sizes="16x16">
    <?php } ?>

    <title><?php echo $store_setting['name'] ?>  <?php echo isset($meta_title) ? '- ' . $meta_title : '' ?></title>
 
    

    <script src="<?= base_url('assets/store/classified/'); ?>dependencies/jquery/js/jquery.min.js"></script>

    <script type="text/javascript">
        $(function () {
            var cookies = document.cookie.split(';').reduce((cookies, cookie) => {
                const [name, val] = cookie.split('=').map(c => c.trim());
                cookies[name] = val;
                if(name && val) {
                    localStorage.setItem(name, val); 
                }
                return cookies;
            }, {});
        });

        (function ($) {
            $.fn.btn = function (action) {
                var self = $(this);
                if (action == 'loading') {
                    if ($(self).attr("disabled") == "disabled") {
                    }
                    $(self).attr("disabled", "disabled");
                    $(self).attr('data-btn-text', $(self).html());
                    $(self).html('<div class="spinner-border spinner-border-sm"></div> ' + $(self).text());
                }
                if (action == 'reset') {
                    $(self).html($(self).attr('data-btn-text'));
                    $(self).removeAttr("disabled");
                }
            }
        })(jQuery);
        
        var formDataFilter = function(formData) {
            if (!(window.FormData && formData instanceof window.FormData)) return formData
            if (!formData.keys) return formData
            var newFormData = new window.FormData()
            Array.from(formData.entries()).forEach(function(entry) {
                var value = entry[1]
                if (value instanceof window.File && value.name === '' && value.size === 0) {
                    newFormData.append(entry[0], new window.Blob([]), '')
                } else {
                    newFormData.append(entry[0], value)
                }
            })
            
            return newFormData
        }

        <?php if($is_preview_page) { ?>
            $.ajaxSetup({
                headers: { 'aff-preview-page': 1 }
            });

            window.AFF_PREVIEW_PAGE = 1;
        <?php } ?>
    </script>

    
    
</head>

<body>

