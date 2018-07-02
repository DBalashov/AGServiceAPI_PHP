#!/usr/bin/php
<?php
class AutoGRAF
    {
    private $token = '';
    private $base_url = 'http://m.tk-chel.ru/ServiceJSON/';

    public function __construct($login, $pass)
        {
        $this->token = $this->Request('Login', array('UserName' => $login, 'password'=> $pass));
        }

    public function Request($service, $data = array())
        {
        try 
            {
            $curl_sets = array(
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'AG-TOKEN: '.$this->token),
                CURLOPT_POSTFIELDS => json_encode($data));

            $curl_id = curl_init($this->base_url.$service);
            curl_setopt_array($curl_id, $curl_sets);
            $data = curl_exec($curl_id);
            $err_no = curl_errno($curl_id);
            if ($err_no) // ���� �����-�� ������ ��� ���������� curl-�������
                throw new RuntimeException(curl_error($curl_id), $err_no);
            $http_code = curl_getinfo($curl_id, CURLINFO_HTTP_CODE);
            if ($http_code != 200) // ���� ������ ������ ������ �� ��/200, � ���-�� ������
                throw new RuntimeException('HTTP Status Code = '.$http_code, $http_code);
            curl_close($curl_id);
            return $data;
            }
        catch (Exception $err) {error_log($err->getMessage()."\n");} // ����� ��������� �� ������ � ������� error.log
#        catch (Exception $err) {echo $err->getMessage()."\n";} // ����� ��������� �� ������ � ������� �����
        }
    }

$test = new AutoGRAF('demo','demo');

$shemas = $test->Request('EnumSchemas');
echo $shemas."\n\n";
$schemas_decoded = json_decode($shemas,TRUE);

$first_schemaID = $schemas_decoded[0]['ID']; // ����� ID ������ ����� �� ����������� ������

$devices = $test->Request('EnumDevices',array('schemaID' => $first_schemaID));
echo $devices."\n\n";
$devices_decoded = json_decode($devices,TRUE);

$info = $test->Request('GetDevicesInfo',array('schemaID' => $first_schemaID));
echo $info."\n\n";
$info_decoded = json_decode($info,TRUE);

$two_items = array($devices_decoded['Items'][0]['ID'],$devices_decoded['Items'][1]['ID']); //// ����� ID ������ ���� ������ �� ������ ������, ���������� �� � ������
$IDs = implode(',',$two_items); // ������ �� ������� ID ������ ������ � ����������� �������� 

$online = $test->Request('GetOnlineInfo',array('schemaID' => $first_schemaID, 'IDs' => $IDs));
echo $online."\n\n";
$online_decoded = json_decode($online,TRUE);

?>
