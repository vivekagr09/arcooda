<?php
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    <url>
        <loc><?php echo base_url(); ?></loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>

    <?php
    if ($active_theme == 'multiple_pages') {
        foreach ($theme_pages as $page) {
            if ($page->slug != '/') {
                if ($page->page_type == 'editable')
                    $pageurl = base_url() . 'p/' . $page->slug;
                else
                    $pageurl = base_url($page->slug);
                ?>
                <url>
                    <loc><?php echo $pageurl; ?></loc>
                    <priority>0.5</priority>
                    <changefreq>daily</changefreq>
                </url>
                <?php
            }
        }
    } else if ($active_theme == 'custom_1' || $active_theme == 'custom_2' || $active_theme == 'custom_3' || $active_theme == 'custom_4' || $active_theme == 'custom_5' || $active_theme == 'custom_6' || $active_theme == 'custom_7' || $active_theme == 'custom_8' || $active_theme == 'custom_9' || $active_theme == 'custom_10') {
        ?>
        <url>
            <loc><?= base_url() . 'term-condition' ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
        <url>
            <loc><?= base_url() . 'register' ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
        <url>
            <loc><?= base_url() . 'register/vendor' ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
        <url>
            <loc><?= base_url() . 'forget-password' ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
    <?php
    }
    ?>

    <url>
        <loc><?= base_url() . 'store/' ?></loc>
        <priority>0.5</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc><?= base_url() . 'store/about' ?></loc>
        <priority>0.5</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc><?= base_url() . 'store/contact' ?></loc>
        <priority>0.5</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc><?= base_url() . 'store/login' ?></loc>
        <priority>0.5</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc><?= base_url() . 'store/policy' ?></loc>
        <priority>0.5</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc><?= base_url() . "store/category/" ?></loc>
        <priority>0.5</priority>
        <changefreq>daily</changefreq>
    </url>

    <?php
    if (isset($store_footer_menu["footer_menu"])) {
        $footer_menu = json_decode($store_footer_menu["footer_menu"]);
        foreach ($footer_menu as $fm) {
            for ($i = 0; $i < sizeOf($fm->links); $i++) {
                ?>
                <url>
                    <loc><?php echo $fm->links[$i]->url; ?></loc>
                    <priority>0.5</priority>
                    <changefreq>daily</changefreq>
                </url>
                <?php
            }
        }
    }
    ?>

    <?php
    foreach ($categorys as $category) {
        ?>
        <url>
            <loc><?php echo base_url() . "store/category/" . $category->slug; ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
        <?php
    }
    ?>

    <?php
    foreach ($products as $product) {
        if ($store_mode == 'sales')
            $prodcuturl = base_url() . "store/product/" . $product->product_id;
        else
            $prodcuturl = base_url() . "store/" . base64_encode((int)$product->product_id) . "/product/" . $product->product_slug;
        ?>
        <url>
            <loc><?php echo $prodcuturl; ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
    <?php
    }
    ?>
</urlset>