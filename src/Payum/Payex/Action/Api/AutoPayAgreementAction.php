<?php
namespace Payum\Payex\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Payex\Api\AgreementApi;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Payex\Request\Api\AutoPayAgreementRequest;
use Payum\Payex\Request\Api\CreateAgreementRequest;

class AutoPayAgreementAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var AgreementApi
     */
    protected $api;
    
    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false == $api instanceof AgreementApi) {
            throw new UnsupportedApiException('Expected api must be instance of AgreementApi.');
        }
        
        $this->api = $api;
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request AutoPayAgreementRequest */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $model->validatedKeysSet(array(
            'agreementRef',
            'price',
            'productNumber',
            'description',
            'orderId',
            'purchaseOperation',
            'currency'
        ));

        $result = $this->api->autoPay((array) $model);

        $model->replace($result);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return 
            $request instanceof AutoPayAgreementRequest &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}