<?php
/**
 * SyncOrders.php
 *
 * @copyright Copyright © 2021 Onecode  All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Onecode\ShopFlixConnector\Helper\ImportOrders;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncOrders extends Command
{


    const NAME = 'Sync SHOPFLIX Order';
    private $state;

    public function __construct(State $state, string $name = null)
    {
        $this->state = $state;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('onecode:shopflix:sync:orders');
        $this->setDescription('Syncing Order From ShopFlix');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(Area::AREA_GLOBAL);
        $importOrders = ObjectManager::getInstance()->create(ImportOrders::class);
        $importOrders->import();
    }
}
