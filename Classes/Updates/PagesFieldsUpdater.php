<?php
declare(strict_types=1);

namespace RKW\RkwPdf2content\Updates;

/**
 * This file is part of the "RkwPdf2content" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Migrates "tx_bmpdf2content_*" fields to "tx_rkwpdf2content_"
 */
class PagesFieldsUpdater extends AbstractUpdate
{
    const TABLE = 'pages';


    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Updates "tx_bmpdf2content_*" fields to "tx_rkwpdf2content_"';
    }

    /**
     * Get description
     *
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'Fills new fields "tx_rkwpdf2content_" of pages table records with old "tx_bmpdf2content_*" data.';
    }

    /**
     * Checks if an update is needed
     *
     * @param string &$description The description for the update
     * @return bool Whether an update is needed (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        if ($this->isWizardDone()) {
            return false;
            //===
        }
        return true;
        //===
    }

    /**
     * Performs the database update
     *
     * @param array &$databaseQueries Queries done in this update
     * @param string &$customMessage Custom message
     * @return bool
     */
    public function performUpdate(array &$databaseQueries, &$customMessage)
    {
        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('pages');

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder->select('uid', 'tx_bmpdf2content_is_import', 'tx_bmpdf2content_is_import_sub')
            ->from('pages')
            ->execute();

        while ($record = $statement->fetch()) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder->update('pages')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                    )
                )
                ->set('tx_rkwpdf2content_is_import', $record['tx_bmpdf2content_is_import'])
                ->set('tx_rkwpdf2content_is_import_sub', $record['tx_bmpdf2content_is_import_sub']);
            $databaseQueries[] = $queryBuilder->getSQL();
            $queryBuilder->execute();
        }

        $this->markWizardAsDone();
        return true;
        //===
    }
}
