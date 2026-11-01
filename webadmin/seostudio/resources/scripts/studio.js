'use strict';

var $window = $(window);
var $document = $(document);

/**
 * Initializes the navigation button on small screens.
 */
function initNavButton() {
    var button = $('header .toggle');
    var nav = $('header nav');

    button.on('click', function() {
        if (nav.hasClass('visible')) {
            nav.stop().slideUp(200);
        }
        else {
            nav.stop().slideDown(200);
        }

        nav.toggleClass('visible');
    });
}

/**
 * Initializes the website switcher dropdown component.
 */
function initWebsiteSwitcher() {
    var selectElement = $('.site-selector .select');
    var dropDownElement = selectElement.find('.dropdown');
    var dropDownLink = dropDownElement.find('li a');
    var dropDownEnabled = false;
    var isSwitching = false;

    selectElement.on('mousedown', function(event) {
        event.preventDefault();
        event.stopPropagation();
    });

    selectElement.on('click', function(event) {
        if (dropDownEnabled) {
            dropDownElement.stop().slideUp('fast');
        }
        else {
            dropDownElement.stop().slideDown('fast');
        }

        dropDownEnabled = !dropDownEnabled;
        event.stopPropagation();
    });

    dropDownLink.on('click', function(e) {
        if (isSwitching) {
            e.preventDefault();
            return;
        }

        isSwitching = true;
        dropDownLink.addClass('working');
        e.stopPropagation();
    });
}

/**
 * Hides errors after a short time after the page finishes loading.
 */
function initErrors() {
    var errors = $('.error');

    if (errors.length > 0) {
        $(function() {
            setTimeout(function() {
                errors.fadeOut();
            }, 6000);
        });
    }
}

/**
 * Initializes sticky table headers and their scroll to top buttons.
 */
function initStickyTables() {
    var tables = $('table[data-sticky]');
    var stickyId = '';

    $window.on('scroll', function() {
        tables.each(function() {
            var table = tables.filter(this);
            var id = table.data('sticky');

            if ($document.scrollTop() > table.offset().top) {
                if (stickyId != id) {
                    stickyId = id;

                    // @nocache – rarely triggered, minimal perf hit
                    $('#' + stickyId).show();
                }
            }

            // @nocache – rarely triggered, minimal perf hit
            else if (stickyId == id) {
                $("#" + stickyId).hide();
                stickyId = "";
            }
        });
    });

    // Scroll to top buttons
    $('.sticky .up').on('click', function() {
        $('html, body').animate({ scrollTop: '0' }, 'fast');
    });
}

/**
 * Adds spinners when submit buttons are clicked.
 */
function initSubmitLoaders() {
    var formSubmitButtons = $('.form-container input[type=submit]');

    formSubmitButtons.on('click', function() {
        setTimeout(function() {
            var button = formSubmitButtons.filter(this);
            button.parent().html(`<img src="${path}resources/images/load32.gif" alt="Loading" width="16px" />`);
        }, 20);
    });
}

/**
 * Adds spinners to tool icons when clicked.
 */
function initToolIconAnimations() {
    var toolButtons = $('.category .tool');

    toolButtons.on('click', function(e) {
        var button = toolButtons.filter(this);

        if (button.hasClass('is-loading')) {
            e.preventDefault();
            return;
        }

        button.addClass('is-loading');
        button.find('.tool-loader').show().html('<img src="resources/images/b-load32.gif" />');
    });

    function clearToolIconAnimations() {
        toolButtons.removeClass('is-loading');
        toolButtons.find('.tool-loader').hide();
    }

    $window.on('pageshow', function(e) {
        if (e.originalEvent.persisted) {
            clearToolIconAnimations();
        }
    });

    clearToolIconAnimations();
}

/**
 * Adds an apply button to the site selector.
 */
function initApplyButtons() {
    $('.site-selector').each(function() {
        var selector = $(this);
        var apply = selector.find('.apply');
        var textbox = selector.find('[name=site]');

        textbox.on('change keyup', function() {
            apply.removeClass('hidden');
        });
    });
}

