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

use RKW\RkwPdf2content\Domain\Repository\PagesRepository;
use RKW\RkwPdf2content\Service\PageTreeService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class BackendModuleController
 *
 * @author Birger Stöckelmann <stoeckelmann@bergisch-media.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwPdf2Content
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class DisplayPagesController extends ActionController
{

    /**
     * @var \RKW\RkwPdf2content\Service\PageTreeService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected PageTreeService $pageTreeService;


    /**
     * PagesRepository
     *
     * @var  \RKW\RkwPdf2content\Domain\Repository\PagesRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?PagesRepository $pagesRepository;


    /**
     * action boxes
     *
     * @return void
     */
    public function importParentPageAction()
    {
        /** @var \TYPO3\CMS\Core\Utility\RootlineUtility $rootLineUtility */
        $rootLineUtility = GeneralUtility::makeInstance(RootlineUtility::class, $GLOBALS['TSFE']->id);
        $rootlinePages = $rootLineUtility->get();

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
