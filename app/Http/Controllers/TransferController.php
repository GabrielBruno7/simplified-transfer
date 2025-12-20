<?php

namespace App\Http\Controllers;

use Domain\User\User;
use Domain\ErrorCodes;
use Infra\Log\LogService;
use Domain\UserException;
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
        } catch (UserException $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => ErrorCodes::translate($e),
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro interno no servidor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
