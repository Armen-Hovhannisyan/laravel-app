<?php


namespace App\Http\Controllers;

use App\Http\Requests\UploadExcelRequest;
use App\Models\UserData;
use App\Services\ExcelImportService;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;


class UserDataController extends Controller
{


    /**
     * @param UserData $userData
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(UserData $userData)
    {
        $data = $userData->getData();
        return view('index',['userData' => $data]);
    }

    /**
     * @param UploadExcelRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fileUpload(UploadExcelRequest $request)
    {
        $file = $request->file('file');
        try {
            Excel::queueImport(new ExcelImportService, $file);
        }catch (ValidationException $e) {
            return back()->with('importError', $e->validator->errors()->first());

        } catch (\Exception $e) {
            return back()->with('importError', 'The given data was invalid');
        }
        return back()->with('success','File has been uploaded.');
    }
}
