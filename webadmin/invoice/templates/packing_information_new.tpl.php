<?php
$packingInfo = invoicePackingInformationFromDatabase($invoice["id"]);
function product_packaging_row(
    $count = "[ROW_COUNT]",
    $product = [],
    $inputVisible = false,
    $onlyPackaging = false
) {
    $row_name = "productPackaging[" . $count . "]";

    $hidden = [
        $row_name . "[product_id]" => $product["product_id"] ?? "",
        $row_name . "[invoice_id]" => $product["invoice_id"] ?? "",
        "Packaging_product_rank[" . $count . "]" => $count,
         $row_name . "[saved_product]" => $onlyPackaging ? 0 : 1,
    ];

    ob_start();
    ?>
    <tr id="packing_product_row[<?= $count ?>]" class="packing_product_row <?= $onlyPackaging ? 'onlyPackagingrow' : ''?>" data-product = "packing_product_row[<?= $product["product_id"] ?? $count ?>]">
        <td style="white-space:nowrap;">
            <?= str_replace("\n", "", formatHTMLInputs($hidden)) ?>
            <input name="<?= $row_name ?>[is_allow]" type="checkbox" value="1" <?= !isset(
    $product["is_allowed"]
) || $product["is_allowed"] == "y"
    ? "checked"
    : "" ?> />
        </td>
        <td>
            <a href="#" onclick="rankPackingProduct(<?= $count ?>, -1); return false">
                <img alt="/\" src="/webadmin/images/up_arrow.gif" border="0" />
            </a>
            <a href="#" onclick="rankPackingProduct(<?= $count ?>, 1); return false">
                <img alt="\/" src="/webadmin/images/down_arrow.gif" border="0" />
            </a>
        </td>
        <td id="<?= $row_name ?>_product_name">
            <?php if ($inputVisible) {
                echo '<input name="' .
                    $row_name .
                    '[product_name]" type="text"  value=""  />';
            } else {
                echo '<input name="' .
                    $row_name .
                    '[product_name]" type="hidden"  value="' .
                    $product["product_name"] .
                    '"  />';
                echo htmlspecialchars($product["product_name"] ?? "");
            } ?>
        </td>
        <td>
            <input name="<?= $row_name ?>[width_allow]" type="checkbox" value="1" class="checkbox" <?= $inputVisible
    ? ""
    : 'style="display:none;"' ?> <?= $product["rotate_width"] == "y"
     ? "checked"
     : "" ?> />
            <?php if ($inputVisible) {
                echo '<input name="' .
                    $row_name .
                    '[width]" type="text" value="" size="3"/>';
            } else {
                 ?>

                <input name="<?= $row_name ?>[width]" type="text" min="1" value="<?= htmlspecialchars(
    $product["dimensions"]["width"] ?? ($product["width"] ?? "")
) ?>" size="3" class="actualdata" style="display:none;" />
                <input name="<?= $row_name ?>[width_packing]" type="text" min="1" value="<?= htmlspecialchars(
    $product["bulk_sizes"][1][0]["width"] ?? ($product["width"] ?? "")
) ?>" size="3" class="packingdata" style="display:none;" />
            <?php
            } ?>

        </td>
        <td>
            <input name="<?= $row_name ?>[depth_allow]" type="checkbox" value="1" class="checkbox" <?= $inputVisible
    ? ""
    : 'style="display:none;"' ?> <?= $product["rotate_depth"] == "y"
     ? "checked"
     : "" ?> />
            <?php if ($inputVisible) {
                echo '<input name="' .
                    $row_name .
                    '[depth]" type="text" value="" size="3"/>';
            } else {
                 ?>
                <input name="<?= $row_name ?>[depth]" type="text" min="1" value="<?= htmlspecialchars(
    $product["dimensions"]["depth"] ?? ($product["depth"] ?? "")
) ?>" size="3" class="actualdata" style="display:none;" />
                <input name="<?= $row_name ?>[depth_packing]" type="text" min="1" value="<?= htmlspecialchars(
    $product["bulk_sizes"][1][0]["depth"] ?? ($product["depth"] ?? "")
) ?>" size="3" class="packingdata" style="display:none;" />
            <?php
            } ?>
        </td>
        <td>
            <input name="<?= $row_name ?>[height_allow]" type="checkbox" value="1" class="checkbox" <?= $inputVisible
    ? ""
    : 'style="display:none;"' ?> <?= $product["rotate_depth"] == "y"
     ? "checked"
     : "" ?> />
            <?php if ($inputVisible) {
                echo '<input name="' .
                    $row_name .
                    '[height]" type="text" value="" size="3"/>';
            } else {
                 ?>
                <input name="<?= $row_name ?>[height]" type="text" min="1" value="<?= htmlspecialchars(
    $product["dimensions"]["height"] ?? ($product["height"] ?? "")
) ?>" size="3" class="actualdata" style="display:none;" />
                <input name="<?= $row_name ?>[height_packing]" type="text" min="1" value="<?= htmlspecialchars(
    $product["bulk_sizes"][1][0]["height"] ?? ($product["height"] ?? "")
) ?>" size="3" class="packingdata" style="display:none;" />
            <?php
            } ?>
        </td>
        <td id="<?= $row_name ?>_weight">
            <?php if ($inputVisible) {
                echo '<input name="' .
                    $row_name .
                    '[weight]" type="text" value="" style="width: 70%;"/>';
            } else {
                echo '<span class="actualdata" style="display:none;">' .
                    htmlspecialchars(
                        $product["dimensions"]["weight"] ??
                            ($product["weight"] ?? "")
                    ) .
                    "</span>";
                echo '<input name="' .
                    $row_name .
                    '[weight]" type="hidden" value="' .
                    htmlspecialchars(
                        $product["dimensions"]["weight"] ??
                            ($product["weight"] ?? "")
                    ) .
                    '" style="width: 70%;"/>';
                echo '<span class="packingdata" style="display:none;">' .
                    htmlspecialchars(
                        $product["bulk_sizes"][1][0]["weight"] ??
                            ($product["weight"] ?? "")
                    ) .
                    "</span>";
                echo '<input name="' .
                    $row_name .
                    '[weight_packing]" type="hidden" value="' .
                    htmlspecialchars(
                        $product["bulk_sizes"][1][0]["weight"] ??
                            $product["weight"]
                    ) .
                    '" style="width: 70%;"/>';
            } ?>
        </td>
        <td id="<?= $row_name ?>_space">
            <span class="actualdata" style="display:none;"><?= htmlspecialchars(
                $product["space"] ?? ""
            ) ?></span>
            <input type="hidden" name="<?= $row_name ?>[space]" value="<?= htmlspecialchars(
    $product["space"] ?? ""
) ?>">
            <span class="packingdata" style="display:none;"><?= htmlspecialchars(
                $product["bulk_space"] ?? ($product["space"] ?? "")
            ) ?></span>
            <input type="hidden" name="<?= $row_name ?>[bulk_space]" value="<?= htmlspecialchars(
    $product["bulk_space"] ?? ($product["space"] ?? "")
) ?>">
        </td>
        <td id="<?= $row_name ?>_qty">
            <?php if ($inputVisible) {
                echo '<input name="' .
                    $row_name .
                    '[product_qty]" type="number" value="" style="width: 70%;"/>';
            } else {
                echo '<input name="' .
                    $row_name .
                    '[product_qty]" type="hidden" value="' .
                    ($product["product_qty"] ?? $product["quantity"]) .
                    '" style="width: 70%;"/>';
                echo "<span>" .
                    htmlspecialchars(
                        $product["product_qty"] ?? ($product["quantity"] ?? "")
                    ) .
                    "</span>";
            } ?>
        </td>
        <td id="<?= $row_name ?>_total_weight">
            <span class="actualdata" style="display:none;" id="<?= $row_name ?>[total_weight]"><?= htmlspecialchars(
                $product["total_weight"] ?? ""
            ) ?></span>
            <input type="hidden" name="<?= $row_name ?>[total_weight]" value="<?= htmlspecialchars(
    $product["total_weight"] ?? ""
) ?>">
            <span class="packingdata" style="display:none;"><?= htmlspecialchars(
                $product["bulk_total_weight"] ??
                    ($product["total_weight"] ??
                        ($product["total_weight"] ?? ""))
            ) ?></span>
            <input type="hidden" name="<?= $row_name ?>[bulk_total_weight]" value="<?= htmlspecialchars(
    $product["bulk_total_weight"] ?? ($product["total_weight"] ?? "")
) ?>">
        </td>
        <td id="<?= $row_name ?>_total_space">
            <span class="actualdata" style="display:none;" id="<?= $row_name ?>[total_space]"><?= htmlspecialchars(
                $product["total_space"] ?? ""
            ) ?></span>
            <input type="hidden" name="<?= $row_name ?>[total_space]" value="<?= htmlspecialchars(
    $product["total_space"] ?? ""
) ?>">
            <span class="packingdata" style="display:none;" id="<?= $row_name ?>[bulk_total_space]"><?= htmlspecialchars(
                $product["bulk_total_space"] ?? ($product["total_space"] ?? "")
            ) ?></span>
            <input type="hidden" name="<?= $row_name ?>[bulk_total_space]" value="<?= htmlspecialchars(
    $product["bulk_total_space"] ?? ($product["total_space"] ?? "")
) ?>">
        </td>
        <td style="white-space:nowrap;">
            <input name="<?= $row_name ?>[allow_stack]" type="checkbox" value="1" <?= 
            $product["allow_stack"] == "y"
                ? "checked"
                : "" ?> />
        </td>
        <td>
            <input type="button" onclick="confirmDelete(<?= $count ?>)" value="Delete" >
        </td>
    </tr>
