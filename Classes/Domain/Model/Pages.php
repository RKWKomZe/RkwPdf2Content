<?php
namespace RKW\RkwPdf2content\Domain\Model;

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

/**
 * Class Pages
 *
 * @package RKW_Pdf2Content
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Pages extends \RKW\RkwBasics\Domain\Model\Pages
{
    /**
     * pubdate
     *
     * @var integer
     * @deprecated
     */
    protected $pubDate;


    /**
     * txBmpdf2contentIsImport
     *
     * @var \integer
     * @deprecated Use txRkwpdf2contentIsImport instead
     * @toDo: Remove. Can be removed, if this field is not longer used by any other extension
     */
    protected $txBmpdf2contentIsImport;


    /**
     * txBmpdf2contentIsImportSub
     *
     * @var \integer
     * @deprecated Use txRkwpdf2contentIsImport instead
     * @toDo: Remove. Can be removed, if this field is not longer used by any other extension
     */
    protected $txBmpdf2contentIsImportSub;

    /**
     * txRkwpdf2contentIsImport
     *
     * @var \integer
     *
     */
    protected $txRkwpdf2contentIsImport;

    /**
     * txRkwpdf2contentIsImportSub
     *
     * @var \integer
     */
    protected $txRkwpdf2contentIsImportSub;


    /**
     * Returns the pubDate
     *
     * @return integer
     * @deprecated
     */
    public function getPubDate() {
        \TYPO3\CMS\Core\Utility\GeneralUtility::deprecationLog(__CLASS__ . ':' . __METHOD__ . ' will be removed soon. Use $this->getLastUpdated instead.');
        return $this->pubDate;
    }


    /**
     * Sets the pubDate
     *
     * @param integer $pubDate
     * @return void
     * @deprecated
     */
    public function setPubdate($pubDate) {
        \TYPO3\CMS\Core\Utility\GeneralUtility::deprecationLog(__CLASS__ . ':' . __METHOD__ . ' will be removed soon. Use $this->getLastUpdated instead.');
        $this->pubDate = $pubDate;
    }


    /**
     * Returns the txBmpdf2contentIsImport
     *
     * @return \string txBmpdf2contentIsImport
     */
    public function getBmpdf2contentIsImport() {
        return $this->txBmpdf2contentIsImport;
    }

    /**
     * Sets the txBmpdf2contentEtrackerIsImport
     * Hint: Migration support - set both. Old and new
     *
     * @param \string $txBmpdf2contentIsImport
     */
    public function setTxBmpdf2contentIsImport($txBmpdf2contentIsImport) {
        $this->txBmpdf2contentIsImport = $txBmpdf2contentIsImport;
        $this->txRkwpdf2contentIsImport = $txBmpdf2contentIsImport;
    }

    /**
     * Returns the txBmpdf2contentIsImportSub
     *
     * @return \string txBmpdf2contentIsImportSub
     */
    public function getBmpdf2contentIsImportSub() {
        return $this->txBmpdf2contentIsImportSub;
    }

    /**
     * Sets the txBmpdf2contentIsImportSub
     * Hint: Migration support - set both. Old and new
     *
     * @param \string $txBmpdf2contentIsImportSub
     */
    public function setTxBmpdf2contentIsImportSub($txBmpdf2contentIsImportSub) {
        $this->txBmpdf2contentIsImportSub = $txBmpdf2contentIsImportSub;
        $this->txRkwpdf2contentIsImportSub = $txBmpdf2contentIsImportSub;
    }

    /**
     * Returns the txRkwpdf2contentIsImport
     *
     * @return \string txRkwpdf2contentIsImport
     */
    public function getRkwpdf2contentIsImport() {
        return $this->txRkwpdf2contentIsImport;
    }

    /**
     * Sets the txRkwpdf2contentEtrackerIsImport
     * Hint: Migration support - set both. Old and new
     *
     * @param \string $txRkwpdf2contentIsImport
     */
    public function setTxRkwpdf2contentIsImport($txRkwpdf2contentIsImport) {
        $this->txRkwpdf2contentIsImport = $txRkwpdf2contentIsImport;
        $this->txBmpdf2contentIsImport = $txRkwpdf2contentIsImport;
    }

    /**
     * Returns the txRkwpdf2contentIsImportSub
     */
    public function getRkwpdf2contentIsImportSub() {
        return $this->txRkwpdf2contentIsImportSub;
    }

    /**
     * Sets the txRkwpdf2contentIsImportSub
     * Hint: Migration support - set both. Old and new
     *
     * @param \string $txRkwpdf2contentIsImportSub
     * @return \string txRkwpdf2contentIsImportSub
     */
    public function setTxRkwpdf2contentIsImportSub($txRkwpdf2contentIsImportSub) {
        $this->txRkwpdf2contentIsImportSub = $txRkwpdf2contentIsImportSub;
        $this->txBmpdf2contentIsImportSub = $txRkwpdf2contentIsImportSub;
    }

}
