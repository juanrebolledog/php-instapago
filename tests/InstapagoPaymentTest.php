<?php

/**
 * The MIT License (MIT)
 * Copyright (c) 2016 Angel Cruz <me@abr4xas.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the “Software”), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Angel Cruz <me@abr4xas.org>
 * @package php-instapago
 * @license MIT License
 * @copyright 2016 Angel Cruz
 */


use \PHPUnit_Framework_TestCase as Test;
use \Instapago\InstapagoGateway\InstapagoPayment;
use \Instapago\InstapagoGateway\Exceptions\InstapagoException;

/**
 * 
 */
class InstapagoPaymentTest extends Test
{
    protected $api;

    protected function setUp() {
        $this->api = new InstapagoPayment('74D4A278-C3F8-4D7A-9894-FA0571D7E023','e9a5893e047b645fed12c82db877e05a');
    }

    public function testObjectInstantiationWithIncorrectAuthThrowsInstapagoException()
    {
        $this->expectException(InstapagoException::class);
        new InstapagoPayment('', '');
    }

    public function testCreaPago()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');

        $this->assertEquals(201, $pago['code']);
    }

    public function testPaymentWithFailureResponseReturnsErrorString()
    {
        $pago = $this->api->payment('','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');

        $this->assertTrue(is_string($pago));
    }

    public function testContinuePaymentWithCorrectParamsReturnsArray()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $continue = $this->api->continuePayment('200', $pago['id_pago']);

        $this->assertTrue(is_array($continue));
    }

    public function testContinuePaymentWithCorrectParamsReturnsArrayWithVoucherKey()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $continue = $this->api->continuePayment('200', $pago['id_pago']);

        $this->assertTrue(isset($continue['voucher']));
    }

    public function testContinuePaymentWithCorrectParamsReturnsArrayWithVoucherNotEmpty()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $continue = $this->api->continuePayment('200', $pago['id_pago']);

        $this->assertTrue(!empty($continue['voucher']));
    }

    public function testContinuePaymentWithIncorrectParamsReturnsErrorString()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $continue = $this->api->continuePayment('', $pago['id_pago']);

        $this->assertTrue(is_string($continue));
    }

    public function testContinuePaymentWithWithoutIdReturnsErrorString()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $continue = $this->api->continuePayment('500', null);

        $this->assertTrue(is_string($continue));
    }

    public function testContinuePaymentWithWithoutAmountReturnsErrorString()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $continue = $this->api->continuePayment(null, $pago['id_pago']);

        $this->assertTrue(is_string($continue));
    }

    public function testCancelPaymentWithCorrectParamsReturnsArray()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');
        $cancel = $this->api->cancelPayment($pago['id_pago']);
        $this->assertTrue(is_array($cancel));
    }

    public function testCancelPaymentWithCorrectParamsReturnsArrayWithMessageKey()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');
        $cancel = $this->api->cancelPayment($pago['id_pago']);
        $this->assertTrue(isset($cancel['msg_banco']));
    }

    public function testCancelPaymentWithCorrectParamsReturnsArrayWith201Code()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');
        $cancel = $this->api->cancelPayment($pago['id_pago']);
        $this->assertEquals(201, $cancel['code']);
    }

    public function testCancelPaymentMissingIdReturnsErrorString()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');
        $cancel = $this->api->cancelPayment(null);
        $this->assertTrue(is_string($cancel));
    }

    public function testPaymentInfoOnStatus1PaymentReturnsArray()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $info = $this->api->paymentInfo($pago['id_pago']);

        $this->assertTrue(is_array($info));
    }

    public function testPaymentInfoOnStatus2PaymentReturnsArray()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');
        $info = $this->api->paymentInfo($pago['id_pago']);

        $this->assertTrue(is_array($info));
    }

    public function testPaymentInfoOnStatus1PaymentReturns201Code()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','1','127.0.0.1');
        $info = $this->api->paymentInfo($pago['id_pago']);

        $this->assertEquals(201, $info['code']);
    }

    public function testPaymentInfoOnStatus2PaymentReturns201Code()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');
        $info = $this->api->paymentInfo($pago['id_pago']);

        $this->assertEquals(201, $info['code']);
    }

    public function testPaymentInfoWithoutIdReturnsErrorString()
    {
        $pago = $this->api->payment('200','test','jon doe','11111111','4111111111111111','123','12/2016','2','127.0.0.1');
        $info = $this->api->paymentInfo(null);

        $this->assertTrue(is_string($info));
    }

    public function testCheckResponseCodeWith201CodeReturnsArray()
    {
        $curl_response = (object)[
            'code' => 201,
            'message' => 'Éxito',
            'voucher' => '<table></table>',
            'id' => 'af614bca-0e2b-4232-bc8c-dbedbdf73b48',
            'reference' => '501791'
        ];
        $response = $this->api->checkResponseCode($curl_response);

        $this->assertTrue(is_array($response));
    }

    public function testCheckResponseCodeWith400CodeThrowsException()
    {
        $this->expectException(InstapagoException::class);
        $curl_response = (object)[
            'code' => 400,
        ];
        $this->api->checkResponseCode($curl_response);
    }

    public function testCheckResponseCodeWith401CodeThrowsException()
    {
        $this->expectException(InstapagoException::class);
        $curl_response = (object)[
            'code' => 401,
        ];
        $this->api->checkResponseCode($curl_response);
    }

    public function testCheckResponseCodeWith403CodeThrowsException()
    {
        $this->expectException(InstapagoException::class);
        $curl_response = (object)[
            'code' => 400,
        ];
        $this->api->checkResponseCode($curl_response);
    }

    public function testCheckResponseCodeWith500CodeThrowsException()
    {
        $this->expectException(InstapagoException::class);
        $curl_response = (object)[
            'code' => 400,
        ];
        $this->api->checkResponseCode($curl_response);
    }

    public function testCheckResponseCodeWith503CodeThrowsException()
    {
        $this->expectException(InstapagoException::class);
        $curl_response = (object)[
            'code' => 400,
        ];
        $this->api->checkResponseCode($curl_response);
    }
}
