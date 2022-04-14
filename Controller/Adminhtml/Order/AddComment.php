<?php
/**
 * AddComment.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;


use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Onecode\ShopFlixConnector\Api\ManagementInterface;
use Onecode\ShopFlixConnector\Api\OrderRepositoryInterface;
use Onecode\ShopFlixConnector\Api\StatusHistoryRepositoryInterface;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order as OrderAction;
use Psr\Log\LoggerInterface;


class AddComment extends OrderAction implements HttpPostActionInterface
{


    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::comment';
    private $orderStatusHistoryRepository;

    public function __construct(
        Action\Context                   $context,
        Registry                         $coreRegistry,
        FileFactory                      $fileFactory,
        InlineInterface                  $translateInline,
        PageFactory                      $resultPageFactory,
        JsonFactory                      $resultJsonFactory,
        LayoutFactory                    $resultLayoutFactory,
        RawFactory                       $resultRawFactory,
        OrderRepositoryInterface         $orderRepository,
        ManagementInterface              $orderManagement,
        StatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        LoggerInterface                  $logger
    )
    {
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderRepository,
            $orderManagement,
            $logger
        );
    }

    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $data = $this->getRequest()->getPost('history');
                if (empty($data['comment']) && $data['status'] == $order->getDataByKey('status')) {
                    throw new LocalizedException(
                        __('The comment is missing. Enter and try again.')
                    );
                }
                $order->setStatus($data['status']);
                $history = $order->addStatusHistoryComment($data['comment'], $data['status']);
                $this->orderStatusHistoryRepository->save($history);

                return $this->resultPageFactory->create();
            } catch (LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot add order history.')];
            }
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
        return $this->resultRedirectFactory->create()->setPath('shopflix/*/');
    }
}
