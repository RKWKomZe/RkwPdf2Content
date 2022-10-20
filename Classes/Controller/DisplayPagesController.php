<?php

namespace RKW\RkwPdf2content\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
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
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 2 or later
 *
 */
class DisplayPagesController extends ActionController
{

	/**
	 * @var \RKW\RkwPdf2content\Service\PageTreeService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $pageTreeService;


	/**
	 * PagesRepository
	 *
	 * @var  \RKW\RkwPdf2content\Domain\Repository\PagesRepository
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $pagesRepository = NULL;



	/**
	 * Default action to display PDF pages from general record storage page settings of this plugin
	 */
	public function listAction()
    {
		$pid = $this->settings['targetPageId'];
		$this->pageTreeService->initFePageTree($pid);
		$this->view->assign('tree', $this->pageTreeService->getFePageTree());
	}


	/**
	 * action boxes
	 *
	 * @return void
	 */
	public function importParentPageAction()
    {
		// get rootline
        /** @var array $rootlinePages */
        $rootlinePages = GeneralUtility::makeInstance(RootlineUtility::class, intval($GLOBALS['TSFE']->id))->get();

        // go through all pages and take the one that has a match in the corresponsing field
		// but only if the current page IS an import sub page!
		$pid = intval($GLOBALS['TSFE']->id);
		if (
			(isset($rootlinePages[count($rootlinePages)-1]))
			&& (isset($rootlinePages[count($rootlinePages)-1]['tx_rkwpdf2content_is_import_sub']))
			&& ($rootlinePages[count($rootlinePages)-1]['tx_rkwpdf2content_is_import_sub'] == 1)
		){

			foreach ($rootlinePages as $page => $values) {
				if (
					($values['tx_rkwpdf2content_is_import'] == 1)
					&& ($values['tx_rkwpdf2content_is_import_sub'] == 0)
				) {
					$pid = intval($values['uid']);
					break;
					//===
				}
			}
		}

		$result = $this->pagesRepository->findByUid($pid);
		if ($result instanceof \RKW\RkwPdf2content\Domain\Model\Pages)
			$this->view->assign('page', $result);
		if ($this->settings['importParentPage']['showField']) {
			$getter = 'get' . ucfirst($this->settings['importParentPage']['showField']);

			if (method_exists($result, $getter))
				$this->view->assign('field', $result->$getter());

		}

	}

}
