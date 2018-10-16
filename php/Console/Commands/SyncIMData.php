<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tencent\TLSSigAPI;
use App\Models\User;
use GuzzleHttp\Client;

class SyncIMData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncim';

    public $http;

    public $url;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync IM data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->http = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $UserSig = 'eJxNjc1Og0AURt*FtTF3-mgxcUEsxqalWksDrMjIDORqhcnMtKEa311C'.
        '2uj2nJzv*w6y9e5W1nV-7Hzlz0YHdwEENxNGpTuPDWo7QhGRcEbojF*kNAZVJX3FrPr'.
        'XOPVRTWpkhAMQykFEF6kHg1ZXsvHTJBFCUIBretLWYd*NggIRhDKAP*nxU08JE0AgDO'.
        'n1D9sRp8n*YfmU0fLRvcc83PTFmruY*xe-dTtT5ydMNqIoD*U*a9Io1TEmsV80*atYvJ'.
        '0PW-HMekfzfpUNq8HOWUGWrFXSfh1byRDn98HPLxDQV7w_';
        $IM = config('im');
        $adminUser = $IM['adminAccount'];
        $this->url = "https://console.tim.qq.com/v4/%s?sdkappid=".
        $IM['SdkAppId']."&identifier=".$IM['adminAccount'].
        "&usersig=".$UserSig."&random=".rand(100000, 999999)."&contenttype=json";
        $this->importUser();
        $this->addGroup();
    }

    public function importUser()
    {
        $baseUrl = 'im_open_login_svc/account_import';
        $url = sprintf($this->url, $baseUrl);
        $users = User::whereNotNull('nickname')->get();
        foreach ($users as $user) {
            $data = [
              'Identifier'=>(string)$user->id,
              'Nick'=>$user->nickname,
              'FaceUrl'=>$user->head,
            ];
            $rs = $this->http->post($url, ['body'=>json_encode($data)]);
            dump(json_decode($rs->getBody()->getContents(), true));
        }
    }
    public function addGroup()
    {
        $baseUrl = 'group_open_http_svc/create_group';
        $url = sprintf($this->url, $baseUrl);
        $users = User::whereNotNull('nickname')->get();
        foreach ($users as $user) {
            $data = [
            'GroupId'=>(string)$user->id,
            'Owner_Account'=>(string)$user->id,
            'Type'=>'Public',
            'ApplyJoinOption'=>'DisableApply',
            'Name'=>$user->nickname.'的家族',
          ];
            $rs = $this->http->post($url, ['body'=>json_encode($data)]);
            dump(json_decode($rs->getBody()->getContents(), true));
        }
    }
}
