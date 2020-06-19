
define([
       'jquery',
       'TYPO3/CMS/Backend/Modal',
       'TYPO3/CMS/Backend/Severity'
   ], function($, Modal, Severity) {

    var RkwPdf2contentMod1 = {};

    var _ajaxProcessPdfUrl = TYPO3.settings.ajaxUrls['rkw_pdf2content_mod1::processPdf'];
    var _renderForm = '#render_form';
    var _upload_form = '#upload_form';
    var _pdfFile = null;

    var _pdfInitialized = false;

    RkwPdf2contentMod1.init = function() {
        // do init stuff
        angular.element(document).ready(function () {
            angular.bootstrap(document, ['Pdf2Content']);
        });

    };

    /**
     * Helper - show error message
     * @param message
     * @private
     */
    var _errorMessage = function (message) {

        var configuration = {
            title: TYPO3.l10n.localize('be.js.label.error'),
            content: message,
            severity: Severity.error,
            //style: Modal.styles.dark,
            buttons: [
                {
                    text: 'Ok',
                    active: true,
                    btnClass: 'btn-secondary',
                    trigger: function() {
                        Modal.currentModal.trigger('modal-dismiss');
                    }
                }
            ]
        };
        Modal.advanced(configuration);
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
                   if (result) {

                       $('.editor_dom').html('').append(result);
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

    // upload and process pdf file
    $('.submit_upload').on('click', function (evt) {

        evt.stopPropagation();
        evt.preventDefault();

        var configuration = {
            title: 'Init PDF',
            content: TYPO3.l10n.localize('be.js.msg.confirm_init'),
            severity: Severity.info,
            buttons: [
                {
                    text: 'Ja',
                    active: true,
                    btnClass: 'btn-primary',
                    trigger: function() {
                        Modal.currentModal.trigger('modal-dismiss');
                        _initPDF();
                    }
                },
                {
                    text: 'Nein',
                    active: true,
                    btnClass: 'btn-secondary',
                    trigger: function() {
                        Modal.currentModal.trigger('modal-dismiss');
                    }
                }
            ]
        };
        Modal.advanced(configuration);
    });

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

            var configuration = {
                title: 'Process PDF',
                content: TYPO3.l10n.localize('be.js.msg.confirm_render'),
                severity: Severity.info,
                url: _ajaxProcessPdfUrl,
                buttons: [
                    {
                        text: 'Ja',
                        active: true,
                        btnClass: 'btn-primary',
                        trigger: function() {
                            Modal.currentModal.trigger('modal-dismiss');
                            $('#render_form').submit();
                        }
                    }
                ]
            };
            Modal.advanced(configuration);
        }
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


    // To let the module be a dependency of another module, we return our object
    return RkwPdf2contentMod1;
});