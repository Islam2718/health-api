<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatDate;

class StockResource extends JsonResource
{
    use FormatDate;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'store_product_id' => $this->store_product_id,
            'quantity' => $this->quantity,
            'transaction_type' => $this->transaction_type,
            'unit_price' => number_format($this->unit_price, 2),
            'total_price' => number_format($this->total_price, 2),
            'remarks' => $this->remarks,
            'transaction_date' => $this->formatDate($this->transaction_date),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}