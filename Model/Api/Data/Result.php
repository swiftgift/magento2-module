<?php
namespace Swiftgift\Gift\Model\Api\Data;

class Result implements \Swiftgift\Gift\Api\Data\ResultInterface {
    
    protected $success = FALSE;
    protected $errorsMessages;
    protected $paymentMethods;
    protected $totals;

    public function setSuccess(bool $value) {
        $this->success = $value;
    }

    public function getSuccess() {
        return $this->success;
    }

    /**
     * @param \Magento\Quote\Api\Data\PaymentMethodInterface[] $paymentMethods
     * @return $this
     */    
    public function setPaymentMethods($paymentMethods) {
        $this->paymentMethods = $paymentMethods;
        return $this;        
    }

    /**
     * @return \Magento\Quote\Api\Data\PaymentMethodInterface[]
     */
    public function getPaymentMethods() {
        return $this->paymentMethods;
    }
    
    /**
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return $this
     */
    public function setTotals($totals) {
        $this->totals = $totals;
        return $this;
    }

    /**
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */    
    public function getTotals() {
        return $this->totals;
    }

    /**
     @params string[] $errorsMessages
     @return $this
     */
    public function setErrorsMessages($errorsMessages) {
        $this->errorsMessages = $errorsMessages;
        return $this;
    }


    /**
      @return string[]
     */
    public function getErrorsMessages() {
        return $this->errorsMessages;
    }
    
    
}