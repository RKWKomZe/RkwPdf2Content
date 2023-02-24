<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * Add ajax handler for backend module upload
 * @see https://docs.typo3.org/c/typo3/cms-core/master/en-us/Changelog/7.6/Breaking-69916-RegisteredAJAXHandlersReplacedByRoutes.html
 * @see https://scripting-base.de/blog/ajax-with-psr-7.html
 */
return [
    'rkw_pdf2content_mod1::processPdf' => [
        //'path' => '/rkw_pdf2content/processpdf',
        'target' => \RKW\RkwPdf2content\Controller\BackendModuleController::class . '::processPdfAjax'
    ],
];
