var PDF2Content = PDF2Content || {};

PDF2Content.BackendModule = (function ($) {

    var _ajaxProcessPdfUrl = TYPO3.settings.ajaxUrls['bm_pdf2content_mod1::processPdf'];

    var _renderForm = '#render_form';
    var _upload_form = '#upload_form';
    var _pdfFile = null;

    var _pdfInitialized = false;

    /**
     * Helper - show error message
     * @param message
     * @private
     */
    var _errorMessage = function (message) {
        TYPO3.Notification.showMessage(
            TYPO3.l10n.localize('be.js.label.error'),
            message,
            TYPO3.Severity.error,
            5
        );
    };

    /**
     * Validate the given file in the upload field
     * @param file
     * @returns {boolean}
     * @private
     */
    var _validateFile = function (file) {
        // on server site now
        return true;
    };

    /**
     * Validate pre render submit parameters like processed pdf, pdf tree and target page
     * @returns {boolean}
     * @private
     */
    var _validateRenderSubmit = function () {

        // target page set?
        if ($('input.target_page_id', _renderForm).val() == '') {
            _errorMessage(TYPO3.l10n.localize('be.js.msg.error_target_page'));
            return false;
        }

        // target page title set?
        if ($('input.first_page_title', _renderForm).val() == '') {
            _errorMessage(TYPO3.l10n.localize('be.js.msg.error_page_title'));
            return false;
        }

        // pdf initialized?
        if (_pdfInitialized !== true) {
            _errorMessage(TYPO3.l10n.localize('be.js.msg.error_pdf_not_init'));
            return false;
        }

        // tree set?
        if ($('input.tree_payload', _renderForm).val() == '[]') {
            _errorMessage(TYPO3.l10n.localize('be.js.msg.error_empty_doc'));
            return false;
        }

        return true;

    };

    var _initPDF = function () {

        $('.overlay', '.editor').removeClass('hidden');

        _pdfInitialized = false;

        // create form data
        var data = new FormData();
        data.append('pdffile', _pdfFile);
        $.ajax({
            url: _ajaxProcessPdfUrl,
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (result) {
                $('.overlay', '.editor').addClass('hidden');

                // all ok, begin pdf work...
                if (result.error === false) {

                    $('.editor_dom').html('').append(result.dom);
                    $('.pdf_upload').fadeOut();
                    _pdfInitialized = true;

                } else {
                    _errorMessage(TYPO3.l10n.localize('be.js.msg.error_process_begin') + ': ' + result.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.overlay', '.editor').addClass('hidden');
                _errorMessage(TYPO3.l10n.localize('be.js.msg.error_ajax_request') + ' ' + textStatus + ' ' + errorThrown);
            }
        });

    };

    /**
     * Init the event handlers
     * @private
     */
    var _initEventHandler = function () {

        // set pdf in file upload
        $('.file_pdf_upload', _upload_form).on('change', function (evt) {
            // check inserted file
            var file = evt.target.files[0];

            // validate
            if (_validateFile(file) === true) {
                _pdfFile = file;
                $('.submit_upload', _upload_form).removeAttr('disabled');
            } else {
                _pdfFile = null;
                $(this).val('');
                $('.submit_upload', _upload_form).attr('disabled', 'disabled');
                _errorMessage(TYPO3.l10n.localize('be.js.msg.error_pdf_file'));
            }
        });

        // submit to render
        $('.submit_render', _renderForm).on('click', function (evt) {

            evt.stopPropagation();
            evt.preventDefault();

            if (_validateRenderSubmit() === true) {
                TYPO3.Dialog.QuestionDialog({
                    title: 'Process PDF',
                    msg: TYPO3.l10n.localize('be.js.msg.confirm_render'),
                    width: 600,
                    url: _ajaxProcessPdfUrl,
                    fn: function (button) {
                        if (button == 'yes') {
                            jQuery('#render_form').submit();
                        }
                    }
                });
            }
        });

        // upload and process pdf file
        $('.submit_upload').on('click', function (evt) {

            evt.stopPropagation();
            evt.preventDefault();

            TYPO3.Dialog.QuestionDialog({
                title: 'Init PDF',
                msg: TYPO3.l10n.localize('be.js.msg.confirm_init'),
                width: 600,
                url: _ajaxProcessPdfUrl,
                fn: function (button) {
                    if (button == 'yes') {
                        _initPDF();
                    }
                }
            });

        });

        /**
         * Opens the page tree browser window
         */
        $('.open_browser').on('click', function (evt) {
            evt.preventDefault();

            var w = 800;
            var h = 650;
            var top = (window.outerHeight / 2) - (h / 2);
            var left = (window.outerWidth / 2) - (w / 2);
            if(elementBrowserURL !== "" || elementBrowserURL !== null) {
                var popw = window.open(elementBrowserURL, 'TYPO3ElementBrowser', 'height=' + h + ',width=' + w + ',status=0,menubar=0,resizable=1,scrollbars=1,top=' + top + ',left=' + left);
                popw.focus();
            }
        });

    };

    var _setTargetPage = function (targetPageId, targetPageTitle) {
        $('.target_page_id', _renderForm).val(targetPageId);
        $('.target_page_title', _renderForm).text(targetPageTitle);
    };

    /**
     * Bootstrap function
     * @private
     */
    var _init = function () {
        // dom ready inits
        $(function () {
            // disable submit buttons
            $('.submit_upload', _upload_form).attr('disabled', 'disabled');
            // init event handler
            _initEventHandler();
        });
    };

    /**
     * Interface
     * @public
     */
    return {
        init: _init,
        setTargetPage: _setTargetPage
    };

})
(jQuery);

// init backend module js
PDF2Content.BackendModule.init();
// init angular app
angular.element(document).ready(function () {
    angular.bootstrap(document, ['Pdf2Content']);
});
