
<style>
    .online-text-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .online-text-table > tbody > tr > th {
        width: 125px;
        vertical-align: top;
        padding: 8px 5px;
        text-align: right;
    }

    .online-text-table > tbody > tr > td {
        padding: 6px 10px;
    }

    .online-text-category {
        width: 100%;
    }

    .online-text-row {
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 27px;
        margin-bottom: 2px;
        gap: 7px;
    }

    .online-text-icon {
        display: inline-block;
        width: 22px;
        min-width: 22px;
        text-align: center;
        font-size: 18px;
        color: #34495e;
    }

    .online-text-row input[type="checkbox"] {
        width: 14px;
        height: 14px;
        margin: 0;
        flex-shrink: 0;
    }

    .online-text-row label {
        display: inline-block;
        width: 112px;
        min-width: 112px;
        margin: 0;
        font-weight: bold;
        white-space: nowrap;
        cursor: pointer;
    }

    .online-text-row input[type="text"] {
        display: block;
        min-width: 0;
        width: 50%;
        height: 27px;
        box-sizing: border-box;
        margin: 0;
    }

    .online-text-country {
        gap: 8px;
        width: 100%;
    }

    .online-text-country select {
        min-width: 0;
        width: 50%;
        height: 27px;
        box-sizing: border-box;
    }

    .online-text-flag {
        display: inline-block;
        width: 26px;
        min-width: 26px;
        height: 20px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
    }
</style>

<?= formatHTMLTitle(
    '<a href="#" onclick="toggleSection(\'sectionDistributorType\');return false">Online Text</a>',
    ''
) ?>
<div id="sectionDistributorType">
    <table class="table_fields">
        <tbody>
            <tr>
                <th>Country :</th>
                <td>
                    <div class="online-text-country">
                        <select name="item[country_location]"
                                id="online_text_country"
                                class="wide">
                            <option value="">Not Selected</option>
                            <?= formatHTMLOptions(
                                wareHouseGetOptionList('country_location'),
                                $itemOnlineText['country_location'] ?? ''
                            ) ?>
                        </select>
                        <span id="onlineTextCountryFlag"
                            class="online-text-flag flagicon-<?=  $item['country_location'] ?>"></span>
                    </div>
                </td>
            </tr>

            <tr>
                <th style="vertical-align:top;padding-top:8px;">
                    Category :
                </th>

                <td>
                    <div class="online-text-category">

                        <!-- Warehouse -->
                        <div class="online-text-row">
                            <i class="fa fa-home online-text-icon"></i>

                            <input type="checkbox"
                                id="online_text_warehouse_check"
                                name="itemOnlineText[show_warehouse]"
                                value="y"
                                <?= ($itemOnlineText['show_warehouse'] ?? '') == 'y'
                                    ? 'checked="checked"' : '' ?>>

                            <label for="online_text_warehouse_check">
                                Warehouse
                            </label>

                            <input type="text"
                                name="itemOnlineText[warehouse]"
                                id="online_text_warehouse"
                                value="<?= htmlspecialchars(
                                    $itemOnlineText['warehouse'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">
                        </div>

                        <!-- Delivery Time -->
                        <div class="online-text-row">
                        <i class="fa fa-truck online-text-icon"></i>

                            <input type="checkbox"
                                id="online_text_delivery_check"
                                name="itemOnlineText[show_delivery_time]"
                                value="y"
                                <?= ($itemOnlineText['show_delivery_time'] ?? '') == 'y'
                                    ? 'checked="checked"' : '' ?>>

                            <label for="online_text_delivery_check">
                                Delivery Time
                            </label>

                            <input type="text"
                                name="itemOnlineText[delivery_time]"
                                id="online_text_delivery"
                                value="<?= htmlspecialchars(
                                    $itemOnlineText['delivery_time'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">
                        </div>

                        <!-- Freight Costs -->
                        <div class="online-text-row">
                        <svg class="online-text-icon"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="18" height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 8v8"/>
                                <path d="M15 10H10.5a1.5 1.5 0 0 0 0 3h3a1.5 1.5 0 0 1 0 3H9"/>
                            </svg>

                            <input type="checkbox"
                                id="online_text_freight_check"
                                name="itemOnlineText[show_freight_costs]"
                                value="y"
                                <?= ($itemOnlineText['show_freight_costs'] ?? '') == 'y'
                                    ? 'checked="checked"' : '' ?>>

                            <label for="online_text_freight_check">
                                Freight Costs
                            </label>

                            <input type="text"
                                name="itemOnlineText[freight_costs]"
                                id="online_text_freight"
                                value="<?= htmlspecialchars(
                                    $itemOnlineText['freight_costs'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">
                        </div>

                        <!-- Notes -->
                        <div class="online-text-row">
                            <svg class="online-text-icon"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="18" height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6"/>
                                <path d="M8 13h8"/>
                                <path d="M8 17h8"/>
                            </svg>

                            <input type="checkbox"
                                id="online_text_notes_check"
                                name="itemOnlineText[show_notes]"
                                value="y"
                                <?= ($itemOnlineText['show_notes'] ?? '') == 'y'
                                    ? 'checked="checked"' : '' ?>>

                            <label for="online_text_notes_check">
                                Notes
                            </label>

                            <input type="text"
                                name="itemOnlineText[notes]"
                                id="online_text_notes"
                                value="<?= htmlspecialchars(
                                    $itemOnlineText['notes'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">
                        </div>

                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <script>
        function updateCountryFlag() {
            var select = document.getElementById('online_text_country');
            var flag = document.getElementById('onlineTextCountryFlag');

            if (!select || !flag) {
                return;
            }

            var selectedOption = select.options[select.selectedIndex];
            var countryCode = selectedOption.value.toLowerCase();
            var countryName = selectedOption.text.trim();

            if (countryCode && countryCode.length === 2) {
                flag.className = 'flagicon-' + countryCode;
                flag.title = countryName + ' - ' + countryCode.toUpperCase();
                flag.style.display = 'inline-block';
            } else {
                flag.style.display = 'none';
            }
        }

        // Show the selected country's flag on initial page load.
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('online_text_country');
            console.log(select);
            if (select) {
                updateCountryFlag();
            }
        });
        $('#online_text_country').on('change', function () {
            updateCountryFlag();
        });
    </script>
</div>
