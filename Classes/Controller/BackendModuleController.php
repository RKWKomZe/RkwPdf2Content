<?php

namespace RKW\RkwPdf2content\Controller;

use TYPO3\CMS\Core\FormProtection\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Birger Stöckelmann <stoeckelmann@bergisch-media.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package RKW_Pdf2Content
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BackendModuleController extends ActionController
{

	/**
	 * @var \RKW\RkwPdf2content\Service\PageTreeService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $pageTreeService;

	/**
	 * @var \RKW\RkwPdf2content\Service\RecordCreationService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $recordCreationService;

	/**
	 * @var \RKW\RkwPdf2content\Service\PdfService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $pdfService;

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $pageRenderer;

	/**
	 * @var \TYPO3\CMS\Backend\Routing\UriBuilder
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $uriBuilderBackend;

	public function initializeAction()
    {
		// add inline translations for javascript labels and messages
		$this->pageRenderer->addInlineLanguageLabelFile('EXT:rkw_pdf2content/Resources/Private/Language/locallang.xlf', 'be.js');
	}

	/**
	 * Display the initial view of editor and site chooser
	 */
	public function indexAction()
    {
		// get url to element browser
		$uri = $this->uriBuilderBackend->buildUriFromRoute('wizard_element_browser');
		$uriString = $uri->getPath().'?'.$uri->getQuery().'&mode=db&bparams=targetPageId|||pages';

		$this->pageRenderer->addJsInlineCode('ElementBrowserURL','
			var elementBrowserURL = "'.$uriString.'";
		'
		);

		$this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Notification');

        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/RkwPdf2content/VendorMod1');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/RkwPdf2content/ScriptsMod1');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/RkwPdf2content/RkwPdf2contentMod1');

		$this->pageRenderer->addCssFile('../typo3conf/ext/rkw_pdf2content/Resources/Public/Styles/backendmodule.css');
		$this->pageRenderer->addCssFile('../typo3conf/ext/rkw_pdf2content/Resources/Public/Styles/vendor.css');
		$this->pageRenderer->addCssFile('../typo3conf/ext/rkw_pdf2content/Resources/Public/Styles/main.css');


		$this->pageRenderer->addJsInlineCode('SelectFromPageBrowser', '
			var browserWin="";
			function setFormValueFromBrowseWin(fName,value,label,exclusiveValues){
				value = value.replace(\'pages_\',\'\');
				$(\'#target_page_title\').text(label);
				$(\'#\'+fName).val(value);
			}');


	}

    /**
     * Processes the pdf and returns the html dom of the pdf
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\Response $ajaxObj
     * @return \TYPO3\CMS\Core\Http\Response
     */
    public function processPdfAjax($params = [], \TYPO3\CMS\Core\Http\Response $ajaxObj = NULL)
    {
        $ajaxObj->withHeader('Content-Type', 'application/json; charset=utf-8');
        //$ajaxObj->setContentFormat('jsonbody');

        $tmpFile = $_FILES['pdffile'];

        // not a valid pdf?
        if (!(is_array($tmpFile) && $tmpFile['type'] == 'application/pdf' || file_exists($tmpFile['tmp_name']))) {

            $message = array(
                'error' => TRUE,
                'message' => 'Not a valid PDF!'
            );

            $ajaxObj->getBody()->write(json_encode($message));

            /*
            $ajaxObj->setContent(array(
				'error' => TRUE,
				'message' => 'Not a valid PDF!'
			));
            */
        }
        else {

            // get typoscript settings for this extension
            /* @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
            $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            /* @var $configManager \TYPO3\CMS\Extbase\Configuration\ConfigurationManager */
            $configManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
            $settings = $configManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, $this->extensionName);
            /* @var $pdfService \RKW\RkwPdf2content\Service\PdfService */
            $pdfService = GeneralUtility::makeInstance('RKW\\RkwPdf2content\\Service\\PdfService');
            $pdfService->setSettings($settings);
            $dom = $pdfService->parsePdf($tmpFile['tmp_name']);

            $ajaxObj->getBody()->write(json_encode($dom));
            //$ajaxObj->setContent(array('error' => FALSE, 'dom' => $dom));
        }
        return $ajaxObj;
        //===
    }

	/**
	 * Renders the given json to pages
	 * @param integer $targetPageId
	 * @param string $firstPageTitle
	 * @param string $treePayload
	 */
	public function renderAction($targetPageId = 0, $firstPageTitle = '', $treePayload)
    {

		try {

			$this->recordCreationService->setSettings($this->settings);
			$this->recordCreationService->init($treePayload, $targetPageId, $firstPageTitle);
			$this->recordCreationService->createRecords();

		} catch (Exception $e) {
			DebuggerUtility::var_dump($e);
			die();
		}

		$this->view->assign('targetPageId', $targetPageId);
		$this->view->assign('firstPageTitle', $firstPageTitle);

	}

}
