<?php
/**
 * SyncReturnOrders.php
 *
 * @copyright Copyright © 2022 Onecode P.C All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Onecode\ShopFlixConnector\Helper\ImportReturnOrders;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncReturnOrders extends Command
{


    /**
     * @var State
     */
    private $state;

    public function __construct(State $state, string $name = null)
    {
        $this->state = $state;
        parent::__construct($name);
    }
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('onecode:shopflix:sync:return_orders');
        $this->setDescription('Syncing Return Orders From SHOPFLIX');
        parent::configure();
    }

    /**
     * CLI command description
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->state->setAreaCode(Area::AREA_GLOBAL);
        $importOrders = ObjectManager::getInstance()->create(ImportReturnOrders::class);
        $importOrders->import();
    }
}
