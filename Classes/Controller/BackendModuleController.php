<?php
namespace RKW\RkwPdf2content\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseFactoryInterface;
use RKW\RkwPdf2content\Service\PageTreeService;
use RKW\RkwPdf2content\Service\PdfService;
use RKW\RkwPdf2content\Service\RecordCreationService;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\FormProtection\Exception;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class BackendModuleController
 *
 * @author Birger Stöckelmann <stoeckelmann@bergisch-media.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwPdf2Content
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class BackendModuleController extends ActionController
{

	/**
	 * @var \RKW\RkwPdf2content\Service\PageTreeService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected ?PageTreeService $pageTreeService = null;


	/**
	 * @var \RKW\RkwPdf2content\Service\RecordCreationService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected ?RecordCreationService $recordCreationService = null;


	/**
	 * @var \RKW\RkwPdf2content\Service\PdfService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected ?PdfService $pdfService = null;


	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected ?PageRenderer $pageRenderer = null;


	/**
	 * @var \TYPO3\CMS\Backend\Routing\UriBuilder
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected ?UriBuilder $uriBuilderBackend = null;

    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?ResponseFactoryInterface $responseFactory = null;


    /**
     * @param \RKW\RkwPdf2content\Service\PageTreeService $pageTreeService
     */
    public function injectPageTreeService(PageTreeService $pageTreeService)
    {
        $this->pageTreeService = $pageTreeService;
    }

    /**
     * @param \RKW\RkwPdf2content\Service\RecordCreationService $recordCreationService
     */
    public function injectRecordCreationService(RecordCreationService $recordCreationService)
    {
        $this->recordCreationService = $recordCreationService;
    }

    /**
     * @param \RKW\RkwPdf2content\Service\PdfService $pdfService
     */
    public function injectPdfService(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
     */
    public function injectMailRepository(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @param \TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilderBackend
     */
    public function injectUriBuilder(UriBuilder $uriBuilderBackend)
    {
        $this->uriBuilderBackend = $uriBuilderBackend;
    }

    /**
     * @param \Psr\Http\Message\ResponseFactoryInterface $responseFactory
     */
    public function injectResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


	public function initializeAction()
    {
		// add inline translations for javascript labels and messages
		$this->pageRenderer->addInlineLanguageLabelFile(
            'EXT:rkw_pdf2content/Resources/Private/Language/locallang.xlf',
            'be.js'
        );
	}


    /**
     * Display the initial view of editor and site chooser
     *
     * @return void
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
	public function indexAction(): void
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
     *
     * @param array|object $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function processPdfAjax($params = []): \Psr\Http\Message\ResponseInterface
    {

        $ajaxObj = $this->responseFactory->createResponse();

        $ajaxObj->withHeader('Content-Type', 'application/json; charset=utf-8');

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
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

            /* @var $configManager \TYPO3\CMS\Extbase\Configuration\ConfigurationManager */
            $configManager = $objectManager->get(ConfigurationManager::class);
            $settings = $configManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'rkwPdf2content'
            );

            /* @var $pdfService \RKW\RkwPdf2content\Service\PdfService */
            $pdfService = GeneralUtility::makeInstance(PdfService::class);
            $pdfService->setSettings($settings);
            $dom = $pdfService->parsePdf($tmpFile['tmp_name']);

            $ajaxObj->getBody()->write(json_encode($dom));
            //$ajaxObj->setContent(array('error' => FALSE, 'dom' => $dom));
        }
        return $ajaxObj;
    }


    /**
     * Renders the given json to pages
     *
     * @param int $targetPageId
     * @param string $firstPageTitle
     * @param string $treePayload
     * @return void
     * @throws \TYPO3\CMS\Core\Error\Exception
     */
	public function renderAction(int $targetPageId = 0, string $firstPageTitle = '', string $treePayload = ''): void
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
