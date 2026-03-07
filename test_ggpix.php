<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Traits\Gateways\GgpixTrait;

class TestGgpix
{
    use GgpixTrait;

    public function test()
    {
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'amount' => 20,
            'cpf' => '12312312312'
        ]);

        // Simular usuário logado
        $user = \App\Models\User::find(4571);
        auth('api')->setUser($user);

        try {
            $res = self::requestQrcodeGgpix($request);
            echo json_encode($res, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

(new TestGgpix())->test();
