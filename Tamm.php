<?php

class Tamm
{
    protected $api_url = '';

    public function __construct()
    {
        $this->api_url = trim(settings('tamm_api_url'));
    }

    /**
     * @param $id_no
     * @param $mobile_no
     * @return array
     */
    public function send_otp($id_no, $mobile_no)
    {
        try {
            $obj = new SoapClient($this->api_url, ['trace' => true, 'cache_wsdl' => WSDL_CACHE_MEMORY]);
            $xml = simplexml_load_string('
            <sendOTP>
                 <nationalId>' . $id_no . '</nationalId>
                 <mobileNo>' . $mobile_no . '</mobileNo>
            </sendOTP>
            ');
            $response = $obj->sendOTP($xml);
            if (isset($response->return->returnCode) && $response->return->returnCode == 0) {
                return [
                    'status' => true,
                    'message' => $response->return->returnMessage,
                    'data' => ['correlation_id' => $response->return->returnCorrelationId]
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $response->return->returnMessage,
                    'data' => []
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => ['trace' => $e->getTrace()]
            ];
        }
    }

    /**
     * @param $id_number
     * @param $id_copy
     * @param $mobile_number
     * @param $plate_number
     * @param $plate_text
     * @param $correlation_id
     * @param $otp_code
     * @return array
     */
    public function issue_authorization($id_number, $id_copy, $mobile_number, $plate_number, $plate_text, $correlation_id, $otp_code)
    {
        try{
            $obj = new SoapClient($this->api_url, ['trace' => true, 'cache_wsdl' => WSDL_CACHE_MEMORY]);
            $xml = simplexml_load_string('
            <issueAuthorization>
                 <authorizedId>'.$id_number.'</authorizedId>
                 <authorizedIdVersion>'.$id_copy.'</authorizedIdVersion>
                 <authorizedMobileNo>'.$mobile_number.'</authorizedMobileNo>
                 <plateNumber>'.$plate_number.'</plateNumber>
                 <plateText>'.$plate_text.'</plateText>
                 <correlationId>'.$correlation_id.'</correlationId>
                 <otpAuthenticationCode>'.$otp_code.'</otpAuthenticationCode>
            </issueAuthorization>
            ');
            $response = $obj->issueAuthorization($xml);
            if (isset($response->return->returnCode) && $response->return->returnCode == 0) {
                return [
                    'status' => true,
                    'message' => $response->return->returnMessage,
                    'data' => ['authorization_number' => $response->return->returnAuthorizationNumber]
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $response->return->returnMessage,
                    'data' => []
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => ['trace' => $e->getTrace()]
            ];
        }
    }

    /**
     * @param $plate_number
     * @param $plate_text
     * @param $tamm_authorization_number
     * @return array
     */
    public function cancel_authorization($plate_number, $plate_text, $tamm_authorization_number)
    {
        try{
            $obj = new SoapClient($this->api_url, ['trace' => true, 'cache_wsdl' => WSDL_CACHE_MEMORY]);
            $xml = simplexml_load_string('
            <cancelAuthorization>
                 <plateNumber>'.$plate_number.'</plateNumber>
                 <plateText>'.$plate_text.'</plateText>
                 <authNo>'.$tamm_authorization_number.'</authNo>
            </cancelAuthorization>
            ');
            $response = $obj->cancelAuthorization($xml);
            if (strtolower($response) == 'success') {
                return [
                    'status' => true,
                    'message' => "Tamm cancelled successfully!",
                    'data' => []
                ];
            } else {
                return [
                    'status' => false,
                    'message' => "Something went wrong while cancelling Tamm.",
                    'data' => []
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => ['trace' => $e->getTrace()]
            ];
        }
    }
}
