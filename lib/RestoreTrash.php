<?php

class RestoreTrash
{
    private $uri;
    private $username;
    private $password;
    private $sabreService;
    private $restoreDate;
    private $trashbinData;

    public function __construct($uri, $username, $password, $restoreDate)
    {
        $this->trashbinData = [];
        $this->sabreService = new Sabre\Xml\Service();
        $this->uri = $uri;
        $this->username = $username;
        $this->password = $password;
        $this->restoreDate = new DateTime($restoreDate);
    }

    public function run()
    {
        echo("Collection files to restore\n");
        $this->collectTrashbinData();
        echo(sprintf("Found %s files to restore \n", count($this->trashbinData)));

        $this->restoreTrashbinData();
    }

    private function collectTrashbinData()
    {
        $ch = curl_init();

        $curlOptions = [
            CURLOPT_FAILONERROR => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->uri . "/remote.php/dav/trash-bin/" . $this->username,
            CURLOPT_USERPWD => "$this->username:$this->password",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => "PROPFIND",
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/xml',
                'Connection: Keep-Alive',
                'charset=UTF-8',
                'Depth: 1',
            ],
            CURLOPT_POSTFIELDS => '<?xml version="1.0"?>
                                                            <d:propfind xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns">
                                                                <d:prop>
                                                                    <oc:trashbin-original-filename />
                                                                    <oc:trashbin-original-location />
                                                                    <oc:trashbin-delete-datetime />
                                                                    <d:getcontentlength />
                                                                    <d:resourcetype />
                                                                </d:prop>
                                                            </d:propfind>'
            ];



        curl_setopt_array($ch, $curlOptions);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo(sprintf("ERROR: %s\n",curl_error($ch)));
        }

        curl_close($ch);

        $data = $this->sabreService->parse($response);
        array_shift($data);

        foreach ($data as $value) {
            $remoteUrl = $value['value'][0]['value'];

            $trashbinOriginalFilename = $value['value'][1]['value'][0]['value'][0]['value'];
            $trashbinOriginalLocation = $value['value'][1]['value'][0]['value'][1]['value'];
            $trashbinDeleteDateTime = new DateTime($value['value'][1]['value'][0]['value'][2]['value']);

            //Only observe data which has been deleted after certain date
            if ($trashbinDeleteDateTime < $this->restoreDate) {
                continue;
            }

            $this->trashbinData[] = [
                'remoteUrl' => $remoteUrl,
                'trashbinOriginalLocation' => $trashbinOriginalLocation,
                "trashbinOriginalFilename" => $trashbinOriginalFilename,
            ];
        }
    }

    private function restoreTrashbinData()
    {
        foreach ($this->trashbinData as $trashbinRecord) {
            $ch = curl_init();

            $curlOptions = [
                CURLOPT_FAILONERROR => 1,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_URL => $this->uri . $trashbinRecord['remoteUrl'],
                CURLOPT_USERPWD => "$this->username:$this->password",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => "MOVE",
                CURLOPT_HTTPHEADER => [
                    'Overwrite: F',
                    'Destination: ' . $this->uri . '/remote.php/dav/files/' . $this->username . '/' . $trashbinRecord['trashbinOriginalLocation'],
                ]];

            curl_setopt_array($ch, $curlOptions);
            curl_exec($ch);

            if (curl_errno($ch)) {
                echo(sprintf("ERROR: %s\n",curl_error($ch)));
            }

            curl_close($ch);

            echo(sprintf("File %s restored\n", $trashbinRecord['trashbinOriginalFilename']));
        }
    }

}