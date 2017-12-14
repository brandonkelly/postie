<?php
namespace FedEx\ShipService\ComplexType;

use FedEx\AbstractComplexType;

/**
 * DeleteTagRequest
 *
 * @author      Jeremy Dunn <jeremy@jsdunn.info>
 * @package     PHP FedEx API wrapper
 * @subpackage  Ship Service
 *
 * @property WebAuthenticationDetail $WebAuthenticationDetail
 * @property ClientDetail $ClientDetail
 * @property TransactionDetail $TransactionDetail
 * @property VersionId $Version
 * @property string $DispatchLocationId
 * @property string $DispatchDate
 * @property Payment $Payment
 * @property string $ConfirmationNumber

 */
class DeleteTagRequest extends AbstractComplexType
{
    /**
     * Name of this complex type
     *
     * @var string
     */
    protected $name = 'DeleteTagRequest';

    /**
     * Descriptive data to be used in authentication of the sender's identity (and right to use FedEx web services).
     *
     * @param WebAuthenticationDetail $webAuthenticationDetail
     * @return $this
     */
    public function setWebAuthenticationDetail(WebAuthenticationDetail $webAuthenticationDetail)
    {
        $this->values['WebAuthenticationDetail'] = $webAuthenticationDetail;
        return $this;
    }

    /**
     * Descriptive data identifying the client submitting the transaction.
     *
     * @param ClientDetail $clientDetail
     * @return $this
     */
    public function setClientDetail(ClientDetail $clientDetail)
    {
        $this->values['ClientDetail'] = $clientDetail;
        return $this;
    }

    /**
     * Descriptive data for this customer transaction. The TransactionDetail from the request is echoed back to the caller in the corresponding reply.
     *
     * @param TransactionDetail $transactionDetail
     * @return $this
     */
    public function setTransactionDetail(TransactionDetail $transactionDetail)
    {
        $this->values['TransactionDetail'] = $transactionDetail;
        return $this;
    }

    /**
     * Identifies the version/level of a service operation expected by a caller (in each request) and performed by the callee (in each reply).
     *
     * @param VersionId $version
     * @return $this
     */
    public function setVersion(VersionId $version)
    {
        $this->values['Version'] = $version;
        return $this;
    }

    /**
     * Only used for tags which had FedEx Express services.
     *
     * @param string $dispatchLocationId
     * @return $this
     */
    public function setDispatchLocationId($dispatchLocationId)
    {
        $this->values['DispatchLocationId'] = $dispatchLocationId;
        return $this;
    }

    /**
     * Only used for tags which had FedEx Express services.
     *
     * @param string $dispatchDate
     * @return $this
     */
    public function setDispatchDate($dispatchDate)
    {
        $this->values['DispatchDate'] = $dispatchDate;
        return $this;
    }

    /**
     * If the original ProcessTagRequest specified third-party payment, then the delete request must contain the same pay type and payor account number for security purposes.
     *
     * @param Payment $payment
     * @return $this
     */
    public function setPayment(Payment $payment)
    {
        $this->values['Payment'] = $payment;
        return $this;
    }

    /**
     * Also known as Pickup Confirmation Number or Dispatch Number
     *
     * @param string $confirmationNumber
     * @return $this
     */
    public function setConfirmationNumber($confirmationNumber)
    {
        $this->values['ConfirmationNumber'] = $confirmationNumber;
        return $this;
    }

    
}