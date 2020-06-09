<?php

namespace BM\BmPdf2content\Controller;

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
 * @package BM_PDF2Content
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BackendModuleController extends ActionController {

	/**
	 * @var \BM\BmPdf2content\Service\PageTreeService
	 * @inject
	 */
	protected $pageTreeService;

	/**
	 * @var \BM\BmPdf2content\Service\RecordCreationService
	 * @inject
	 */
	protected $recordCreationService;

	/**
	 * @var \BM\BmPdf2content\Service\PdfService
	 * @inject
	 */
	protected $pdfService;

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @inject
	 */
	protected $pageRenderer;

	/**
	 * @var \TYPO3\CMS\Backend\Routing\UriBuilder
	 * @inject
	 */
	protected $uriBuilderBackend;

	public function initializeAction() {
		// add inline translations for javascript labels and messages
		$this->pageRenderer->addInlineLanguageLabelFile('EXT:bm_pdf2content/Resources/Private/Language/locallang.xlf', 'be.js');
	}

	/**
	 * Display the initial view of editor and site chooser
	 */
	public function indexAction() {

		// get url to element browser
		$uri = $this->uriBuilderBackend->buildUriFromRoute('wizard_element_browser');
		$uriString = $uri->getPath().'?'.$uri->getQuery().'&mode=db&bparams=targetPageId|||pages';

		$this->pageRenderer->addJsInlineCode('ElementBrowserURL','
			var elementBrowserURL = "'.$uriString.'";
		'
		);

		$this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Notification');

		// add notifications and dialog js file
		$this->pageRenderer->addJsFile('sysext/backend/Resources/Public/JavaScript/notifications.js', $type='text/javascript', FALSE, FALSE, '', TRUE);

//		$this->pageRenderer->addJsFile('../typo3conf/ext/bm_pdf2content/Resources/Public/Scripts/oldieshim.js');
		$this->pageRenderer->addJsFile('../typo3conf/ext/bm_pdf2content/Resources/Public/Scripts/vendor.js');
		$this->pageRenderer->addJsFile('../typo3conf/ext/bm_pdf2content/Resources/Public/Scripts/scripts.js');
		$this->pageRenderer->addJsFile('../typo3conf/ext/bm_pdf2content/Resources/Public/Scripts/backendmodule.js');

		$this->pageRenderer->addCssFile('../typo3conf/ext/bm_pdf2content/Resources/Public/Styles/backendmodule.css');
		$this->pageRenderer->addCssFile('../typo3conf/ext/bm_pdf2content/Resources/Public/Styles/vendor.css');
		$this->pageRenderer->addCssFile('../typo3conf/ext/bm_pdf2content/Resources/Public/Styles/main.css');


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
	 * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
	 */
	public function processPdfAjax($params = array(), \TYPO3\CMS\Core\Http\AjaxRequestHandler &$ajaxObj = NULL) {

		$ajaxObj->setContentFormat('jsonbody');

		$tmpFile = $_FILES['pdffile'];

		// not a valid pdf?
		if (!(is_array($tmpFile) && $tmpFile['type'] == 'application/pdf' || file_exists($tmpFile['tmp_name']))) {
			$ajaxObj->setContent(array(
				'error' => TRUE,
				'message' => 'Not a valid PDF!'
			));
		}
		else {

			try {

				// get typoscript settings for this extension
				/* @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
				$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
				/* @var $configManager \TYPO3\CMS\Extbase\Configuration\ConfigurationManager */
				$configManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
				$settings = $configManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, $this->extensionName);

				/* @var $pdfService \BM\BmPdf2content\Service\PdfService */
				$pdfService = GeneralUtility::makeInstance('BM\\BmPdf2content\\Service\\PdfService');
				$pdfService->setSettings($settings);
				$dom = $pdfService->parsePdf($tmpFile['tmp_name']);

				$ajaxObj->setContent(array('error' => FALSE, 'dom' => $dom));


			} catch (Exception $e) {
				$ajaxObj->setContent(array(
					'error' => TRUE,
					'message' => $e->getMessage()
				));
			}

		}

	}

	/**
	 * Renders the given json to pages
	 * @param integer $targetPageId
	 * @param string $firstPageTitle
	 * @param string $treePayload
	 */
	public function renderAction($targetPageId = 0, $firstPageTitle = '', $treePayload) {

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