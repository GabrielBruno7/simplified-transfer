<?php

namespace App\Http\Controllers;

use Domain\User\User;
use Infra\Log\LogService;
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
                'valor' => 'required|numeric',
                'pagador' => 'required|string',
                'recebedor' => 'required|string',
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
                ->setEmailSender(new LaravelEmailSender())
                ->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'))
                ->execute()
            ;

            return response()->json(['id' => $transfer->getId()], 200);
        } catch (\Throwable $e) {
            $logService = new LogService();
            $response = $logService->handle($e);

            return response()->json(
                $response['body'],
                $response['status']
            );
        }
    }

    public function actionAddBalance(Request $request, string $document)
    {
        try {
            $validatedData = $request->validate([
                'valor' => 'required|numeric',
            ]);

            $user = (new User(new UserDb()))
                ->setDocument($document)
                ->loadByDocument()
                ->getWallet()
                ->addBalance($validatedData['valor'])
                ->updateBalance()
            ;

            return response()->json([
                'Carteira' => $user->getId(),
                'Saldo Inserido' => "R$ {$validatedData['valor']}",
            ], 200);
        } catch (\Throwable $e) {
            $logService = new LogService();
            $response = $logService->handle($e);

            return response()->json(
                $response['body'],
                $response['status']
            );
        }
    }

    public function actionStatements(string $document)
    {
        try {
            $user = (new User(new UserDb()))
                ->setDocument($document)
                ->loadByDocument()
            ;

            $statements = $user
                ->getWallet()
                ->loadStatements()
                ->getStatements()
            ;

            $result = [];

            foreach ($statements as $statement) {
                $result[] = [
                    'valor' => $statement->amount,
                    'para' => $statement->to_name,
                    'data' => $statement->created_at
                ];
            }

            return response()->json(['Extrato' => $result], 200);
        } catch (\Throwable $e) {
            $logService = new LogService();
            $response = $logService->handle($e);

            return response()->json(
                $response['body'],
                $response['status']
            );
        }
    }
}
