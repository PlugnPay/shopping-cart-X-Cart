<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\PlugnPay\Payment\Model\Payment\Processor;

/**
 * PlugnPay SSv2 processor
 *
 * Find the latest document here:
 * http://www.plugnpay.com/
 */
 
class SS2Payment extends \XLite\Model\Payment\Base\WebBased
{
    public function getSettingsWidget()
    {
        return 'modules/PlugnPay/Payment/config.twig';
    }

    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
            && $method->getSetting('gateway_account');
    }

    protected function getFormURL()
    {
        return 'https://pay1.plugnpay.com/pay/';
    }

    public function getWebhookURL()
    {
        return $this->getReturnURL('pt_order_classifier');
    }

    protected function getFormFields()
    {
        $currency = $this->transaction->getCurrency();

        $bName = $this->getProfile()->getBillingAddress()->getFirstname()
                  . ' '
                  . $this->getProfile()->getBillingAddress()->getLastname();

        $bState = $this->getProfile()->getBillingAddress()->getState()->getCode()
                   ? $this->getProfile()->getBillingAddress()->getState()->getCode()
                   : 'ZZ';

        $sName => $shippingAddress->getFirstname()
                   . ' '
                   . $shippingAddress->getLastname();

        $sState => $shippingAddress->getState()->getCode()
                    ? $shippingAddress->getState()->getCode()
                    : 'ZZ';

        $fields = [
            'pt_gateway_account'       => $this->getSetting('gateway_account'),
            'pd_display_items'         => 'no',
            'pt_transaction_amount'    => round($this->transaction->getValue(), 2),
            'pt_currency'              => $currency->getCode(),
            'pt_client_identifier'     => 'X-Cart_SS2',
            'pt_account_code_1'        => $this->getProfile()->getLogin(),
            'pt_payment_name'          => $bName,
            'pt_billing_address_1'     => $this->getProfile()->getBillingAddress()->getStreet(),
            'pt_billing_city'          => $this->getProfile()->getBillingAddress()->getCity(),
            'pt_billing_state'         => $bState,
            'pt_billing_postal_code'   => $this->getProfile()->getBillingAddress()->getZipcode(),
            'pt_billing_country'       => $this->getProfile()->getBillingAddress()->getCountry()->getCountry(),
            'pt_billing_phone_number'  => $this->getProfile()->getBillingAddress()->getPhone(),
            'pt_billing_email_address' => $this->getProfile()->getLogin(),
            'pt_order_classifier'      => $this->transaction->getPublicTxnId(),
            'pb_transition_template'   => 'hidden',
            'pb_success_url'           => $this->getReturnURL('pt_order_classifier','true'),
            'pt_ip_address'            => $this->getClientIP(),
        ];

        $shippingAddress = $this->getProfile()->getShippingAddress();
        if ($shippingAddress) {

            $fields += [
                'pd_collect_shipping_information' => 'no',
                'pt_shipping_name'        => $sName,
                'pt_shipping_address_1'   => $shippingAddress->getStreet(),
                'pt_shipping_city'        => $shippingAddress->getCity(),
                'pt_shipping_state'       => $sState,
                'pt_shipping_postal_code' => $shippingAddress->getZipcode(),
                'pt_shipping_country'     => $shippingAddress->getCountry()->getCountry(),
            ];
        }

        return $fields;
    }

    protected function getStatusBasedOnFinalStatus($pi_response_status)
    {
        $status = null;

        switch ($pi_response_status) {
            case 'success':
                $status = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;
                break;

            case 'badcard':
            case 'problem':
            case 'fraud':
                $status = \XLite\Model\Payment\Transaction::STATUS_FAILED;
                break;

            case 'pending':
                $status = \XLite\Model\Payment\Transaction::STATUS_PENDING;
                break;

            default:
                $status = \XLite\Model\Payment\Transaction::STATUS_INITIALIZED;
                break;
        }

        return $status;
    }


    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

        $request = \XLite\Core\Request::getInstance();

        $status = $this->getStatusBasedOnXResponseCode($request->pi_response_status);

        $this->setDetail('Status', $request->status, 'Result');
        $this->setDetail('TxnNum', $request->transactionID, 'Transaction number');

        if (isset($request->pi_error_message)) {
            $this->setDetail('response', $request->pi_error_message, 'Response');
            $this->transaction->setNote($request->pi_error_message);
        }

        if (isset($this->err[$request->pi_response_code])) {
            $this->setDetail('response', $this->err[$request->pi_response_code], 'Response');
            $this->transaction->setNote($this->err[$request->pi_response_code]);
        }

        if ($request->pt_authorization_code) {
            $this->setDetail('authCode', $request->pt_authorization_code, 'Auth code');
        }

        if ($request->pt_order_id) {
            $this->setDetail('transId', $request->pt_order_id, 'Transaction ID');
        }

        if ($request->pt_ip_address) {
            $this->setDetail('ipAddress', $request->pt_ip_address, 'IP Address');
        }

        if ($request->pi_response_status) {
            $this->setDetail('finalstatus', $request->pi_response_status, 'Gateway Response');
        }

        if (!$this->checkTotal($request->pt_transaction_amount)) {
            $status = $transaction::STATUS_FAILED;
        }

        $this->transaction->setStatus($status);
    }
}

