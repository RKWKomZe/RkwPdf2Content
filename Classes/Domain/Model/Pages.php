<?php
namespace BM\BmPdf2content\Domain\Model;

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
 * @package BM_Pdf2Content
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
     * @deprectaed
     */
    protected $pubDate;


    /**
     * txBmpdf2contentIsImport
     *
     * @var \integer
     */
    protected $txBmpdf2contentIsImport;


    /**
     * txBmpdf2contentIsImportSub
     *
     * @var \integer
     */
    protected $txBmpdf2contentIsImportSub;


    /**
     * Returns the pubDate
     *
     * @return integer
     * @deprectaed
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
     * @deprectaed
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
     *
     * @param \string $txBmpdf2contentIsImport
     * @return \string txBmpdf2contentIsImport
     */
    public function setTxBmpdf2contentIsImport($txBmpdf2contentIsImport) {
        $this->txBmpdf2contentIsImport = $txBmpdf2contentIsImport;
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
     *
     * @param \string $txBmpdf2contentIsImportSub
     * @return \string txBmpdf2contentIsImportSub
     */
    public function setTxBmpdf2contentIsImportSub($txBmpdf2contentIsImportSub) {
        $this->txBmpdf2contentIsImportSub = $txBmpdf2contentIsImportSub;
    }

}
