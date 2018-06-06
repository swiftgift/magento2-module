<?php
namespace Swiftgift\Gift\Api\Data;

interface ResultInterface {

    /**
     * @return bool
     */
    public function getSuccess();

    /**
     * @return \Magento\Quote\Api\Data\PaymentMethodInterface[]
     */ 
    public function getPaymentMethods();

    /**
     * @param \Magento\Quote\Api\Data\PaymentMethodInterface[] $paymentMethods
     * @return $this
     */
    public function setPaymentMethods($paymentMethods);


    /**
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getTotals();

    /**
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return $this
     */
    public function setTotals($totals);

    /**
     @params string[] $errorsMessages
     @return $this
     */
    public function setErrorsMessages($errorsMessages);


    /**
      @return string[]
     */
    public function getErrorsMessages();
    
    
}