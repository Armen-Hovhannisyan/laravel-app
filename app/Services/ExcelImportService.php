<?php


namespace App\Services;

use App\Models\UserData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Validator;

class ExcelImportService implements ToModel, WithBatchInserts, WithChunkReading, ShouldQueue
{
    public function model(array $row)
    {
        $dateTime = strtotime($row[2]);
        $data = [
                'id'   => $row[0],
                'name' => $row[1],
                'date' => date('d-m-Y', $dateTime),
            ];
        $validator = $this->validate($data);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $data['date'] = date('Y-m-d H:i:s', $dateTime);
        return new UserData($data);
    }
    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validate ($data)
    {
        $rules = [
            'id' => 'required|unique:user_dates,id',
            'name' => 'required|min:3',
            'date' => 'required|date_format:d-m-Y',
        ];

        $messages =  [
            'id.unique'=> 'id should be unique', // custom message
            'id.required'=> 'id is Required', // custom message
            'name.required'=> 'Name is Required', // custom message
            'name.min'=> 'The name must be at least 3 letters long.', // custom message
            'date.required'=> 'date is Required', // custom message
            'date.date_format'=> 'invalid date format', // custom message
        ];
        return Validator::make($data, $rules, $messages);
    }

}
