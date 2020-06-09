<?php

namespace BM\BmPdf2content\Controller;

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
 * @package BM_Pdf2Content
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 2 or later
 *
 */
class DisplayPagesController extends ActionController {

	/**
	 * @var \BM\BmPdf2content\Service\PageTreeService
	 * @inject
	 */
	protected $pageTreeService;


	/**
	 * PagesRepository
	 *
	 * @var  \BM\BmPdf2content\Domain\Repository\PagesRepository
	 * @inject
	 */
	protected $pagesRepository = NULL;



	/**
	 * Default action to display PDF pages from general record storage page settings of this plugin
	 */
	public function listAction() {
		$pid = $this->settings['targetPageId'];
		$this->pageTreeService->initFePageTree($pid);
		$this->view->assign('tree', $this->pageTreeService->getFePageTree());
	}


	/**
	 * action boxes
	 *
	 * @return void
	 */
	public function importParentPageAction() {

		// get PageRepository and rootline
		$repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
		$rootlinePages = $repository->getRootLine(intval($GLOBALS['TSFE']->id));

		// go through all pages and take the one that has a match in the corresponsing field
		// but only if the current page IS an import sub page!
		$pid = intval($GLOBALS['TSFE']->id);
		if (
			(isset($rootlinePages[count($rootlinePages)-1]))
			&& (isset($rootlinePages[count($rootlinePages)-1]['tx_bmpdf2content_is_import_sub']))
			&& ($rootlinePages[count($rootlinePages)-1]['tx_bmpdf2content_is_import_sub'] == 1)
		){

			foreach ($rootlinePages as $page => $values) {
				if (
					($values['tx_bmpdf2content_is_import'] == 1)
					&& ($values['tx_bmpdf2content_is_import_sub'] == 0)
				) {
					$pid = intval($values['uid']);
					break;
					//===
				}
			}
		}

		$result = $this->pagesRepository->findByUid($pid);
		if ($result instanceof \BM\BmPdf2content\Domain\Model\Pages)
			$this->view->assign('page', $result);
		if ($this->settings['importParentPage']['showField']) {
			$getter = 'get' . ucFirst($this->settings['importParentPage']['showField']);

			if (method_exists($result, $getter))
				$this->view->assign('field', $result->$getter());

		}

	}




}