<?php return ob_get_clean();
}
?>

<?= formatHTMLTitle(
    '<a href="javascript:toggleSection(\'packing_information_new\');" > Packaging Information New</a>'
) ?>
<div id="packing_information_new">
    <textarea name="packing_string" style="display:none;"><?= htmlspecialchars(
        $invoice["packing_string"]
    ) ?></textarea>
    <input type="hidden" name="packing_type" value="<?= $invoice[
        "packing_type"
    ] ?>">
    <table class="table_grid">
        <tr>
            <th style="width:2%"></th>
            <th style="width:5%"></th>
            <th style="width:17%" id="pro-name-packaging">

            </th>
            <th style="width:10%">Width(cms)</th>
            <th style="width:10%">Depth(cms)</th>
            <th style="width:10%">Height(cms)</th>
            <th style="width:10%">Weight(kg)</th>
            <th style="width:10%">Space(m³)</th>
            <th style="width:10%">Pieces</th>
            <th style="width:5%">Total Weight(kg)</th>
            <th style="width:5%">Total Space(m³)</th>
            <th style="width:5%">Allow Stack</th>
            <th style="width:13%">Action</th>
        </tr>
        <tbody id="product_packaging_table" name="product_packaging_table">
            <?php
            $pcount = 1;
            if (count($packingInfo) == 0) {
                if (isset($invoice_products)) {
                    foreach ($invoice_products as $product) {
                        if ("n" == $product["generic"]) {
                            $product_str = productStrJoin(
                                $product["product_id"],
                                $product["sub_id"]
                            );

                            $dimensions = productDimensionFromDatabase(
                                $product_str
                            );
                            if (!is_array(reset($dimensions))) {
                                $dimensions = [$dimensions];
                            }
                            $product["dimensions"] = reset($dimensions);
                            $product["space"] = 0.0;
                            $product["total_weight"] = 0.0;
                            $product["total_space"] = 0.0;
                            // print_r($product['dimensions']);

                            if (is_array($product["dimensions"])) {
                                $width = floatval(
                                    $product["dimensions"]["width"]
                                );
                                $depth = floatval(
                                    $product["dimensions"]["depth"]
                                );
                                $height = floatval(
                                    $product["dimensions"]["height"]
                                );
                                $volume = $width * $depth * $height;
                                $space = round($volume / 5000000, 3); // Force 3 decimal places
                                $product["space"] = number_format(
                                    $space,
                                    3,
                                    ".",
                                    ""
                                );
                                $totalWeight =
                                    $product["dimensions"]["weight"] *
                                    $product["product_qty"];
                                $product["total_weight"] =
                                    floor($totalWeight) == $totalWeight
                                        ? number_format(
                                            $totalWeight,
                                            0,
                                            ".",
                                            ""
                                        )
                                        : number_format(
                                            $totalWeight,
                                            2,
                                            ".",
                                            ""
                                        );
                                $product["total_space"] = number_format(
                                    $space * $product["product_qty"],
                                    3,
                                    ".",
                                    ""
                                );
                            }
                            $packing = [];
                            if (function_exists("productPackingFromDatabase")) {
                                $packing = productPackingFromDatabase(
                                    $product_str,39, true
                                );
                                //Hack: If function only returns 1 packing, make this into an array of packing dimensions
                                if (is_null($packing)) {
                                    $packing = [];
                                }
                                if (
                                    count($packing) &&
                                    !is_array(reset($packing))
                                ) {
                                    $packing = [$packing];
                                }
                            }
                            /**
                             * Collect the packaging sizes
                             */
                            $bulk_sizes = [];

                            foreach ($dimensions as $row) {
                                $bulk_sizes[1] = [
                                    [
                                        "qty" => 1,
                                        "weight" => $row["weight"],
                                        "cubic" => bcmul(
                                            $row["cubic"],
                                            0 != $row["cubic_divisor"]
                                                ? $row["cubic_divisor"]
                                                : "5000.0",
                                            3
                                        ),
                                        "width" => $row["pack_width"],
                                        "depth" => $row["pack_depth"],
                                        "height" => $row["pack_height"],
                                    ],
                                ];
                            }
                            foreach ($dimensions as $row) {
                                $bulk_sizes[intval($row["carton_units"])] = [
                                    [
                                        "qty" => $row["carton_units"],
                                        "weight" => $row["carton_weight"],
                                        "cubic" => bcmul(
                                            $row["carton_cubic"],
                                            0 != $row["carton_cubic_divisor"]
                                                ? $row["carton_cubic_divisor"]
                                                : "5000.0",
                                            3
                                        ),
                                        "width" => $row["carton_width"],
                                        "depth" => $row["carton_depth"],
                                        "height" => $row["carton_height"],
                                    ],
                                ];
                            }
                            if (
                                function_exists(
                                    "productBulkWeightsFromDatabase"
                                )
                            ) {
                                foreach (
                                    productBulkWeightsFromDatabase($product_str)
                                    as $row
                                ) {
                                    $bulk_sizes[$row["bulk_qty"]] = [
                                        [
                                            "qty" => $row["bulk_qty"],
                                            "weight" => $row["weight"],
                                            "cubic" => bcmul(
                                                $row["cubic"],
                                                0 != $row["cubic_divisor"]
                                                    ? $row["cubic_divisor"]
                                                    : "5000.0",
                                                3
                                            ),
                                            "width" => $row["width"],
                                            "depth" => $row["depth"],
                                            "height" => $row["height"],
                                        ],
                                    ];
                                }
                            }

                            if (count($packing)) {
                                /**
                                 * Hack for invalid product updates
                                 */
                                foreach ($packing as $k1 => $v1) {
                                    if (
                                        "y" /*$v1["pallet_id"] != 0*/ ==
                                        $v1["pallet_enable"]
                                    ) {
                                        $try1 = bcmul(
                                            bccomp(
                                                $v1["pallet_width"],
                                                $v1["width"],
                                                1
                                            ) > 0
                                                ? $v1["pallet_width"]
                                                : $v1["width"],
                                            bccomp(
                                                $v1["pallet_depth"],
                                                $v1["depth"],
                                                1
                                            ) > 0
                                                ? $v1["pallet_depth"]
                                                : $v1["depth"],
                                            2
                                        );
                                        $try2 = bcmul(
                                            bccomp(
                                                $v1["pallet_width"],
                                                $v1["depth"],
                                                1
                                            ) > 0
                                                ? $v1["pallet_width"]
                                                : $v1["depth"],
                                            bccomp(
                                                $v1["pallet_depth"],
                                                $v1["width"],
                                                1
                                            ) > 0
                                                ? $v1["pallet_depth"]
                                                : $v1["width"],
                                            2
                                        );
                                        if (bccomp($try1, $try2, 2) < 0) {
                                            $packing[$k1]["volume"] = bcmul(
                                                $try1,
                                                bcadd(
                                                    $v1["pallet_height"],
                                                    $v1["height"],
                                                    1
                                                ),
                                                3
                                            );
                                        } else {
                                            $packing[$k1]["volume"] = bcmul(
                                                $try2,
                                                bcadd(
                                                    $v1["pallet_height"],
                                                    $v1["height"],
                                                    1
                                                ),
                                                3
                                            );
                                        }
                                        $packing[$k1]["weight"] = bcadd(
                                            $v1["weight"],
                                            $v1["pallet_weight"],
                                            3
                                        );
                                        $packing[$k1]["volume_divisor"] = "1";
                                    }
                                }
                                $bulk_sizes[1] = [];
                                foreach ($packing as $row) {
                                    $bulk_sizes[1][] = [
                                        "qty" => 1,
                                        "weight" => $row["weight"],
                                        "cubic" => bcmul(
                                            $row["volume"],
                                            0 != $row["volume_divisor"]
                                                ? $row["volume_divisor"]
                                                : "5000.0",
                                            3
                                        ),
                                        "width" => $row["width"],
                                        "depth" => $row["depth"],
                                        "height" => $row["height"],
                                    ];
                                }
                            }            
                            //print_r($bulk_sizes);

                            unset($bulk_sizes[0]);
                            krsort($bulk_sizes);

                            /**
                             * Estimate packages with no dimensions
                             */
                            $changes = true;
                            while ($changes) {
                                $changes = false;
                                foreach ($bulk_sizes as $k1 => $v1) {
                                    if (
                                        count($v1) == 1 &&
                                        (bccomp($v1[0]["width"], "0.00", 2) ==
                                            0 ||
                                            bccomp(
                                                $v1[0]["depth"],
                                                "0.00",
                                                2
                                            ) == 0 ||
                                            bccomp(
                                                $v1[0]["height"],
                                                "0.00",
                                                2
                                            ) == 0)
                                    ) {
                                        foreach ($bulk_sizes as $k2 => $v2) {
                                            if ($k2 < $k1 && count($v2) == 1) {
                                                if (
                                                    bccomp(
                                                        $v2[0]["width"],
                                                        "0.00",
                                                        2
                                                    ) != 0 &&
                                                    bccomp(
                                                        $v2[0]["depth"],
                                                        "0.00",
                                                        2
                                                    ) != 0 &&
                                                    bccomp(
                                                        $v2[0]["height"],
                                                        "0.00",
                                                        2
                                                    ) != 0
                                                ) {
                                                    $multiplier =
                                                        ($k1 + 0.0) /
                                                        ($k2 + 0.0);
                                                    $root_x = ceil(
                                                        pow(
                                                            $multiplier,
                                                            1.0 / 3.0
                                                        )
                                                    );
                                                    $root_y = ceil(
                                                        sqrt(
                                                            $multiplier /
                                                                $root_x
                                                        )
                                                    );
                                                    $root_z = ceil(
                                                        $multiplier /
                                                            ($root_x * $root_y)
                                                    );
                                                    $dim = [
                                                        $v2[0]["width"],
                                                        $v2[0]["depth"],
                                                        $v2[0]["height"],
                                                    ];
                                                    bcscale(2);
                                                    usort($dim, "bccomp");
                                                    $bulk_sizes[$k1][0][
                                                        "width"
                                                    ] = bcmul(
                                                        $dim[0],
                                                        $root_x,
                                                        2
                                                    );
                                                    $bulk_sizes[$k1][0][
                                                        "depth"
                                                    ] = bcmul(
                                                        $dim[1],
                                                        $root_y,
                                                        2
                                                    );
                                                    $bulk_sizes[$k1][0][
                                                        "height"
                                                    ] = bcmul(
                                                        $dim[2],
                                                        $root_z,
                                                        2
                                                    );
                                                    $changes = true;
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            /*if($product['product_id'] == 20141){
                                echo '<pre>'; print_r($bulk_sizes); die;
                            }*/
                            $onlyPackaging = false;
                            foreach ($bulk_sizes[1] as $key => $bulk) {
                               $product["bulk_sizes"][1][0] = $bulk;
                                    $volume =
                                        $bulk["width"] *
                                        $bulk["depth"] *
                                        $bulk["height"];
                                    $product["bulk_space"] = round(
                                        $volume / 5000000,
                                        3
                                    );
                                    $totalWeight =
                                        $bulk["weight"] * $product["product_qty"];
                                    $product["bulk_total_weight"] =
                                        floor($totalWeight) == $totalWeight
                                            ? number_format(
                                                $totalWeight,
                                                0,
                                                ".",
                                                ""
                                            )
                                            : number_format(
                                                $totalWeight,
                                                2,
                                                ".",
                                                ""
                                            );
                                    $product["bulk_total_space"] = number_format(
                                        $product["bulk_space"] *
                                            $product["product_qty"],
                                        3,
                                        ".",
                                        ""
                                    );
                                 if (
                                "" != $product["generic_code"] &&
                                "0" == $product["product_id"]
                                ) {
                                    $product["product_id"] = $product["generic_code"];
                                }
                                if ($product["product_id"] != "GFR") {
                                    echo product_packaging_row($pcount++, $product ,false,$onlyPackaging);
                                }
                                $onlyPackaging = true;
                            }
                            
                        }
                       
                    }
                }
            } else {
                foreach ($packingInfo as $product) {
                    $volume =
                        $product["width"] *
                        $product["depth"] *
                        $product["height"];
                    $space = round($volume / 5000000, 3); // Force 3 decimal places
                    $product["space"] = number_format($space, 3, ".", "");
                    $totalWeight = $product["weight"] * $product["quantity"];
                    $product["total_weight"] =
                        floor($totalWeight) == $totalWeight
                            ? number_format($totalWeight, 0, ".", "")
                            : number_format($totalWeight, 2, ".", "");
                    $product["total_space"] = number_format(
                        $space * $product["quantity"],
                        3,
                        ".",
                        ""
                    );
                    echo product_packaging_row($pcount++, $product);
                }
            }
            ?>
        </tbody>
        <tr>
            <td colspan="8" style="text-align: right;padding-right: 10px;"><b>Total</b></td>
            <td id="total_quantity">20</td>
            <td id="total_weight">200</td>
            <td id="total_volume">300</td>
        </tr>
    </table>
    <div id="rotation-text" style="font-size: 12px; font-style: italic; color: red; margin: 10px 30px; display: none; text-align: left;">
        * allows rotating of product
    </div>
    <br />
    <center>
        <input type="button" value="Add New Cargo" onclick="AddNewCargo()" style="margin-top: 10px;" />
        <input type="button" value="Create Without Crating"  style="margin-top: 10px; <?= 
        count($packingInfo) == 0
            ? ""
            : "display:none;" ?>" />
        <input type="button" value="Create With Crating"  style="margin-top: 10px;<?= 
        count($packingInfo) == 0
            ? ""
            : "display:none;" ?>" />
        <?php if (count($packingInfo) > 0) {
            echo '<input type="button" value="Reset"  style="margin-top: 10px;" onclick="resetPackaging(\'this will delete the tables and refresh this data, click ok\')">';
        } ?>
    </center>
</div>
<div id="container-response">
</div>
<script type="text/javascript">
    function getProductPackagingRows() {
        var product_table = getElement("product_packaging_table");
        var product_rows = [];
        if (product_table != null) {
            var rows = product_table.getElementsByTagName("tr");
            for (var i = 0; i < rows.length; i++) {
                if (rows[i].id.indexOf("packing_product_row") != -1) {
                    rows[i].style.display = "";
                    var currentid = rows[i].id.substr(19);
                     setValue('productPackaging'+currentid+'[saved_product]',1);
                    if(crating == false && rows[i].classList.contains("onlyPackagingrow")){
                        setValue('productPackaging'+currentid+'[saved_product]',0);
                        rows[i].style.display = "none";
                        continue;
                    }
                    product_rows.push(rows[i]);
                }
            }
        }
        return product_rows;
    }

    function updateRotationText() {
        var checkboxes = document.querySelectorAll('.checkbox');
        var rotationText = document.getElementById('rotation-text');
        var isAnyChecked = false;

        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                isAnyChecked = true;
            }
        });

        rotationText.style.display = isAnyChecked ? 'block' : 'none';
    }

    function attachCheckboxListeners() {
        var checkboxes = document.querySelectorAll('.checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateRotationText);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        attachCheckboxListeners();
        updateRotationText();
    });

    function rankPackingProduct(row, direction) {
        var from_row = '[' + row + ']';
        var from_rank = Number(getValue("Packaging_product_rank" + from_row));
        var swap_row = null;
        var swap_rank = null;
        var product_rows = getProductPackagingRows();
        for (var i = 0; i < product_rows.length; i++) {
            var check_row = product_rows[i].id.substr(19);
            var check_rank = Number(getValue("Packaging_product_rank" + check_row));
            if (check_row != from_row && ((direction < 0 && check_rank <
                    from_rank && (swap_rank == null || check_rank > swap_rank)
                ) || (direction > 0 && check_rank > from_rank && (
                    swap_rank == null || check_rank < swap_rank)))) {
                swap_row = check_row;
                swap_rank = check_rank;
            }
        }
        if (swap_row != null) {
            setValue("Packaging_product_rank" + from_row, swap_rank);
            setValue("Packaging_product_rank" + swap_row, from_rank);
            if (direction > 0) {
                var temp = from_row;
                from_row = swap_row;
                swap_row = temp;
            }
            var source_row;
            var target_row = getElement("packing_product_row" + swap_row);
            var target_table = target_row.parentNode;
            if (source_row = getElement("packing_product_row" + from_row)) target_table.insertBefore(source_row, target_row);
        }
    }
    var product_packaging_count = <?= max(100, count($invoice_products)) ?>;

    function AddNewCargo() {
        $("#product_packaging_table").append(<?= strtr(
            json_encode(product_packaging_row("[ROW_COUNT]", [], true,false) ?: "") ?:
            "",
            [
                "[ROW_COUNT]" => '" + product_packaging_count + "',
            ]
        ) ?>);
        product_packaging_count++;
        attachInputListeners(product_packaging_count - 1);
    }

    function attachInputListeners(rowId) {
        const rowPrefix = `productPackaging[${rowId}]`;
        const widthInput = document.querySelector(`input[name='${rowPrefix}[width]']`);
        const depthInput = document.querySelector(`input[name='${rowPrefix}[depth]']`);
        const heightInput = document.querySelector(`input[name='${rowPrefix}[height]']`);
        const weightInput = document.querySelector(`input[name='${rowPrefix}[weight]']`);
        const qtyInput = document.querySelector(`input[name='${rowPrefix}[product_qty]']`);

        const updateCalculations = () => {
            const width = parseFloat(widthInput.value) || 0;
            const depth = parseFloat(depthInput.value) || 0;
            const height = parseFloat(heightInput.value) || 0;
            const weight = parseFloat(weightInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 0;

            // Calculate exactly like PHP
            const volume = width * depth * height;
            const space = Math.round((volume / 5000000) * 1000) / 1000; // Force 3 decimal places
            const spaceM = space.toFixed(3);
            const totalWeight = weight * qty;
            const totalWeightStr = totalWeight % 1 === 0 ? totalWeight.toString() : totalWeight.toFixed(2);
            const totalSpaceM = (space * qty).toFixed(3);

            // Display values
            document.getElementById(`${rowPrefix}_space`).textContent = spaceM;
            document.getElementById(`${rowPrefix}_total_weight`).textContent = totalWeightStr;
            document.getElementById(`${rowPrefix}_total_space`).textContent = totalSpaceM;
            calculateTotalWeightAndVolume();
        };

        // Add checkbox change listeners
        const widthCheckbox = document.querySelector(`input[name='${rowPrefix}[width_allow]']`);
        const depthCheckbox = document.querySelector(`input[name='${rowPrefix}[depth_allow]']`);
        const heightCheckbox = document.querySelector(`input[name='${rowPrefix}[height_allow]']`);

        const updateRotationText = () => {
            const rotationText = document.getElementById('rotation-text');
            const allCheckboxes = document.querySelectorAll('.checkbox[type="checkbox"]');
            const isAnyChecked = Array.from(allCheckboxes).some(cb => cb.checked);
            rotationText.style.display = isAnyChecked ? 'block' : 'none';
        };

        [widthCheckbox, depthCheckbox, heightCheckbox].forEach(checkbox => {
            if (checkbox) {
                checkbox.addEventListener('change', updateRotationText);
            }
        });

        [widthInput, depthInput, heightInput, weightInput, qtyInput].forEach(input => {
            input.addEventListener('input', updateCalculations);
        });
        updateCalculations();
    }

    function toggleDimensions(showPackage) {
        const actualInputs = document.querySelectorAll('.actualdata');
        const packageInputs = document.querySelectorAll('.packingdata');
        const checkboxes = document.querySelectorAll('.checkbox');

        actualInputs.forEach(input => input.style.display = showPackage ? 'none' : '');
        packageInputs.forEach(input => input.style.display = showPackage ? '' : 'none');
        checkboxes.forEach(checkbox => checkbox.style.display = showPackage ? '' : '');
    }
    var crating = true;
    document.querySelector('input[value="Create Without Crating"]').onclick = function() {
        toggleDimensions(false);
        calculatePacking(false);
        crating = false;
        $('.onlyPackagingrow').hide();
    };

    document.querySelector('input[value="Create With Crating"]').onclick = function() {
        crating = true;
         $('.onlyPackagingrow').show();
        toggleDimensions(true);
        calculatePacking(true);
    };

    // Attach listeners to existing rows if needed
    document.addEventListener('DOMContentLoaded', () => {
        const existingRows = getProductPackagingRows();
        existingRows.forEach(rowId => {
            console.log(rowId);
            attachInputListeners(rowId);
        });
    });

    function calculatePacking(crating) {
        $.ajax({
            url: "/webadmin/packing_v2/packing_calculate.php?packing_action=invoice_crating&return_message=calculate",
            type: "POST",
            data: getProductDataforpackaging().reduce((acc, item, index) => {
                Object.keys(item).forEach(key => {
                    acc.push(`productPackaging[${index}][${key}]=${encodeURIComponent(item[key])}`);
                });
                return acc;
            }, []).join("&"), // Convert array to URL-encoded format
            success: function(response, status, xhr) {
                var contentType = xhr.getResponseHeader("Content-Type");
                if (contentType.includes("application/json")) {
                    //response = JSON.parse(response);
                } else {
                    $('#container-response').html(response);
                    return;
                }
                setValue('packing_string', response.packing_string);
                $.ajax({
                    url: "/webadmin/packing_v2/packing_view.php?return_message=view",
                    type: "POST",
                    data: getProductDataforpackaging().reduce((acc, item, index) => {
                        Object.keys(item).forEach(key => {
                            acc.push(`productPackaging[${index}][${key}]=${encodeURIComponent(item[key])}`);
                        });
                        return acc;
                    }, []).join("&"), // Convert array to URL-encoded format
                    success: function(response) {
                        setValue('packing_type', crating ? 'with_packing' : 'Without_packing');
                        $('#pro-name-packaging').html(crating ? 'with Packing' : 'Without Packing');
                        $('#container-response').html('');
                        $('#container-response').append(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function packingIgnoreErrorNew() {
        $.ajax({
            url: "/webadmin/packing_v2/packing_view.php?return_message=view",
            type: "POST",
            data: getProductDataforpackaging().reduce((acc, item, index) => {
                Object.keys(item).forEach(key => {
                    acc.push(`productPackaging[${index}][${key}]=${encodeURIComponent(item[key])}`);
                });
                return acc;
            }, []).join("&"), // Convert array to URL-encoded format
            success: function(response) {
                setValue('packing_type', crating ? 'with_packing' : 'Without_packing');
                $('#pro-name-packaging').html(crating ? 'with Packing' : 'Without Packing');
                $('#container-response').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function getProductDataforpackaging() {
        var product_rows = getProductPackagingRows();
        var postArray = []; // Main array

        for (var i = 0; i < product_rows.length; i++) {
            const checkbox = document.querySelector('input[name="productPackaging' + product_rows[i].id.substr(19) + '[is_allow]"]');
            const is_stack = document.querySelector('input[name="productPackaging' + product_rows[i].id.substr(19) + '[allow_stack]"]');
            if (checkbox.checked) {
                let tempobj = {
                    product_name: getValue("productPackaging" + product_rows[i].id.substr(19) + "[product_name]") || null,
                    product_id: getValue("productPackaging" + product_rows[i].id.substr(19) + "[product_id]") || null,
                    invoice_id: getValue("productPackaging" + product_rows[i].id.substr(19) + "[invoice_id]") || null,
                    stack: is_stack.checked,
                    width: crating ?
                        (getValue("productPackaging" + product_rows[i].id.substr(19) + "[width_packing]") ?? getValue("productPackaging" + product_rows[i].id.substr(19) + "[width]")) :
                        getValue("productPackaging" + product_rows[i].id.substr(19) + "[width]"),
                    depth: crating ?
                        (getValue("productPackaging" + product_rows[i].id.substr(19) + "[depth_packing]") ?? getValue("productPackaging" + product_rows[i].id.substr(19) + "[depth]")) :
                        getValue("productPackaging" + product_rows[i].id.substr(19) + "[depth]"),
                    height: crating ?
                        (getValue("productPackaging" + product_rows[i].id.substr(19) + "[height_packing]") ?? getValue("productPackaging" + product_rows[i].id.substr(19) + "[height]")) :
                        getValue("productPackaging" + product_rows[i].id.substr(19) + "[height]"),
                    weight: crating ?
                        (getValue("productPackaging" + product_rows[i].id.substr(19) + "[weight_packing]") ?? getValue("productPackaging" + product_rows[i].id.substr(19) + "[weight]")) :
                        getValue("productPackaging" + product_rows[i].id.substr(19) + "[weight]"),
                    product_qty: getValue("productPackaging" + product_rows[i].id.substr(19) + "[product_qty]") || 0
                };

                postArray.push(tempobj);
            }
        }
        calculateTotalWeightAndVolume();
        return postArray;
    }
</script>
<script>
    function confirmDelete(count) {
        let userResponse = confirm("Are you sure you want to remove this product?");
        if (userResponse) {
            let row = document.getElementById('packing_product_row['+count+']');
             row.remove();
              document.getElementById("invoice-form").submit();
        }    
    }

    function resetPackaging(message){
        let userResponse = confirm(message);
        if (userResponse) {
            setValue('packing_type','');
            setValue('packing_string','');
            let form = document.getElementById("invoice-form");
            form.action = ".?action=reset-packaging"; 
            form.submit();
        } 
    }

    function calculateTotalWeightAndVolume() {
        var product_rows = getProductPackagingRows();
        var totalWeight = 0.00;
        var totalQuantity = 0;
        var totalSpace = 0.00;

        for (var i = 0; i < product_rows.length; i++) {
            var qty = parseInt(getValue("productPackaging" + product_rows[i].id.substr(19) + "[product_qty]")) || 0;
            var weight = parseFloat(crating ?
                (getValue("productPackaging" + product_rows[i].id.substr(19) + "[bulk_total_weight]") || getValue("productPackaging" + product_rows[i].id.substr(19) + "[total_weight]")) :
                getValue("productPackaging" + product_rows[i].id.substr(19) + "[total_weight]")) || 0;

            var space = parseFloat(crating ?
                (getValue("productPackaging" + product_rows[i].id.substr(19) + "[bulk_total_space]") || getValue("productPackaging" + product_rows[i].id.substr(19) + "[total_space]")) :
                getValue("productPackaging" + product_rows[i].id.substr(19) + "[total_space]")) || 0;

            totalQuantity += qty;
            totalWeight += weight;
            totalSpace += space;
        }

        $('#total_quantity').html(totalQuantity);
        $('#total_weight').html(totalWeight.toFixed(2));
        $('#total_volume').html(totalSpace.toFixed(2));
    }
</script>
<?php if (
    $invoice["packing_type"] == "with_packing" ||
    $invoice["packing_type"] == "Without_packing"
) { ?>
    <script type="text/javascript">
        $(document).ready(function() {
            if (getValue('packing_type') == 'with_packing') {
                document.querySelector('input[value="Create With Crating"]').click();
            } else {
                document.querySelector('input[value="Create Without Crating"]').click();
            }
        });
    </script>
<?php } ?>
