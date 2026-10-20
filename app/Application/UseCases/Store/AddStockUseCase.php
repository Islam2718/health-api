<?php
namespace App\Application\UseCases\Store;
use App\Application\DTOs\Store\StockDTO;
use App\Domain\Interfaces\StockRepositoryInterface;
use App\Domain\Interfaces\StoreProductRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AddStockUseCase
{
    public function __construct(
        private StockRepositoryInterface $stockRepository,
        private StoreProductRepositoryInterface $storeProductRepository
    ) {}

    public function execute(StockDTO $dto): StockDTO
    {
        // Check if store product exists
        $storeProduct = $this->storeProductRepository->findById($dto->store_product_id);
        if (!$storeProduct) {
            throw ValidationException::withMessages([
                'store_product_id' => 'Store product not found.'
            ]);
        }

        if ($dto->transaction_type === 'sale' && $storeProduct->current_stock < $dto->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock for this sale.'
            ]);
        }

        // Calculate total price
        $totalPrice = $dto->quantity * $dto->unit_price;
        
        // Create stock entry
        $stockData = $dto->toArray();
        $stockData['total_price'] = $totalPrice;
        $stockData['transaction_date'] = $dto->transaction_date ?? now()->toDateString();

        $stock = $this->stockRepository->create($stockData);
        
        return new StockDTO(
            id: $stock->id,
            store_product_id: $stock->store_product_id,
            quantity: $stock->quantity,
            transaction_type: $stock->transaction_type,
            unit_price: $stock->unit_price,
            total_price: $stock->total_price,
            remarks: $stock->remarks,
            transaction_date: $stock->transaction_date,
            created_at: $stock->created_at?->toISOString(),
            updated_at: $stock->updated_at?->toISOString()
        );
    }
}