/**
 * Implements the language dropdown menu.
 */
function initLanguageBar() {
    var dropdowns = $('.language-bar .dropdown');
    var languageMenu = $('.language-menu');

    dropdowns.on('click', function() {
        var dropdown = dropdowns.filter(this);

        if (dropdown.hasClass('active')) {
            dropdown.removeClass('active');
            languageMenu.hide();
        }
        else {
            dropdown.addClass('active');
            languageMenu.show();
        }
    });
}

/**
 * Implements the submit button on the "Submit Sitemaps" tool.
 */
function initSitemapSubmit() {
    if (typeof submitSitemaps != 'undefined') {
        var submitButton = $('.submit');

        submitButton.on('click', function() {
            var td = submitButton.parent();
            var id = submitButton.data('id');
            var sec = submitButton.data('sec');

            td.html(`<img src="${path}resources/images/load32.gif" alt="Loading" width="16px" />`);

            $.get(window.location + `&sitemap=${id}&sec=${sec}`, function(data) {
                td.html(`<img src="${path}resources/images/check32.png" alt="Submitted" width="16px" />`);
            });
        });
    }
}

/**
 * Implements spinners when buttons are clicked.
 */
function initLoadableButtons() {
    var loadables = $('.loadable');
    var loaderElement = document.getElementById('loader');

    if (!loaderElement && loadables.length > 0) {
        console.error('Missing #loader element');
        return;
    }

    $.each(loadables, function() {
        var button = loadables.filter(this);

        if (button.is('button') || button.is('a')) {
            button.on('click', function(e) {
                if (button.hasClass('triggered')) {
                    e.preventDefault();
                    return;
                }

                button.addClass('triggered');
                button.prepend(loaderElement.innerHTML);
            });
        }
        else if (button.is('form')) {
            var form = button;
            var submitButton = form.find('button.loadable');

            form.on('submit', function(e) {
                if (submitButton.length == 0) {
                    if (form.hasClass('triggered')) {
                        e.preventDefault();
                        return;
                    }

                    form.addClass('triggered');
                }
            });
        }
        else {
            console.error('Element', button[0], 'is marked as loadable but is not a button or link');
        }
    });
}

/**
 * Implements table action buttons (such as copy and download).
 */
function initTableActions() {
    var actionFlexElements = $('.table-actions');

    $.each(actionFlexElements, function() {
        var flexElement = actionFlexElements.filter(this);
        var selector = flexElement.data('target');
        var target = $(selector);

        if (!target.length) {
            return;
        }

        var buttons = flexElement.find('button.action-button');

        $.each(buttons, function() {
            var button = buttons.filter(this);
            var actionName = button.data('action');

            button.on('click', function() {
                if (!button.hasClass('action-success')) {
                    if (runTableAction(target, actionName)) {
                        button.addClass('action-success');

                        setTimeout(function() {
                            button.removeClass('action-success');
                        }, 2000);
                    }
                }
            });
        });
    });
}

/**
 * Invokes a table action.
 *
 * @param {HTMLTableElement} table
 * @param {string} action
 */
function runTableAction(table, action) {
    var data = getTableData(table);

    switch (action) {
        case 'copy':
            if (typeof window.navigator.clipboard != 'undefined') {
                data.shift();
                window.navigator.clipboard.writeText(createCSV(data));
                return true;
            }
            else {
                alert(
                    'Failed to copy: ' +
                    'This is likely because you are viewing these results inside a frame, and the parent website has ' +
                    'not granted us permission to access the clipboard.'
                );
            }

            break;
        case 'download':
            var bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
            var blob = new Blob([bom, createCSV(data)], { type: 'text/csv; charset=utf8' });
            var url = window.URL.createObjectURL(blob);
            var anchor = document.createElement('a');
            var fileName = getFileName(table);

            anchor.style.display = 'none';
            anchor.href = url;
            anchor.download = fileName + '.csv';
            anchor.click();

            window.URL.revokeObjectURL(url);
            return true;
    }
}

