<?php

namespace App\Imports;

use App\Models\items;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception;

class ItemsImport implements ToModel, WithHeadingRow
{
    protected $cat_id;
    public $errors = []; // To store errors
    public $skippedItems = []; // To store skipped items due to duplication

    public function __construct($cat_id)
    {
        $this->cat_id = $cat_id;
    }

    public function model(array $row)
    {
        try {
            // Check if the 'name' key exists in the row array
            if (!array_key_exists('name', $row)) {
                throw new Exception("The 'name' column is missing or misnamed.");
            }

            // Check if the item already exists
            $existingItem = items::where('name', $row['name'])
                                ->where('cat_id', $this->cat_id)
                                ->first();

            if ($existingItem) {
                // Store the name of the existing item
                $this->skippedItems[] = $row['name'];
                return null; // Skip the existing item
            }

            return new items([
                'name' => $row['name'],
                'cat_id' => $this->cat_id,
            ]);
        } catch (Exception $e) {
            // Store the error message with row details
            $this->errors[] = $e->getMessage() . ' in row: ' . json_encode($row);
            return null; // Skip this row
        }
    }
}
