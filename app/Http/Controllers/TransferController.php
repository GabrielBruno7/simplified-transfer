<?php

namespace App\Http\Controllers;

use Domain\User\User;
use Infra\Database\UserDb;
use Illuminate\Http\Request;
use Domain\Transfer\Transfer;
use Infra\Database\TransferDb;
use Infra\Mail\LaravelEmailSender;
use Domain\Transfer\AuthorizerFactory;

class TransferController extends Controller
{
    public function actionTransfer(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'valor' => 'required|numeric|min:0.01',
                'pagador' => 'required|string|max:14',
                'recebedor' => 'required|string|max:14',
            ]);

            $authorizer = AuthorizerFactory::make();

            $payer = (new User(new UserDb()))
                ->setDocument($validatedData['pagador'])
                ->loadByDocument()
            ;

            $payee = (new User(new UserDb()))
                ->setDocument($validatedData['recebedor'])
                ->loadByDocument()
            ;

            $transfer = (new Transfer(new TransferDb()))
                ->setPayer($payer)
                ->setPayee($payee)
                ->setAuthorizer($authorizer)
                ->setValue($validatedData['valor'])
                ->setEmailSender((new LaravelEmailSender()))
                ->execute()
            ;

            return response()->json(['id' => $transfer->getId()], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Internal Server Error'], 500); //TODO: CUSTOM ERROR
        }
    }
}