/**
 * Builds a file name for a download.
 *
 * @param {HTMLTableElement} table
 */
function getFileName(table) {
    var name = table.data('name') || '%tool';

    name = name.replace('%tool', (tool_name || 'results').toLowerCase());
    name = name.replace(/&quot;/g, '"');
    name = name.replace(/&#39;/g, '\'');
    name = name.replace(/(&([a-z0-9]|#[0-9]+)+;)+/g, '-');
    name = name.replace(/[ \\/:"*?<>|]+/g, '-');
    name = name.replace(/-{2,}/g, '-');
    name = name.replace(/^-+/, '');
    name = name.replace(/-+$/, '');

    if (site) {
        name = site + '-' + name;
    }

    return name;
}

/**
 * Builds a data array from the given table element.
 *
 * @param {HTMLTableElement} table
 */
function getTableData(table) {
    var columnElements = table.find('thead td, thead th');
    var rowElements = table.find('tbody tr');

    var columns = [];
    var columnsIgnored = [];
    var rows = [columns];

    $.each(columnElements, function(index) {
        var element = columnElements.filter(this);
        var text = element.text().trim();

        if (element.data('ignore')) {
            columnsIgnored.push(index);
            return;
        }

        if (element.data('column')) {
            text = element.data('column');
        }

        if (element.data('split')) {
            var titles = element.data('split').split(',');

            for (var i = 0; i < titles.length; i++) {
                columns.push(titles[i].trim());
            }
        }
        else {
            columns.push(text);
        }
    });

    $.each(rowElements, function() {
        var rowElement = rowElements.filter(this);
        var columnElements = rowElement.find('th, td');
        var values = [];

        $.each(columnElements, function(index) {
            var columnElement = columnElements.filter(this);
            var value = columnElement.text().trim();

            if (columnsIgnored.indexOf(index) >= 0) {
                return;
            }

            if (columnElement.attr('data-value')) {
                value = columnElement.attr('data-value');
            }

            if (columnElement.data('split')) {
                var keys = columnElement.data('split').split(',');

                for (var i = 0; i < keys.length; i++) {
                    var keyName = keys[i];
                    var element = columnElement.find('[data-key="' + keyName + '"]');
                    var value = '';

                    if (element.length > 0) {
                        value = element.text().trim();

                        if (element.attr('data-value')) {
                            value = element.attr('data-value');
                        }
                    }

                    values.push(value);
                }
            }
            else if (columnElement.data('split-values')) {
                var split = columnElement.data('split-values').split(',');

                for (var i = 0; i < split.length; i++) {
                    values.push(split[i].trim());
                }
            }
            else {
                values.push(value);
            }
        });


        if (values.length > 0) {
            rows.push(values);
        }
    });

    return rows;
}

/**
 * Converts the given data array into a CSV string.
 */
function createCSV(data) {
    return data.map(function(row) {
        return row.map(function(value) {
            if (value.indexOf('"') >= 0 || value.indexOf(",") >= 0|| value.indexOf("\n") >= 0 || value.indexOf("\r") >= 0) {
                return '"' + value.replace(/"/g, '""') + '"';
            }

            return value;
        }).join(',');
    }).join('\n');
}

function initSparklines() {
    if (typeof renderSparklines != 'undefined') {
        $('.sparklines').sparkline('html', {
            type: 'bar',
            tooltipFormat: '<span class="date">{{offset:names}}</span><span class="value">{{value}}</span>',
            tooltipValueLookups: {
                names: sparklineKeys
            }
        });
    }
}

// Call initializers
initNavButton();
initWebsiteSwitcher();
initErrors();
initStickyTables();
initSubmitLoaders();
initToolIconAnimations();
initApplyButtons();
initSitemapSubmit();
initLanguageBar();
initLoadableButtons();
initTableActions();
initSparklines();
