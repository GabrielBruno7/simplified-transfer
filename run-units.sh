echo "Rodando os testes unitários no container..."

docker exec -it picpay-simplificado-app php artisan test

echo "Testes finalizados!"
