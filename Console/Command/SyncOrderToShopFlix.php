<?php
/**
 * SyncOrderToShopFlix.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Console\Command;

use Magento\Framework\App\ObjectManager;
use Onecode\ShopFlixConnector\Helper\ExportOrders;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncOrderToShopFlix extends Command
{
    const NAME = 'Sync Order Back To ShopFlix';

    protected function configure()
    {
        $this->setName('onecode:shopflix:sync:orders:back');
        $this->setDescription('Syncing Order To ShopFlix');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exportOrders = ObjectManager::getInstance()->create(ExportOrders::class);
        $exportOrders->export();
    }
}
