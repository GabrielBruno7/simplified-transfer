echo "Subindo containers Docker..."
docker compose up -d

echo "Aguardando o banco de dados iniciar..."
sleep 5

echo "Limpando o banco..."
docker exec picpay-simplificado-app php artisan migrate:fresh --seed
echo "Banco recriado com sucesso!"